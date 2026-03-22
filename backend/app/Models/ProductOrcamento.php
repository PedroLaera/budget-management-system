<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrcamento extends Model
{
    protected $table = 'productoOrcamento';
    protected $fillable = ['orcamento_id', 'nome', 'valor'];
}