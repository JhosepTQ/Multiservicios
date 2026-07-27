<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('items.product', 'user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('receipt_number', 'like', "%$search%")
                  ->orWhere('customer_name', 'like', "%$search%")
                  ->orWhere('customer_email', 'like', "%$search%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from,
                $request->date_to
            ]);
        }

        return response()->json(
            $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 50)),
            200
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'in:pendiente,completada,cancelada',
        ]);

        return DB::transaction(function () use ($validated) {
            $sale = auth()->user()->sales()->create([
                'receipt_number' => $this->generateReceiptNumber(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'tax' => $validated['tax'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'] ?? 'completada',
            ]);

            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $itemTotal = $item['quantity'] * $item['price'];
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total' => $itemTotal,
                ]);

                // Decrease stock
                $product->decrement('stock', $item['quantity']);
                $subtotal += $itemTotal;
            }

            $sale->update([
                'subtotal' => $subtotal,
                'total' => $subtotal + $sale->tax,
            ]);

            return response()->json([
                'message' => 'Venta registrada exitosamente',
                'sale' => $sale->load('items.product', 'user'),
            ], 201);
        });
    }

    public function show($id)
    {
        $sale = Sale::with('items.product', 'user')->findOrFail($id);
        return response()->json($sale, 200);
    }

    public function update(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        $validated = $request->validate([
            'customer_name' => 'string|max:255',
            'customer_phone' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'status' => 'in:pendiente,completada,cancelada',
            'notes' => 'nullable|string',
        ]);

        $sale->update($validated);

        return response()->json([
            'message' => 'Venta actualizada exitosamente',
            'sale' => $sale->load('items.product', 'user'),
        ], 200);
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        
        // Restore stock
        foreach ($sale->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        $sale->delete();

        return response()->json([
            'message' => 'Venta eliminada exitosamente',
        ], 200);
    }

    private function generateReceiptNumber()
    {
        $count = Sale::count();
        return 'BOL-' . date('Ymd') . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }
}
