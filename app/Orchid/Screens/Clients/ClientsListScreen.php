<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Clients;

use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserListLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ClientsListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'users' => User::with('roles')
                ->whereHas('roles', function (Builder $query) {
                    $query->where('slug', '=', 'client');
                })
                ->defaultSort('id', 'desc')
                ->paginate(30),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Clients';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'All registered clients';
    }

    /**
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            'platform.systems.clients',
        ];
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
                ->route('platform.clients.create'),
        ];
    }

    /**
     * Views.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('users', [
                TD::make('name', __('Name'))
                    ->cantHide()
                    ->render(function (User $user) {
                        return Link::make($user->name)->route('platform.clients.edit', $user);
                    }),

                TD::make('mailing_list', __('Email'))
                    ->cantHide(),

                TD::make('updated_at', __('Last edit'))
                    ->render(function (User $user) {
                        return $user->updated_at->toDateTimeString();
                    }),
            ])
        ];
    }

    /**
     * @param Request $request
     */
    public function remove(Request $request): void
    {
        User::findOrFail($request->get('id'))->delete();

        Toast::info(__('User was removed'));
    }
}
