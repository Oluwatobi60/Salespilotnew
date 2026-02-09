# Multi-Branch Inventory Distribution System
## Implementation Guide & Architecture

---

## 📋 **Overview**

This system allows a business creator to:
1. Add inventory items (Standard & Variant) centrally
2. Distribute/allocate stock to different branches
3. Track sales and inventory per branch
4. Maintain proper branch-level accountability

---

## 🏗️ **Architecture**

### **Database Schema**

#### **1. branch_inventory Table** (NEW)
Tracks inventory allocated to each branch:
```sql
- id
- branch_id (FK to branches)
- business_name
- item_id (polymorphic reference)
- item_type ('standard' or 'variant')
- allocated_quantity (total allocated)
- current_quantity (available now)
- sold_quantity (cumulative sales)
- low_stock_threshold
- allocated_by (user who distributed)
- allocated_at
- notes
```

#### **2. cart_items Table** (UPDATED)
Added branch tracking:
```sql
- branch_id (FK to branches)
- branch_name
- branch_manager_id (FK to users)
```

#### **3. sell_products Table** (UPDATED)
Added branch tracking:
```sql
- branch_id (FK to branches)
- branch_name
- branch_manager_id (FK to users)
```

---

## 🔄 **Workflow**

### **Phase 1: Central Inventory Creation**
```
Business Creator → Adds Items → Central Pool
- StandardItem (business_name, manager_name, manager_email)
- VariantItem (business_name, manager_name, manager_email)
- Items exist globally for the business
```

### **Phase 2: Branch Allocation**
```
Business Creator → Selects Items → Allocates to Branch(es)
- Choose item(s)
- Select target branch
- Set allocation quantity
- Creates BranchInventory record
```

### **Phase 3: Branch Operations**
```
Branch Manager/Staff → Sees Only Allocated Items → Makes Sales
- POS shows only items allocated to their branch
- Sales deduct from branch_inventory.current_quantity
- Transaction records include branch_id, branch_manager_id
```

---

## 💻 **Implementation Steps**

### **Step 1: Run Migrations**
```bash
php artisan migrate
```

### **Step 2: Update Models**

#### **BranchInventory Model** (Already created)
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchInventory extends Model
{
    protected $table = 'branch_inventory';
    
    protected $fillable = [
        'branch_id',
        'business_name',
        'item_id',
        'item_type',
        'allocated_quantity',
        'current_quantity',
        'sold_quantity',
        'low_stock_threshold',
        'allocated_by',
        'allocated_at',
        'notes',
    ];
    
    protected $casts = [
        'allocated_quantity' => 'decimal:2',
        'current_quantity' => 'decimal:2',
        'sold_quantity' => 'decimal:2',
        'low_stock_threshold' => 'decimal:2',
        'allocated_at' => 'datetime',
    ];
    
    // Relationships
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch\Branch::class);
    }
    
    public function allocatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'allocated_by');
    }
    
    public function item()
    {
        if ($this->item_type === 'standard') {
            return $this->belongsTo(\App\Models\StandardItem::class, 'item_id');
        } else {
            return $this->belongsTo(\App\Models\ProductVariant::class, 'item_id');
        }
    }
    
    // Check if stock is low
    public function isLowStock(): bool
    {
        return $this->low_stock_threshold 
            && $this->current_quantity <= $this->low_stock_threshold;
    }
    
    // Deduct stock (when sale is made)
    public function deductStock(float $quantity): bool
    {
        if ($this->current_quantity >= $quantity) {
            $this->current_quantity -= $quantity;
            $this->sold_quantity += $quantity;
            $this->save();
            return true;
        }
        return false;
    }
    
    // Add stock (when restocking/adjusting)
    public function addStock(float $quantity): void
    {
        $this->allocated_quantity += $quantity;
        $this->current_quantity += $quantity;
        $this->save();
    }
}
```

#### **Update CartItem Model**
Add to fillable:
```php
'branch_id',
'branch_name',
'branch_manager_id',
```

Add relationship:
```php
public function branch()
{
    return $this->belongsTo(\App\Models\Branch\Branch::class);
}
```

#### **Update Branch Model**
Add relationships:
```php
public function inventory()
{
    return $this->hasMany(\App\Models\BranchInventory::class);
}

public function cartItems()
{
    return $this->hasMany(\App\Models\CartItem::class);
}

public function sales()
{
    return $this->hasMany(\App\Models\SellProduct::class);
}
```

### **Step 3: Create Controllers**

#### **BranchInventoryController** (NEW)
```php
<?php
namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BranchInventory;
use App\Models\Branch\Branch;
use App\Models\StandardItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class BranchInventoryController extends Controller
{
    // Show allocation page
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isBusinessCreator()) {
            return redirect()->back()->with('error', 'Only business owner can manage inventory distribution.');
        }
        
        $branches = Branch::where('user_id', $user->id)->get();
        $standardItems = StandardItem::where('business_name', $user->business_name)->get();
        $variantItems = ProductVariant::whereHas('variantItem', function($q) use ($user) {
            $q->where('business_name', $user->business_name);
        })->get();
        
        $allocations = BranchInventory::where('business_name', $user->business_name)
            ->with(['branch', 'allocatedBy'])
            ->paginate(20);
        
        return view('manager.inventory.branch_allocation', compact('branches', 'standardItems', 'variantItems', 'allocations'));
    }
    
    // Allocate inventory to branch
    public function allocate(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isBusinessCreator()) {
            return redirect()->back()->with('error', 'Only business owner can allocate inventory.');
        }
        
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'item_id' => 'required|integer',
            'item_type' => 'required|in:standard,variant',
            'quantity' => 'required|numeric|min:0.01',
            'low_stock_threshold' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        // Check if allocation already exists
        $allocation = BranchInventory::updateOrCreate(
            [
                'branch_id' => $validated['branch_id'],
                'item_id' => $validated['item_id'],
                'item_type' => $validated['item_type'],
            ],
            [
                'business_name' => $user->business_name,
                'allocated_quantity' => \DB::raw('allocated_quantity + ' . $validated['quantity']),
                'current_quantity' => \DB::raw('current_quantity + ' . $validated['quantity']),
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? null,
                'allocated_by' => $user->id,
                'allocated_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]
        );
        
        ActivityLogger::log('allocate_inventory', "Allocated {$validated['quantity']} units to branch");
        
        return redirect()->back()->with('success', 'Inventory allocated successfully!');
    }
    
    // View branch-specific inventory
    public function branchInventory($branchId)
    {
        $user = Auth::user();
        $branch = Branch::where('user_id', $user->id)->findOrFail($branchId);
        
        $inventory = BranchInventory::where('branch_id', $branchId)
            ->with(['branch'])
            ->get();
        
        return view('manager.inventory.branch_stock', compact('branch', 'inventory'));
    }
}
```

### **Step 4: Update SellProductController**

Modify checkout method to:
1. Check branch inventory availability
2. Deduct from branch inventory
3. Record branch info in transactions

```php
// In checkout method, add:
$branchId = session('selected_branch_id'); // Or from Auth user's assigned branch
$branch = Branch::find($branchId);

foreach ($cartItems as $cartItem) {
    // Check branch inventory
    $branchInventory = BranchInventory::where([
        'branch_id' => $branchId,
        'item_id' => $cartItem->item_id,
        'item_type' => $cartItem->item_type,
    ])->first();
    
    if (!$branchInventory || $branchInventory->current_quantity < $cartItem->quantity) {
        return redirect()->back()->with('error', 'Insufficient stock in branch for ' . $cartItem->item_name);
    }
    
    // Deduct from branch inventory
    $branchInventory->deductStock($cartItem->quantity);
    
    // Add branch info to cart item
    $cartItem->update([
        'branch_id' => $branchId,
        'branch_name' => $branch->branch_name,
        'branch_manager_id' => $branch->manager_id,
    ]);
}
```

---

## 🎯 **Benefits**

✅ **Proper Inventory Control**: Each branch has its own stock allocation  
✅ **Accurate Reporting**: Know exactly what sold where  
✅ **Branch Accountability**: Track performance per branch  
✅ **Prevent Overselling**: Branches can only sell allocated stock  
✅ **Central Management**: Creator controls distribution  
✅ **Audit Trail**: Know who allocated what and when  

---

## 📊 **Reporting Possibilities**

1. **Branch Performance Report**: Sales per branch
2. **Inventory Status**: Current stock per branch
3. **Low Stock Alerts**: Per branch basis
4. **Transfer History**: Allocation tracking
5. **Branch Comparison**: Revenue, stock turnover, etc.

---

## 🚀 **Next Steps**

1. Run migrations
2. Update all models with relationships
3. Create BranchInventoryController
4. Build allocation UI (view)
5. Modify POS to show only branch inventory
6. Update checkout logic to deduct from branch stock
7. Add branch selection for managers/staff login

---

## ✅ **Is This Achievable?**

**YES - 100% Achievable!**

This is a standard multi-location inventory system used by:
- Retail chains
- Restaurant franchises
- Warehouses with multiple locations

**Complexity**: Moderate  
**Time to Implement**: 2-3 days for full functionality  
**Maintainability**: High (clean architecture)  
**Scalability**: Excellent (supports unlimited branches)

---

## 📝 **Summary**

Your system will work like this:

**Creator Flow:**
1. Adds products → Central inventory
2. Goes to "Branch Allocation" page
3. Selects branch + items + quantity
4. Clicks "Allocate" → Stock transferred

**Branch Flow:**
1. Manager/Staff logs in → Auto-assigned to branch
2. POS shows only their branch's inventory
3. Makes sale → Deducts from branch stock
4. Sale records include branch_id, branch_manager_id

**Simple, effective, and scalable!** 🎉
