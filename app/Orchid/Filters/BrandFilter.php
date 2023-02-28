<?php

namespace App\Orchid\Filters;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class BrandFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Бренд';
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['brand'];
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        return $builder->where('brand_id', $this->request->get('brand'));
    }

    /**
     * @return Field[]
     */
    public function display() : array
    {
        return [
            Select::make('brand')
                ->options(Brand::forFilters($this->request))
                ->empty()
                ->value($this->request->get('brand'))
                ->title('Бренд')
        ];
    }
}
