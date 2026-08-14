<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Completed Sales Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Completed Sales</h2>
    <table>
        <thead>
            <tr>
                <th>Receipt Number</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Seller</th>
                @if(!$hideBranchColumn)
                <th>Branch</th>
                @endif
                <th>Items Sold</th>
                <th>Total Discount (₦)</th>
                <th>Net Total (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($completedSales as $sale)
            <tr>
                <td>{{ $sale->receipt_number }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y H:i') }}</td>
                <td>{{ $sale->customer_name ?? 'Walk-in Customer' }}</td>
                <td>
                    @if($sale->staff_id)
                        {{ $sale->staff_name }} (Staff)
                    @else
                        {{ $sale->manager_name }} (Manager)
                    @endif
                </td>
                @if(!$hideBranchColumn)
                <td>{{ $sale->branch_name ?? 'Main Branch' }}</td>
                @endif
                <td>{{ $sale->items_count }}</td>
                <td>{{ number_format($sale->discount, 2) }}</td>
                <td>{{ number_format($sale->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
