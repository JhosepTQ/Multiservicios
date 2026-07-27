<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta {{ $sale->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #1f2937;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 12px;
        }
        .receipt-number {
            background-color: #f3f4f6;
            padding: 10px;
            margin: 15px 0;
            border-left: 4px solid #3b82f6;
        }
        .receipt-number strong {
            color: #1f2937;
            font-size: 14px;
        }
        .info-section {
            margin: 20px 0;
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 4px;
        }
        .info-section h3 {
            color: #1f2937;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .info-section p {
            font-size: 11px;
            margin: 2px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table thead {
            background-color: #1f2937;
            color: white;
        }
        .items-table th {
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #1f2937;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 12px;
        }
        .total-row.grand-total {
            font-weight: bold;
            font-size: 14px;
            background-color: #f3f4f6;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #666;
            font-size: 10px;
        }
        .currency {
            font-weight: 600;
            color: #10b981;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧾 BOLETA DE VENTA</h1>
            <p>Sistema Multiservicio</p>
        </div>

        <div class="receipt-number">
            <strong>Boleta: {{ $sale->receipt_number }}</strong>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="info-section">
                <h3>Cliente</h3>
                <p><strong>{{ $sale->customer_name }}</strong></p>
                <p>Teléfono: {{ $sale->customer_phone }}</p>
                <p>Email: {{ $sale->customer_email }}</p>
            </div>
            <div class="info-section">
                <h3>Detalles</h3>
                <p>Fecha: {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                <p>Estado: <span style="background-color: #10b981; color: white; padding: 2px 8px; border-radius: 3px; font-size: 10px;">{{ strtoupper($sale->status) }}</span></p>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Precio Unitario</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sale->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right"><span class="currency">S/. {{ number_format($item->unit_price, 2) }}</span></td>
                        <td class="text-right"><span class="currency">S/. {{ number_format($item->total, 2) }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No hay items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span class="currency">S/. {{ number_format($sale->subtotal ?? $sale->total, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Impuestos:</span>
                <span class="currency">S/. {{ number_format($sale->tax ?? 0, 2) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span class="currency">S/. {{ number_format($sale->total, 2) }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Gracias por su compra</p>
            <p style="margin-top: 5px;">Generado: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
