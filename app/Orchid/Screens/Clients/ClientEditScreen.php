<?php

namespace App\Orchid\Screens\Clients;

use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

class ClientEditScreen extends Screen
{
    public $client;
    /**
     * Query data.
     *
     * @return array
     */
    public function query(User $user): iterable
    {
        return [
            "client" => $user
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->client->exists ? 'Редактировать клиента "' . $this->client->name .'"' : 'Создать клиента';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->icon('check')
                ->method('save'),
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
            Layout::block([
                Layout::rows([
                    Input::make('client.name')
                        ->title('Наименование')
                        ->type('text')
                        ->required(),
                    Input::make('client.mailing_list')
                        ->title('Почты для рассылок')
                        ->type('text')
                        ->help('Укажите почты с разделителем - запятая')
                        ->required(),
                    CheckBox::make('client.is_important')
                        ->title('Статус')
                        ->sendTrueOrFalse()
                        ->placeholder('Добавить в список "избранных"')
                        ->help('Клиенты с этим статусом получают выгрузку остатков и диллерские цены'),
                ]),
            ])->title('Основное'),
            Layout::block([
                Layout::rows([
                    Input::make('client.token')
                        ->type('text')
                        ->readonly()
                        ->title(__('Token')),
                    Group::make([
                        CheckBox::make('client.is_full_export')
                            ->title('Тип доступа')
                            ->placeholder('Доступ к закрытой части API')
                            ->sendTrueOrFalse(),
                    ]),
                ])
            ])->title('Права доступа')
                ->commands([
                    Button::make('Удалить токен')
                        ->type(Color::DEFAULT())
                        ->icon('trash')
                        ->confirm('Вы уверены, что хотите удалить токен клиента <b>' . $this->client->name . '</b>?')
                        ->method('removeToken'),

                    Button::make(__('Generate'))
                        ->type(Color::DEFAULT())
                        ->icon('reload')
                        ->confirm(__("Are you sure you want to generate a new token for user?"))
                        ->method('tokenUpdate')
                ]),
        ];
    }

    public function tokenUpdate(User $user): void
    {
        User::updateToken($user);
        Toast::info('Токен обновлен');
    }

    public function removeToken(User $user): void
    {
        $user->update(['token' => null]);
        Toast::info('Токен удален');
    }

    public function save(User $user, Request $request)
    {
        $mailing_list = (string)$request->collect('client')['mailing_list'];
        $email = explode(',', $mailing_list)[0];

        $client = $request->collect('client')->toArray();

        $client['email'] = $email;
        $client['is_important'] = (bool)$client['is_important'];

        $user->fill($client)->save();
        $user->replaceRoles([4]);
        Toast::info('Клиент обновлен');
        return redirect()->route('platform.clients');
    }
}
