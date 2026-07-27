<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte P&G</title>
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
            border-bottom: 3px solid #0f766e;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #0f766e;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .period {
            background-color: #f0fdfa;
            padding: 10px;
            margin: 15px 0;
            border-left: 4px solid #0f766e;
            border-radius: 4px;
        }
        .report-section {
            margin: 25px 0;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 4px;
        }
        .report-section h2 {
            color: #0f766e;
            font-size: 14px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .report-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
        }
        .report-row.header {
            font-weight: 600;
            background-color: #0f766e;
            color: white;
            padding: 10px;
            margin: -15px -15px 10px -15px;
            border-radius: 4px 4px 0 0;
        }
        .report-row.total {
            font-weight: bold;
            font-size: 13px;
            background-color: #f0fdfa;
            padding: 12px;
            margin: 10px 0 0 0;
            border: 1px solid #0f766e;
            border-radius: 4px;
        }
        .label {
            flex: 1;
        }
        .amount {
            flex: 0 0 150px;
            text-align: right;
            font-weight: 600;
            color: #0f766e;
        }
        .profit {
            color: #10b981 !important;
        }
        .loss {
            color: #ef4444 !important;
        }
        .summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .summary-box {
            padding: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .summary-box h3 {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .summary-box .amount {
            display: block;
            font-size: 18px;
            flex: none;
            text-align: center;
        }
        .positive {
            background-color: #d1fae5;
            border-left: 4px solid #10b981;
        }
        .negative {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 REPORTE DE PÉRDIDAS Y GANANCIAS</h1>
            <p>Sistema Multiservicio</p>
        </div>

        <div class="period">
            <strong>Período:</strong> {{ \Carbon\Carbon::parse($report['date_from'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($report['date_to'])->format('d/m/Y') }}
        </div>

        <div class="summary">
            <div class="summary-box positive">
                <h3>Ingresos Totales</h3>
                <span class="amount">S/. {{ number_format($report['total_sales'], 2) }}</span>
            </div>
            <div class="summary-box negative">
                <h3>Gastos Totales</h3>
                <span class="amount">S/. {{ number_format($report['total_expenses'], 2) }}</span>
            </div>
        </div>

        <div class="report-section">
            <div class="report-row header">
                <span class="label">Concepto</span>
                <span class="amount">Monto (S/.)</span>
            </div>
            
            <div class="report-row">
                <span class="label">Ventas Completadas</span>
                <span class="amount profit">S/. {{ number_format($report['total_sales'], 2) }}</span>
            </div>
            
            <div class="report-row">
                <span class="label">Gastos Operacionales</span>
                <span class="amount loss">-S/. {{ number_format($report['total_expenses'], 2) }}</span>
            </div>

            <div class="report-row total" style="{{ $report['profit'] >= 0 ? 'border-color: #10b981; background-color: #d1fae5;' : 'border-color: #ef4444; background-color: #fee2e2;' }}">
                <span class="label">Ganancia Neta</span>
                <span class="amount" style="{{ $report['profit'] >= 0 ? 'color: #10b981;' : 'color: #ef4444;' }}">
                    S/. {{ number_format($report['profit'], 2) }}
                </span>
            </div>
        </div>

        <div class="report-section">
            <h2>📈 Análisis</h2>
            <div class="report-row">
                <span class="label">Margen de Ganancia</span>
                <span class="amount">
                    @if($report['total_sales'] > 0)
                        {{ number_format(($report['profit'] / $report['total_sales']) * 100, 2) }}%
                    @else
                        0.00%
                    @endif
                </span>
            </div>
            <div class="report-row">
                <span class="label">Ratio Gasto/Ingreso</span>
                <span class="amount">
                    @if($report['total_sales'] > 0)
                        {{ number_format(($report['total_expenses'] / $report['total_sales']) * 100, 2) }}%
                    @else
                        0.00%
                    @endif
                </span>
            </div>
        </div>

        <div class="footer">
            <p>Reporte generado por Sistema Multiservicio</p>
            <p style="margin-top: 5px;">{{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
