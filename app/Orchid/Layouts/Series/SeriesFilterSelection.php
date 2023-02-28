<?php

namespace App\Orchid\Layouts\Series;

use App\Orchid\Filters\BrandFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class SeriesFilterSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            BrandFilter::class,
        ];
    }
}
