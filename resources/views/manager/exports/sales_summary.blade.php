<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Summary Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Sales Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Transactions</th>
                <th>Items Sold</th>
                <th>Gross Sales (₦)</th>
                <th>Discount (₦)</th>
                <th>Cost of Items (₦)</th>
                <th>Tax (₦)</th>
                <th>Gross Profit (₦)</th>
                <th>Margin (%)</th>
                <th>Net Sales (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesSummary as $sale)
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y') }}</td>
                <td>{{ number_format($sale->transaction_count) }}</td>
                <td>{{ number_format($sale->items_sold) }}</td>
                <td>{{ number_format($sale->gross_sales, 2) }}</td>
                <td>{{ number_format($sale->total_discount, 2) }}</td>
                <td>{{ number_format($sale->cost_of_items, 2) }}</td>
                <td>{{ number_format($sale->taxes, 2) }}</td>
                <td>{{ number_format($sale->gross_profit, 2) }}</td>
                <td>{{ number_format($sale->margin, 1) }}%</td>
                <td>{{ number_format($sale->net_sales, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
