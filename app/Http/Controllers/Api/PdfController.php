<?php

namespace App\Http\Controllers\Api;

use App\Models\Sale;
use App\Models\Quotation;
use App\Models\Expense;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends \App\Http\Controllers\Controller
{
    // Generate Sale Receipt PDF
    public function saleReceipt($id)
    {
        $sale = Sale::with('items.product')->findOrFail($id);
        
        $pdf = Pdf::loadView('pdfs.sale-receipt', ['sale' => $sale]);
        return $pdf->download("boleta-{$sale->receipt_number}.pdf");
    }

    // Generate Quotation PDF
    public function quotationPdf($id)
    {
        $quotation = Quotation::with('items.product')->findOrFail($id);
        
        $pdf = Pdf::loadView('pdfs.quotation', ['quotation' => $quotation]);
        return $pdf->download("cotizacion-{$quotation->quotation_number}.pdf");
    }

    // Generate Profit & Loss Report
    public function profitLossReport(Request $request)
    {
        $dateFrom = $request->query('date_from', now()->startOfMonth());
        $dateTo = $request->query('date_to', now()->endOfMonth());

        $totalSales = Sale::whereBetween('created_at', [$dateFrom, $dateTo])->sum('total');
        $totalExpenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])->sum('amount');

        $report = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
            'profit' => $totalSales - $totalExpenses,
        ];

        $pdf = Pdf::loadView('pdfs.profit-loss', ['report' => $report]);
        return $pdf->download('reporte-pyg.pdf');
    }

    // Generate Inventory Report
    public function inventoryReport()
    {
        $products = Product::all();

        $pdf = Pdf::loadView('pdfs.inventory', ['products' => $products]);
        return $pdf->download('reporte-inventario.pdf');
    }
}
