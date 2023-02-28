<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Screen\AsSource;

class MarketingBadge extends Model
{
    use HasFactory, AsSource, Attachable;

    protected $fillable = [
        'code',
        'title',
        'color',
        'font_size',
        'inner_text'
    ];

    public function images()
    {
        return $this->attachment('marketing');
    }
}
