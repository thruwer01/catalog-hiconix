<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOptionInSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        "product_id",
        "set_option_id",
        'created_at',
        'updated_at'
    ];
}
