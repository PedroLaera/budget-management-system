<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BudgetController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nomeCliente'       => 'required|string|max:45',
                'data'              => 'required|string|max:45',
                'produtos'          => 'required|array|min:1',
                'produtos.*.nome'   => 'required|string|max:45',
                'produtos.*.valor'  => 'required|string|max:45',
            ], [
                'nomeCliente.required'      => 'O nome do cliente é obrigatório.',
                'data.required'             => 'A data é obrigatória.',
                'produtos.required'         => 'Adicione ao menos um produto.',
                'produtos.min'              => 'Adicione ao menos um produto.',
                'produtos.*.nome.required'  => 'O nome do produto é obrigatório.',
                'produtos.*.valor.required' => 'O valor do produto é obrigatório.',
            ]);

            $budget = DB::transaction(function () use ($validated) {
                $budget = Budget::create([
                    'nomeCliente' => $validated['nomeCliente'],
                    'data'        => $validated['data'],
                ]);

                foreach ($validated['produtos'] as $produto) {
                    $budget->produtos()->create([
                        'nome'  => $produto['nome'],
                        'valor' => $produto['valor'],
                    ]);
                }

                return $budget->load('produtos');
            });

            return response()->json([
                'success' => true,
                'message' => 'Orçamento criado com sucesso.',
                'data'    => $budget,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar orçamento.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}