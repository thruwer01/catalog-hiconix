<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Settings extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        "cron_sync_stock",
        "cron_sync_prices",
        "show_products_on_site"
    ];
}
