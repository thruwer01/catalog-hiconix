<?php

namespace App\Orchid\Screens\Series;

use App\Models\Series;
use App\Orchid\Filters\BrandFilter;
use App\Orchid\Layouts\Series\SeriesFilterSelection;
use App\Orchid\Layouts\Series\SeriesTable;
use Orchid\Screen\Screen;

class SeriesListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            "series" => Series::filtersApply([BrandFilter::class])->defaultSort('id')->paginate(500)
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Серии';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            SeriesFilterSelection::class,
            SeriesTable::class,
        ];
    }
}
