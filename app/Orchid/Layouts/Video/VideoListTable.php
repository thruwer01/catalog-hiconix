<?php

namespace App\Orchid\Layouts\Video;

use App\Models\Product;
use App\Models\Video;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class VideoListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'videos';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('title', 'Название')
                ->render(function(Video $video) {
                    return Link::make($video->title)->route('platform.video.edit', $video);
                }),
            TD::make('description', 'Описание')
                ->render(function (Video $video) {
                    return trim(mb_substr($video->description,0,40))."..";
                })
        ];
    }
}
