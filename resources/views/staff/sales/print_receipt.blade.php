

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Receipt</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .receipt-container { max-width: 500px; margin: 30px auto; border-radius: 8px; background: #fff; box-shadow: 0 2px 8px #eee; padding: 28px 28px 18px 28px; }
        @media print {
            .print-btns { display: none !important; }
            body { background: #fff !important; }
            .receipt-container { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-container border">
        <h4 class="text-center mb-4">Sales Receipt</h4>
        <div class="mb-3">
            <div><strong>Receipt Number:</strong> {{ $sale->receipt_number }}</div>
            <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y h:i A') }}</div>
            <div><strong>Customer:</strong> {{ $sale->customer_name ?? 'Walk-in Customer' }}</div>
            <div><strong>Sold By:</strong>
                @if($sale->staff_id && $sale->staff)
                    {{ $sale->staff->fullname }}
                @elseif($sale->user)
                    {{ $sale->user->name }}
                @else
                    Unknown
                @endif
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₦{{ number_format($item->item_price, 2) }}</td>
                            <td>₦{{ number_format($item->subtotal, 2) }}</td>
                            <td>₦{{ number_format($item->discount, 2) }}</td>
                            <td>₦{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row mt-4">
            <div class="col-6 text-end"><strong>Subtotal:</strong></div>
            <div class="col-6 text-end">₦{{ number_format($subtotal, 2) }}</div>
            <div class="col-6 text-end"><strong>Discount:</strong></div>
            <div class="col-6 text-end">₦{{ number_format($discount, 2) }}</div>
            <div class="col-6 text-end"><h5><strong>Total:</strong></h5></div>
            <div class="col-6 text-end"><h5>₦{{ number_format($total, 2) }}</h5></div>
        </div>
        <div class="print-btns mt-4">
            <button class="btn btn-primary me-2" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <!-- Optionally include Bootstrap JS for icons (if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>
