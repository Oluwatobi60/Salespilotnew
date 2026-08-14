<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales by Item Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Sales by Item</h2>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>SKU</th>
                <th>Quantity Sold</th>
                <th>Cost Price (₦)</th>
                <th>Total Cost (₦)</th>
                <th>Gross Sales (₦)</th>
                <th>Discount (₦)</th>
                <th>Gross Profit (₦)</th>
                <th>Margin (%)</th>
                <th>Net Sales (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesbyitem as $item)
            <tr>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->category_name }}</td>
                <td>{{ $item->sku }}</td>
                <td>{{ number_format($item->total_quantity_sold) }}</td>
                <td>{{ number_format($item->cost_price, 2) }}</td>
                <td>{{ number_format($item->total_cost, 2) }}</td>
                <td>{{ number_format($item->gross_sales, 2) }}</td>
                <td>{{ number_format($item->total_discount, 2) }}</td>
                <td>{{ number_format($item->gross_profit, 2) }}</td>
                <td>{{ number_format($item->profit_margin, 1) }}%</td>
                <td>{{ number_format($item->total_sales, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="3">Total</td>
                <td>{{ number_format($salesbyitem->sum('total_quantity_sold')) }}</td>
                <td>-</td>
                <td>{{ number_format($totals['cost_price'], 2) }}</td>
                <td>{{ number_format($totals['gross_sales'], 2) }}</td>
                <td>{{ number_format($totals['total_discount'], 2) }}</td>
                <td>{{ number_format($totals['gross_profit'], 2) }}</td>
                <td>-</td>
                <td>{{ number_format($salesbyitem->sum('total_sales'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
