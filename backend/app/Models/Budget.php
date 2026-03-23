<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    protected $fillable = ['nome_cliente', 'data_solicitacao', 'total'];

    protected $casts = [
        'data_solicitacao' => 'date',
        'total' => 'decimal:2',
    ];

    public function itens(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }
}