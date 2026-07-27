<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización {{ $quotation->quotation_number }}</title>
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
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #7c3aed;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .quotation-number {
            background-color: #f3f4f6;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #7c3aed;
            border-radius: 4px;
        }
        .quotation-number strong {
            color: #7c3aed;
            font-size: 14px;
        }
        .info-section {
            margin: 20px 0;
            padding: 12px;
            background-color: #f9fafb;
            border-radius: 4px;
        }
        .info-section h3 {
            color: #7c3aed;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .info-section p {
            font-size: 11px;
            margin: 3px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table thead {
            background-color: #7c3aed;
            color: white;
        }
        .items-table th {
            padding: 12px;
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
            border-top: 2px solid #7c3aed;
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
            background-color: #faf5ff;
            padding: 12px;
            margin-top: 10px;
            border-radius: 4px;
            border-left: 3px solid #7c3aed;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            margin-top: 5px;
        }
        .status-pendiente {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-aceptada {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-rechazada {
            background-color: #fee2e2;
            color: #7f1d1d;
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
            color: #7c3aed;
        }
        .validity {
            background-color: #ede9fe;
            padding: 10px;
            border-radius: 4px;
            margin: 15px 0;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📄 COTIZACIÓN</h1>
            <p>Sistema Multiservicio</p>
        </div>

        <div class="quotation-number">
            <strong>Cotización: {{ $quotation->quotation_number }}</strong>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="info-section">
                <h3>Cliente</h3>
                <p><strong>{{ $quotation->customer_name }}</strong></p>
                <p>Teléfono: {{ $quotation->customer_phone }}</p>
                <p>Email: {{ $quotation->customer_email }}</p>
            </div>
            <div class="info-section">
                <h3>Detalles</h3>
                <p>Fecha Emisión: {{ $quotation->created_at->format('d/m/Y') }}</p>
                <p>Válida hasta: {{ $quotation->valid_until->format('d/m/Y') }}</p>
                <p>Estado: <span class="status-badge status-{{ $quotation->status }}">{{ strtoupper($quotation->status) }}</span></p>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Precio Unitario</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotation->items as $item)
                    <tr>
                        <td>{{ $item->description ?? $item->product->name ?? 'Producto' }}</td>
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

        <div class="validity">
            <strong>⚠️ Validez de cotización:</strong> Esta cotización es válida hasta el {{ $quotation->valid_until->format('d/m/Y') }}. Después de esta fecha, los precios pueden cambiar.
        </div>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span class="currency">S/. {{ number_format($quotation->subtotal ?? 0, 2) }}</span>
            </div>
            @if($quotation->discount)
            <div class="total-row">
                <span>Descuento:</span>
                <span class="currency">-S/. {{ number_format($quotation->discount, 2) }}</span>
            </div>
            @endif
            <div class="total-row">
                <span>Impuestos (IGV 18%):</span>
                <span class="currency">S/. {{ number_format($quotation->tax ?? 0, 2) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span class="currency">S/. {{ number_format($quotation->total, 2) }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Esta es una cotización. No es factura. Consulte términos y condiciones.</p>
            <p style="margin-top: 5px;">Generado: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
