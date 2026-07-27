<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('concept', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%");
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('expense_date', [
                $request->date_from,
                $request->date_to
            ]);
        }

        return response()->json(
            $query->orderBy('expense_date', 'desc')->paginate($request->get('per_page', 50)),
            200
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'concept' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:Servicios,Suministros,Nómina,Otros',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        $expense = auth()->user()->expenses()->create($validated);

        return response()->json([
            'message' => 'Gasto registrado exitosamente',
            'expense' => $expense->load('user'),
        ], 201);
    }

    public function show($id)
    {
        $expense = Expense::with('user')->findOrFail($id);
        return response()->json($expense, 200);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'concept' => 'string|max:255',
            'description' => 'nullable|string',
            'category' => 'in:Servicios,Suministros,Nómina,Otros',
            'amount' => 'numeric|min:0',
            'expense_date' => 'date',
        ]);

        $expense->update($validated);

        return response()->json([
            'message' => 'Gasto actualizado exitosamente',
            'expense' => $expense->load('user'),
        ], 200);
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json([
            'message' => 'Gasto eliminado exitosamente',
        ], 200);
    }
}
