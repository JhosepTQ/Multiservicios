<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with('items.product', 'user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('quotation_number', 'like', "%$search%")
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
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'valid_until' => 'required|date|after:today',
            'notes' => 'nullable|string',
            'status' => 'in:pendiente,aceptada,rechazada',
        ]);

        return DB::transaction(function () use ($validated) {
            $quotation = auth()->user()->quotations()->create([
                'quotation_number' => $this->generateQuotationNumber(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'] ?? 'pendiente',
            ]);

            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $itemTotal = $item['quantity'] * $item['price'];
                
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'description' => 'Item',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total' => $itemTotal,
                ]);

                $subtotal += $itemTotal;
            }

            $total = $subtotal - $quotation->discount + $quotation->tax;
            $quotation->update([
                'subtotal' => $subtotal,
                'total' => $total,
            ]);

            return response()->json([
                'message' => 'Cotización creada exitosamente',
                'quotation' => $quotation->load('items.product', 'user'),
            ], 201);
        });
    }

    public function show($id)
    {
        $quotation = Quotation::with('items.product', 'user')->findOrFail($id);
        return response()->json($quotation, 200);
    }

    public function update(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);

        $validated = $request->validate([
            'customer_name' => 'string|max:255',
            'customer_phone' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'valid_until' => 'date|after:today',
            'status' => 'in:pendiente,aceptada,rechazada',
            'notes' => 'nullable|string',
        ]);

        $quotation->update($validated);

        return response()->json([
            'message' => 'Cotización actualizada exitosamente',
            'quotation' => $quotation->load('items.product', 'user'),
        ], 200);
    }

    public function destroy($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->delete();

        return response()->json([
            'message' => 'Cotización eliminada exitosamente',
        ], 200);
    }

    private function generateQuotationNumber()
    {
        $count = Quotation::count();
        return 'COT-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }
}
