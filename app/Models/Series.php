<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;

class Series extends Model
{
    use HasFactory, Filterable;

    protected $allowedFilters = [
        "brand_id"
    ];

    protected $allowedSorts = [
        "id"
    ];

    protected $fillable = [
        "id",
        "brand_id",
        "name",
        "link",
        "meta_title",
        "meta_desc",
        "meta_keys",
        "meta_title_ecoclima",
        "meta_desc_ecoclima",
        "meta_keys_ecoclima",
        "meta_title_auxair",
        "meta_desc_auxair",
        "meta_keys_auxair",
        "h1_content",
        "status",
        "html_description",
        "html_description_other",
        "html_features"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    public static function forFilters()
    {
        $all = self::all();
        $filters = [];

        foreach($all as $series)
        {
            $filters[$series->id] = $series->name;
        }

        return $filters;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
