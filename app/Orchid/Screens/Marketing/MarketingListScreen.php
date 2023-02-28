<?php

namespace App\Orchid\Screens\Marketing;

use App\Models\MarketingBadge;
use App\Orchid\Layouts\Marketing\MarketignListTable;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class MarketingListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            "badges" => MarketingBadge::paginate(50)->sortBy('code', SORT_NATURAL),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Маркетинговые ярлыки';
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
                ->route('platform.marketing.create'),
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
            MarketignListTable::class,
        ];
    }
}
