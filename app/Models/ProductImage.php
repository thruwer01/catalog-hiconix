<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'url',
        'file_name'
    ];

    protected $hidden = [
        "product_id",
        "created_at",
        "updated_at"
    ];
}
