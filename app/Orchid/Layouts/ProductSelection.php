<?php

namespace App\Orchid\Layouts;

use App\Orchid\Filters\BrandFilter;
use App\Orchid\Filters\CategoryFilter;
use App\Orchid\Filters\PerPageFilter;
use App\Orchid\Filters\SeriesFilter;
use App\Orchid\Filters\StatusFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class ProductSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            BrandFilter::class,
            CategoryFilter::class,
            SeriesFilter::class,
            StatusFilter::class,
            PerPageFilter::class
        ];
    }
}
