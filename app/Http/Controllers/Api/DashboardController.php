<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary()
    {
        $currentMonth = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        // Total sales this month
        $totalSales = Sale::where('status', 'completada')
            ->whereBetween('created_at', [$currentMonth, $currentMonthEnd])
            ->sum('total');

        // Total expenses this month
        $totalExpenses = Expense::whereBetween('expense_date', [$currentMonth, $currentMonthEnd])
            ->sum('amount');

        // Profit
        $profit = $totalSales - $totalExpenses;

        // Total active products
        $totalProducts = Product::where('active', true)->count();

        // Low stock products
        $lowStockProducts = Product::whereRaw('stock <= min_stock')->count();

        // Recent sales
        $recentSales = Sale::with('items.product')
            ->where('status', 'completada')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Sales by category this month
        $salesByCategory = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completada')
            ->whereBetween('sales.created_at', [$currentMonth, $currentMonthEnd])
            ->select('categories.name', DB::raw('SUM(sale_items.total) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->get();

        // Top products
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completada')
            ->whereBetween('sales.created_at', [$currentMonth, $currentMonthEnd])
            ->select('products.name', DB::raw('COUNT(*) as sales_count'), DB::raw('SUM(sale_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();

        // Pending quotations
        $pendingQuotations = Quotation::where('status', 'pendiente')->count();

        // Daily transactions (today)
        $today = now()->startOfDay();
        $dailyTransactions = Sale::where('status', 'completada')
            ->whereBetween('created_at', [$today, now()])
            ->count();

        // Average ticket (this month)
        $totalSalesCount = Sale::where('status', 'completada')
            ->whereBetween('created_at', [$currentMonth, $currentMonthEnd])
            ->count();
        $averageTicket = $totalSalesCount > 0 ? round($totalSales / $totalSalesCount, 2) : 0;

        // Customers served (unique customers this month)
        $customersServed = Sale::where('status', 'completada')
            ->whereBetween('created_at', [$currentMonth, $currentMonthEnd])
            ->distinct('customer_name')
            ->count('customer_name');

        return response()->json([
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
            'profit' => $profit,
            'total_products' => $totalProducts,
            'low_stock_products' => $lowStockProducts,
            'recent_sales' => $recentSales,
            'sales_by_category' => $salesByCategory,
            'top_products' => $topProducts,
            'pending_quotations' => $pendingQuotations,
            'daily_transactions' => $dailyTransactions,
            'average_ticket' => $averageTicket,
            'customers_served' => $customersServed,
        ], 200);
    }
}
