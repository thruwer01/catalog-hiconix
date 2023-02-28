<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * @param Dashboard $dashboard
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * @return Menu[]
     */
    public function registerMainMenu(): array
    {
        return [
            Menu::make(__('Products'))
                ->icon('database')
                ->route('platform.products')
                ->permission('platform.systems.products')
                ->title(__('Catalog')),
            
            Menu::make(('Серии'))
                ->route('platform.series.list')
                ->permission('platform.systems.series')
                ->icon('social-stumbleupon'),
            
            Menu::make('Видео')
                ->route('platform.video.list')
                ->permission('platform.video.list')
                ->icon('youtube'),

            Menu::make(('Категории'))
                ->permission('platform.systems.categories')
                ->route('platform.category.list')
                ->icon('database'),

            Menu::make(__('Clients list'))
                ->icon('user')
                ->route('platform.clients')
                ->permission('platform.systems.clients')
                ->title(__('Clients')),

            Menu::make(__('Log'))
                ->icon('list')
                ->route('platform.systems.log')
                ->permission('platform.systems.log')
                ->title(__('System')),

            Menu::make('Настройки')
                ->icon('settings')
                ->route('platform.systems.settings')
                ->permission('platform.systems.settings'),

            Menu::make('Маркетинговые ярлыки')
                ->icon('link')
                ->route('platform.marketing.list')
                ->permission('platform.systems.marketing'),

            Menu::make('Управление контентом')
                ->icon('info')
                ->title('Сайты')
                ->list([
                    Menu::make('Публикации')
                    ->route('platform.publication.list')
                ])
                ->permission('platform.sites.content'),

            Menu::make(__('Users'))
                ->icon('user')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access rights')),

            Menu::make(__('Roles'))
                ->icon('lock')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),
        ];
    }

    /**
     * @return Menu[]
     */
    public function registerProfileMenu(): array
    {
        return [
            Menu::make('Profile')
                ->route('platform.profile')
                ->icon('user'),
        ];
    }

    /**
     * @return ItemPermission[]
     */
    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users'))
                ->addPermission('platform.systems.log', __('Log'))
                ->addPermission('platform.systems.settings', 'Настройки')
                ->addPermission('platform.systems.marketing', 'Маркетинговые ярлыки')
                ->addPermission('platform.video.list', 'Видео'),
            ItemPermission::group(__('Catalog'))
                ->addPermission('platform.systems.clients', __('Clients'))
                ->addPermission('platform.systems.products', __('Products'))
                ->addPermission('platform.systems.exportandimport', 'Экспорт и импорт')
                ->addPermission('platform.systems.series', 'Серии')
                ->addPermission('platform.systems.categories', 'Категории')
                ->addPermission('platform.systems.delete_products', 'Удаление товаров'),
            ItemPermission::group(__('Clients'))
                ->addPermission('platform.systems.token', __('Update token'))
                ->addPermission('platform.systems.uploadSettings', __('Upload settings')),
            ItemPermission::group('Интерфейс')
                ->addPermission('platform.seealltabs', __('Расширенное редактирование товаров'))
                ->addPermission('platform.productstable.images', __('Картинки в таблице товаров'))
                ->addPermission('platform.productstable.documents', __('Документы в таблице товаров')),
            ItemPermission::group('Управление сайтами')
                ->addPermission('platform.sites.content', __('Управление контентом'))
            
        ];
    }
}
