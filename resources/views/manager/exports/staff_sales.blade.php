<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales by Staff Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Sales by Staff</h2>
    <table>
        <thead>
            <tr>
                <th>Staff Name</th>
                <th>Role</th>
                <th>Transactions</th>
                <th>Items Sold</th>
                <th>Total Sales (₦)</th>
                <th>Last Sale Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesbystaff as $staff)
            <tr>
                <td>{{ $staff->seller_name }}</td>
                <td>{{ $staff->seller_role }}</td>
                <td>{{ number_format($staff->transactions_count) }}</td>
                <td>{{ number_format($staff->items_sold) }}</td>
                <td>{{ number_format($staff->total_sales, 2) }}</td>
                <td>{{ $staff->last_transaction_date ? \Carbon\Carbon::parse($staff->last_transaction_date)->format('M d, Y H:i') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="2">Total</td>
                <td>{{ number_format($totals->transactions_count ?? 0) }}</td>
                <td>{{ number_format($totals->items_sold ?? 0) }}</td>
                <td>{{ number_format($totals->total_sales ?? 0, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
