<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth();
        $dateTo = $request->date_to ?? now()->endOfMonth();

        $sales = Sale::where('status', 'completada')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('items.product', 'user')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSales = $sales->sum('total');
        $totalQuantity = $sales->sum(function ($sale) {
            return $sale->items->sum('quantity');
        });

        return response()->json([
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'total_sales' => $totalSales,
            'total_quantity' => $totalQuantity,
            'sales_count' => $sales->count(),
            'average_sale' => $sales->count() > 0 ? $totalSales / $sales->count() : 0,
            'sales' => $sales,
        ], 200);
    }

    public function expenseReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth();
        $dateTo = $request->date_to ?? now()->endOfMonth();

        $expenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->with('user')
            ->orderBy('expense_date', 'desc')
            ->get()
            ->groupBy('category');

        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->sum('amount');

        $byCategory = [];
        foreach ($expenses as $category => $items) {
            $byCategory[$category] = [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
                'items' => $items,
            ];
        }

        return response()->json([
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'total_expenses' => $totalExpenses,
            'by_category' => $byCategory,
            'expense_count' => Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->count(),
        ], 200);
    }

    public function profitLossReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth();
        $dateTo = $request->date_to ?? now()->endOfMonth();

        $totalIncome = Sale::where('status', 'completada')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total');

        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->sum('amount');

        $netProfit = $totalIncome - $totalExpenses;

        return response()->json([
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'income' => $totalIncome,
            'expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'profit_margin' => $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0,
        ], 200);
    }

    public function inventoryReport()
    {
        $products = Product::with('category')
            ->orderBy('stock')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name,
                    'sku' => $product->sku,
                    'stock' => $product->stock,
                    'min_stock' => $product->min_stock,
                    'status' => $product->stock <= 0 ? 'CRITICO' : ($product->stock <= $product->min_stock ? 'BAJO' : 'OK'),
                    'price' => $product->price,
                    'cost' => $product->cost,
                    'margin' => $product->margin,
                ];
            });

        $criticalProducts = $products->where('status', 'CRITICO')->count();
        $lowStockProducts = $products->where('status', 'BAJO')->count();

        return response()->json([
            'total_products' => $products->count(),
            'critical_products' => $criticalProducts,
            'low_stock_products' => $lowStockProducts,
            'products' => $products,
        ], 200);
    }
}
