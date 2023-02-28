<?php

namespace App\Orchid\Screens;

use App\Models\Video;
use App\Orchid\Layouts\Video\VideoListTable;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class VideoList extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            "videos" => Video::paginate(20)
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Все видео';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('plus')
                ->route('platform.video.create'),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            VideoListTable::class,
        ];
    }
}
