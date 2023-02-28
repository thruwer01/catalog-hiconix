<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertiesToProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        "product_id",
        "property_id",
        "property_value"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];
}
