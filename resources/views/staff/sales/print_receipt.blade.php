
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Receipt - {{ $sale->receipt_number }}</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .receipt-container {
            max-width: 600px;
            margin: 30px auto;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
        }

        .receipt-logo {
            width: 120px;
            height: auto;
            max-height: 80px;
            object-fit: contain;
            margin-bottom: 15px;
            background: white;
            padding: 8px;
            border-radius: 8px;
        }

        .receipt-header h4 {
            margin: 0 0 5px 0;
            font-size: 24px;
            font-weight: 700;
        }

        .receipt-header .business-name {
            font-size: 16px;
            opacity: 0.95;
        }

        .receipt-body {
            padding: 30px;
        }

        .receipt-info {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px dashed #e0e0e0;
        }

        .receipt-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .receipt-info-label {
            color: #6c757d;
            font-weight: 500;
        }

        .receipt-info-value {
            color: #2c3e50;
            font-weight: 600;
        }

        .receipt-items-header {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .items-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #f8f9fa;
            padding: 10px 8px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #e0e0e0;
        }

        .items-table td {
            padding: 10px 8px;
            font-size: 13px;
            border: 1px solid #e0e0e0;
        }

        .receipt-totals {
            padding-top: 20px;
            border-top: 2px dashed #e0e0e0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .total-row.grand-total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #2c3e50;
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
        }

        .print-btns {
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 2px dashed #e0e0e0;
            text-align: center;
        }

        /* Print Styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            .receipt-container {
                max-width: 100% !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            .receipt-header {
                padding: 15px 20px !important;
            }

            .receipt-logo {
                width: 100px !important;
                max-height: 60px !important;
                margin-bottom: 10px !important;
            }

            .receipt-header h4 {
                font-size: 22px !important;
            }

            .receipt-header .business-name {
                font-size: 15px !important;
            }

            .receipt-body {
                padding: 15px 30px !important;
            }

            .receipt-info {
                margin-bottom: 15px !important;
                padding-bottom: 12px !important;
            }

            .receipt-info-row {
                margin-bottom: 6px !important;
                font-size: 13px !important;
            }

            .receipt-items-header {
                font-size: 14px !important;
                margin-bottom: 12px !important;
            }

            .items-table th {
                padding: 8px 6px !important;
                font-size: 12px !important;
            }

            .items-table td {
                padding: 8px 6px !important;
                font-size: 12px !important;
            }

            .receipt-totals {
                padding-top: 12px !important;
            }

            .total-row {
                font-size: 14px !important;
                margin-bottom: 8px !important;
            }

            .total-row.grand-total {
                font-size: 18px !important;
                margin-top: 12px !important;
                padding-top: 12px !important;
            }

            .print-btns {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="SalesPilot Logo" class="receipt-logo">
            <h4><i class="bi bi-receipt"></i> Sales Receipt</h4>
            <div class="business-name">SalesPilot Inventory</div>
        </div>

        <div class="receipt-body">
            <div class="receipt-info">
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Receipt Number:</span>
                    <span class="receipt-info-value">{{ $sale->receipt_number }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Date:</span>
                    <span class="receipt-info-value">{{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y h:i A') }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Customer:</span>
                    <span class="receipt-info-value">{{ $sale->customer_name ?? 'Walk-in Customer' }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Sold By:</span>
                    <span class="receipt-info-value">
                        @if($sale->staff_id && $sale->staff)
                            {{ $sale->staff->fullname }}
                        @elseif($sale->user)
                            {{ $sale->user->name }}
                        @else
                            Staff
                        @endif
                    </span>
                </div>
            </div>

            <div class="receipt-items-header">Items Purchased</div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Price</th>
                        <th style="text-align: right;">Subtotal</th>
                        <th style="text-align: right;">Discount</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td><strong>{{ $item->item_name }}</strong></td>
                            <td style="text-align: center;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">₦{{ number_format($item->item_price, 2) }}</td>
                            <td style="text-align: right;">₦{{ number_format($item->subtotal, 2) }}</td>
                            <td style="text-align: right;">₦{{ number_format($item->discount, 2) }}</td>
                            <td style="text-align: right;"><strong>₦{{ number_format($item->total, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="receipt-totals">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>₦{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>Discount:</span>
                    <span>-₦{{ number_format($discount, 2) }}</span>
                </div>
                <div class="total-row grand-total">
                    <span>Total:</span>
                    <span>₦{{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="print-btns">
            <button class="btn btn-primary me-2" onclick="window.print()">
                <i class="bi bi-printer"></i> Print Receipt
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
