<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Category extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        "id",
        "title",
        "parent_id",
        "link",
        "is_private",
        "sort",
        "is_active_hiconix",
        "is_menu_hiconix",
        "html_description_header",
        "html_description_footer",
        "html_description_footer_second",
        "img_preview_path",
        "filter_string",
        "product_prefix",
        "temp_name",
        "meta_title",
        "meta_description",
        "meta_keys",
        "compatibility"
    ];

    protected $hidden = [
        "updated_at",
        "created_at"
    ];

    public static function forFilters()
    {
        $all = self::all();
        $filters = [];

        foreach($all as $category)
        {
            $filters[$category->id] = $category->title;
        }

        return $filters;
    }
}
