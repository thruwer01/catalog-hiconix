<?php

namespace App\Orchid\Layouts;

use App\Orchid\Filters\PerPageFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class ProductPerPage extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            PerPageFilter::class,
        ];
    }
}
