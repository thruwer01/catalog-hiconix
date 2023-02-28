<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class ProducingCountry extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        "name",
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];
}
