<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Logger extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        "user_id",
        "id",
        "object",
        "action",
        "ip",
        "created_at",
        "updated_at"
    ];

    protected $hiddent = [
        "updated_at",
    ];
}
