<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            margin: 0;
            padding: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            border-radius: 4px;
            background: #fbfbfb;
        }
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
        }
        .title {
            font-size: 28px;
            color: #2b6cb0;
            font-weight: bold;
        }
        .details {
            text-align: right;
        }
        .table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .table th {
            background: #edf2f7;
            font-weight: bold;
        }
        .total-row td {
            font-weight: bold;
            text-align: right;
            border-bottom: none;
            padding-top: 5px;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td class="title">INVOICE</td>
                <td class="details">
                    <strong>Invoice #:</strong> {{ $sale->invoice_number }}<br>
                    <strong>Date:</strong> {{ $sale->created_at->format('Y-m-d H:i') }}<br>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Billed To:</strong><br>
                    {{ $sale->customer->name }}<br>
                    {{ $sale->customer->email }}
                </td>
                <td class="details">
                    <strong>Branch Location:</strong><br>
                    {{ $sale->branch->name }}<br>
                    {{ $sale->branch->location ?? 'Headquarters' }}
                </td>
            </tr>
        </table>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>${{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
                
                <tr class="total-row">
                    <td colspan="3">Subtotal:</td>
                    <td>${{ number_format($sale->subtotal, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3">Tax:</td>
                    <td>${{ number_format($sale->tax_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3">Discount:</td>
                    <td>-${{ number_format($sale->discount_amount, 2) }}</td>
                </tr>
                <tr class="total-row" style="border-top: 2px solid #2b6cb0;">
                    <td colspan="3" style="font-size: 16px; color: #2b6cb0;">Total Paid:</td>
                    <td style="font-size: 16px; color: #2b6cb0;">${{ number_format($sale->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
