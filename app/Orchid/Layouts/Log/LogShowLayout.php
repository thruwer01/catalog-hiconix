<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Log;

use App\Models\User;
use Illuminate\Support\Facades\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

class LogShowLayout extends Rows
{
    public $log_id = null;

    public function __construct($id)
    {
        $this->log_id = $id;
    }

    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Relation::make('log.user_id')
                ->fromModel(User::class, 'name')
                ->disabled(),

            Input::make('log.test')
                ->value($this->log_id),
        ];
    }
}
