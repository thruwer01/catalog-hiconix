<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Video extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        "title",
        "description",
        "youtube_url"
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

}
