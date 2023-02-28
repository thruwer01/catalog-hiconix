<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'code',
        'product_id',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        "created_at",
        "product_id",
        "id",
        "updated_at"
    ];
}
