<?php

namespace App\Orchid\Layouts\Log;

use App\Models\Logger;
use App\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class LogTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'logs';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make("user_id", 'Пользователь')
                ->render(function (Logger $log) {
                    if ($log->user_id) {
                        return Link::make(User::find($log->user_id)->name)->route('platform.systems.users.edit', $log->user_id);
                    } else {
                        return "<div style=\"padding: .25rem .5rem\"> $log->object</div>";
                    }
                }),
            TD::make("action", 'Событие')
                ->render(function (Logger $log) {
                    return $log->action;
                }),
            TD::make("created_at", 'Дата')
                ->render(function (Logger $log) {
                    return $log->created_at->format('H:i:s d.m.Y');
                }),
        ];
    }
}
