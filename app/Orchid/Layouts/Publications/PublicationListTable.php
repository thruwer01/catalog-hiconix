<?php

namespace App\Orchid\Layouts\Publications;

use App\Models\News;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PublicationListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'news';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('h1', 'Заголовок')
                ->render(function ($publication) {
                    return Link::make($publication->h1)->route('platform.publication.edit', $publication);
                }),
            TD::make('date', 'Дата'),
            TD::make('group', 'Группа')
                ->render(function (News $news) {
                    return $news->getHumanGroup();
                }),
        ];
    }
}
