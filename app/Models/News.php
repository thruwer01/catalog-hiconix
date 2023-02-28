<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class News extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        "anons_text",
        "code",
        "is_active",
        "group",
        "date",
        "show_on_index",
        "html",
        "h1",
        "content_img",
        "anons_img"
    ];

    public function getHumanGroup()
    {
        if ($this->group == "actions") {
            return "Акция";
        }
        if ($this->group == "articles") {
            return "Статья";
        }
        if ($this->group == "news") {
            return "Новость";
        }
        if ($this->group == "video") {
            return "Видео";
        }

        return null;
    }
}
