<?php

namespace App\Orchid\Screens;

use App\Models\News;
use App\Orchid\Layouts\Publications\PublicationListTable;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class PublicationList extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            "news" => News::orderBy('date', 'DESC')->paginate(20),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Публикации';
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
                ->route('platform.publication.create'),
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
            PublicationListTable::class,
        ];
    }
}
