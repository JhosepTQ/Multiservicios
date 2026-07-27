<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inventario</title>
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
            max-width: 900px;
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
        .report-date {
            background-color: #f3f4f6;
            padding: 10px;
            margin: 15px 0;
            border-left: 4px solid #7c3aed;
            border-radius: 4px;
            font-size: 12px;
        }
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .inventory-table thead {
            background-color: #7c3aed;
            color: white;
        }
        .inventory-table th {
            padding: 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
        }
        .inventory-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .inventory-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            text-align: center;
            width: 60px;
        }
        .status-critico {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-bajo {
            background-color: #fef3c7;
            color: #b45309;
        }
        .status-ok {
            background-color: #d1fae5;
            color: #065f46;
        }
        .summary {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .summary-box {
            padding: 12px;
            border-radius: 4px;
            text-align: center;
            border-left: 3px solid #7c3aed;
        }
        .summary-box h3 {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .summary-box .count {
            font-size: 18px;
            font-weight: bold;
            color: #7c3aed;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #666;
            font-size: 10px;
        }
        .price {
            color: #7c3aed;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 REPORTE DE INVENTARIO</h1>
            <p>Sistema Multiservicio</p>
        </div>

        <div class="report-date">
            <strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}
        </div>

        <div class="summary">
            <div class="summary-box">
                <h3>Total Productos</h3>
                <div class="count">{{ $products->count() }}</div>
            </div>
            <div class="summary-box">
                <h3>En Stock Crítico</h3>
                <div class="count">{{ $products->where('stock', '<=', 0)->count() }}</div>
            </div>
            <div class="summary-box">
                <h3>Stock Bajo</h3>
                <div class="count">{{ $products->whereBetween('stock', [1, 'min_stock'])->count() }}</div>
            </div>
        </div>

        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th class="text-right">Stock Actual</th>
                    <th class="text-right">Stock Mínimo</th>
                    <th class="text-right">Precio Unit.</th>
                    <th style="text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->sku }}</td>
                        <td class="text-right">{{ $product->stock }}</td>
                        <td class="text-right">{{ $product->min_stock }}</td>
                        <td class="text-right"><span class="price">S/. {{ number_format($product->price, 2) }}</span></td>
                        <td style="text-align: center;">
                            @if($product->stock <= 0)
                                <span class="status-badge status-critico">CRÍTICO</span>
                            @elseif($product->stock <= $product->min_stock)
                                <span class="status-badge status-bajo">BAJO</span>
                            @else
                                <span class="status-badge status-ok">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">No hay productos registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>Reporte de inventario actual del sistema</p>
            <p style="margin-top: 5px;">Sistema Multiservicio © 2026</p>
        </div>
    </div>
</body>
</html>
