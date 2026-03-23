<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_produtos(): void
    {
        Product::factory()->create(['nome' => 'Notebook', 'valor' => 3500]);

        $response = $this->getJson('/api/products');

        $response
            ->assertOk()
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_cria_orcamento_com_itens(): void
    {
        $produto = Product::create([
            'nome' => 'Mouse',
            'valor' => 150.00,
        ]);

        $payload = [
            'nome_cliente' => 'Pedro',
            'data_solicitacao' => '2026-03-22',
            'itens' => [
                ['product_id' => $produto->id, 'quantidade' => 2],
            ],
        ];

        $response = $this->postJson('/api/budgets', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total', '300.00');
    }
}