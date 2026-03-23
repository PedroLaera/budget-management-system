<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = ['nomeCliente', 'data'];

    public function produtos()
    {
        return $this->hasMany(ProductOrcamento::class, 'orcamento_id');
    }
}