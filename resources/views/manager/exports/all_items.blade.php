<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Items Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>All Items Inventory</h2>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Type</th>
                <th>SKU/Code</th>
                <th>Category</th>
                <th>Cost Price (₦)</th>
                <th>Selling Price (₦)</th>
                <th>Profit Margin (%)</th>
                <th>Current Stock</th>
                <th>Opening Stock</th>
                <th>Low Stock Threshold</th>
                <th>Added On</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allItems as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $item['type'])) }}</td>
                <td>{{ $item['code'] }}</td>
                <td>{{ $item['category'] ?? 'Uncategorized' }}</td>
                <td>{{ isset($item['cost_price']) ? number_format($item['cost_price'], 2) : '-' }}</td>
                <td>{{ isset($item['selling_price']) ? number_format($item['selling_price'], 2) : '-' }}</td>
                <td>{{ isset($item['profit_margin']) ? number_format($item['profit_margin'], 1) . '%' : '-' }}</td>
                <td>{{ number_format($item['current_stock']) }}</td>
                <td>{{ number_format($item['opening_stock']) }}</td>
                <td>{{ $item['low_stock_threshold'] ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($item['created_at'])->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
