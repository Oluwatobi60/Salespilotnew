<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales by Category Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Sales by Category</h2>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Items Sold</th>
                <th>Gross Sales (₦)</th>
                <th>Discount (₦)</th>
                <th>Cost (₦)</th>
                <th>Tax (₦)</th>
                <th>Gross Profit (₦)</th>
                <th>Margin (%)</th>
                <th>Net Sales (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesByCategory as $cat)
            <tr>
                <td>{{ $cat['category_name'] }}</td>
                <td>{{ number_format($cat['total_quantity_sold']) }}</td>
                <td>{{ number_format($cat['gross_sales'], 2) }}</td>
                <td>{{ number_format($cat['total_discount'], 2) }}</td>
                <td>{{ number_format($cat['total_cost'], 2) }}</td>
                <td>{{ number_format($cat['tax'], 2) }}</td>
                <td>{{ number_format($cat['gross_profit'], 2) }}</td>
                <td>{{ number_format($cat['margin'], 1) }}%</td>
                <td>{{ number_format($cat['total_sales'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td>Total</td>
                <td>{{ number_format($salesByCategory->sum('total_quantity_sold')) }}</td>
                <td>{{ number_format($totals['gross_sales'], 2) }}</td>
                <td>{{ number_format($totals['total_discount'], 2) }}</td>
                <td>{{ number_format($totals['items_cost'], 2) }}</td>
                <td>{{ number_format($totals['tax'], 2) }}</td>
                <td>{{ number_format($totals['gross_profit'], 2) }}</td>
                <td></td>
                <td>{{ number_format($totals['net_sales'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
