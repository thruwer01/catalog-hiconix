<?php

namespace App\Orchid\Layouts\Series;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class SeriesTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'series';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', 'Название')->render( function($series) {
                return Link::make($series->name)
                    ->route('platform.series.edit', $series);
            }),
            TD::make('brand', 'Бренд')->render(function($series) {
                return $series->brand->name;
            }),
            TD::make('status', 'Статус')->render(function($series) {
                if ($series->status == 1) return 'Активная';
                if ($series->status == 0) return 'Архив';
            })
        ];
    }
}
