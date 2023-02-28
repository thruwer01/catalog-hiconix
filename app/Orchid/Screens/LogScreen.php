<?php

namespace App\Orchid\Screens;

use App\Models\Logger;
use App\Models\News;
use App\Orchid\Layouts\Log\LogShowLayout;
use App\Orchid\Layouts\Log\LogTable;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Sight;

class LogScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            "logs" => Logger::orderBy('created_at', 'DESC')->paginate(50),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Лог';
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
            LogTable::class
        ];
    }
}
