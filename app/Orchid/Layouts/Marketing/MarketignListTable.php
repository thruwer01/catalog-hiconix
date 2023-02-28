<?php

namespace App\Orchid\Layouts\Marketing;

use App\Models\MarketingBadge;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class MarketignListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'badges';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('title', 'Название')->render(function (MarketingBadge $badge) {
                return Link::make($badge->title)->route('platform.marketing.edit', $badge);
            }),
            TD::make('code', 'Код'),
            TD::make('Картинка')->render(function (MarketingBadge $badge) {
                if (count($badge->images()->get()) > 0) {
                    $attach = $badge->images()->get()->first();
                    $img = "storage/".$attach->path . $attach->name.".".$attach->extension;
                    return "<img height=\"40px\" src=".$img.">";
                }
            }),
            
        ];
    }
}
