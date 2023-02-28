<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "name"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    public static function forFilters($request)
    {
        $all = self::all();
        $filters = [];

        foreach($all as $brand)
        {
            $filters[$brand->id] = $brand->name;
        }

        return $filters;
    }
}
