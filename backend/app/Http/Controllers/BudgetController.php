<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BudgetController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome_cliente' => 'required|string|max:100',
                'data_solicitacao' => 'required|date',
                'itens' => 'required|array|min:1',
                'itens.*.product_id' => 'required|integer|exists:products,id',
                'itens.*.quantidade' => 'required|integer|min:1',
            ], [
                'nome_cliente.required' => 'O nome do cliente é obrigatório.',
                'data_solicitacao.required' => 'A data da solicitação é obrigatória.',
                'itens.required' => 'Adicione ao menos um item.',
                'itens.min' => 'Adicione ao menos um item.',
                'itens.*.product_id.required' => 'O produto é obrigatório.',
                'itens.*.product_id.exists' => 'Produto inválido.',
                'itens.*.quantidade.required' => 'A quantidade é obrigatória.',
                'itens.*.quantidade.min' => 'A quantidade deve ser maior que zero.',
            ]);

            $budget = DB::transaction(function () use ($validated) {
                $productIds = collect($validated['itens'])->pluck('product_id')->unique()->values();
                $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

                $budget = Budget::create([
                    'nome_cliente' => $validated['nome_cliente'],
                    'data_solicitacao' => $validated['data_solicitacao'],
                    'total' => 0,
                ]);

                $total = 0;

                foreach ($validated['itens'] as $item) {
                    $product = $products->get($item['product_id']);
                    $valorUnitario = (float) $product->valor;
                    $subtotal = $valorUnitario * (int) $item['quantidade'];

                    $budget->itens()->create([
                        'product_id' => $product->id,
                        'quantidade' => $item['quantidade'],
                        'valor_unitario' => $valorUnitario,
                        'subtotal' => $subtotal,
                    ]);

                    $total += $subtotal;
                }

                $budget->update(['total' => $total]);

                return $budget->load('itens.product');
            });

            return response()->json([
                'success' => true,
                'message' => 'Orçamento criado com sucesso.',
                'data' => $budget,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar orçamento.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        $budgets = Budget::with('itens.product')->orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'data' => $budgets,
        ], 200);
    }
}