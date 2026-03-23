<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('nomeCliente', 45);
            $table->string('data', 45);
            $table->timestamps();
        });

        Schema::create('productoOrcamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orcamento_id')->constrained('budgets')->onDelete('cascade');
            $table->string('nome', 45);
            $table->string('valor', 45);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productoOrcamento');
        Schema::dropIfExists('budgets');
    }
};