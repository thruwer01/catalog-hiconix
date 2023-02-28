<?php

declare(strict_types=1);

use App\Http\Controllers\AntiDublesController;
use App\Http\Controllers\CategoriesExportController;
use App\Http\Controllers\ClientsImportController;
use App\Http\Controllers\DeleteAttach\DeleteDocumentsController;
use App\Http\Controllers\DeleteAttach\DeleteImageController;
use App\Http\Controllers\Export1CController;
use App\Http\Controllers\ForAnyTestController;
use App\Http\Controllers\ProductExportController;
use App\Http\Controllers\ProductFinal\ProductFinalImportController;
use App\Http\Controllers\SeriesExportController;
use App\Http\Controllers\SyncMediaFilesController;
use App\Http\Controllers\SyncPriceController;
use App\Http\Controllers\SyncProductMediaController;
use App\Http\Controllers\SyncStockController;
use App\Http\Controllers\UpdateProductFilesController;
use App\Http\Controllers\UpdateProductMarketingController;
use App\Http\Controllers\UploadNewDocumentsController;
use App\Http\Controllers\v1\ProductController;
use App\Http\Controllers\v1\ProductImportController;
use App\Orchid\Screens\Clients\ClientEditScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\Clients\ClientsListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;
use App\Http\Controllers\v1\TestImportController;
use App\Orchid\Screens\Category\CategoryEditScreen;
use App\Orchid\Screens\Category\CategoryListScreen;
use App\Orchid\Screens\LogScreen;
use App\Orchid\Screens\Marketing\MarketingBadgeEditScreen;
use App\Orchid\Screens\Marketing\MarketingListScreen;
use App\Orchid\Screens\Products\ProductEditScreen;
use App\Orchid\Screens\Products\ProductListScreen;
use App\Orchid\Screens\PublicationList;
use App\Orchid\Screens\Publications\PublicationEditScreen;
use App\Orchid\Screens\Series\SeriesEditScreen;
use App\Orchid\Screens\Series\SeriesListScreen;
use App\Orchid\Screens\SettingsScreen;
use App\Orchid\Screens\Video\VideoEditScreen;
use App\Orchid\Screens\VideoList;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Profile'), route('platform.profile'));
    });

// Platform > System > Users
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(function (Trail $trail, $user) {
        return $trail
            ->parent('platform.systems.users')
            ->push(__('User'), route('platform.systems.users.edit', $user));
    });

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.users')
            ->push(__('Create'), route('platform.systems.users.create'));
    });

// Platform > System > Users > User
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Users'), route('platform.systems.users'));
    });

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(function (Trail $trail, $role) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Role'), route('platform.systems.roles.edit', $role));
    });

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Create'), route('platform.systems.roles.create'));
    });

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Roles'), route('platform.systems.roles'));
    });

// Platform > Products
Route::screen('products', ProductListScreen::class)
    ->name('platform.products')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Products'), route('platform.products'));
    });

// Platform > Products > Edit
Route::screen('products/{product}/edit', ProductEditScreen::class)
    ->name('platform.products.edit')
    ->breadcrumbs(function (Trail $trail, $product) {
        return $trail
            ->parent('platform.products')
            ->push('Редактировать товар', route('platform.products.edit', $product));
    });

// Platform > Clients
Route::screen('clients', ClientsListScreen::class)
->name('platform.clients')
->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.index')
        ->push(__('Clients'), route('platform.clients'));
});

// Platform > Clients > Edit
Route::screen('clients/{client}/edit', ClientEditScreen::class)
    ->name('platform.clients.edit')
    ->breadcrumbs(function (Trail $trail, $client) {
        return $trail
            ->parent('platform.clients')
            ->push(__('Клиент'), route('platform.clients.edit', $client));
    });

// Platform > System > Clients > Create
Route::screen('clients/create', ClientEditScreen::class)
    ->name('platform.clients.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.clients')
            ->push(__('Create'), route('platform.clients.create'));
    });

// Platform > System > Settings
Route::screen('settings', SettingsScreen::class)
    ->name('platform.systems.settings')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push('Настройки', route('platform.systems.settings'));
    });

// Platform > System > Log
Route::screen('log', LogScreen::class)
    ->name('platform.systems.log')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push('Лог', route('platform.systems.log'));
    });

// Platform > System > Export Excel Products
Route::get('/export_products', [ProductExportController::class, 'export'])
    ->name('platform.export.products');

// Platform > System > Export Excel Categories
Route::get('/export_categories', [CategoriesExportController::class, 'export'])
    ->name('platform.export.categories');

// Platform > System > Export Excel Categories
Route::get('/export_series', [SeriesExportController::class, 'export'])
    ->name('platform.export.series');

// Delete Product Attachments Images
Route::post('/attachments/images', [DeleteImageController::class, 'delete']);
Route::post('/attachments/documents', [DeleteDocumentsController::class, 'delete']);

// Platform > Series > List
Route::screen('series', SeriesListScreen::class)
    ->name('platform.series.list')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push('Серии', route('platform.series.list'));
    });

// Platform > Series > Edit
Route::screen('series/{series}/edit', SeriesEditScreen::class)
    ->name('platform.series.edit')
    ->breadcrumbs(function (Trail $trail, $series) {
        return $trail
            ->parent('platform.series.list')
            ->push('Редактировать серию', route('platform.products.edit', $series));
    });

    

// Platform > Marketing > List
Route::screen('marketing', MarketingListScreen::class)
    ->name('platform.marketing.list')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push('Маркетинговые ярлыки', route('platform.marketing.list'));
    });

// Platform > Marketing > Edit
Route::screen('marketing/{badge}/edit', MarketingBadgeEditScreen::class)
    ->name('platform.marketing.edit')
    ->breadcrumbs(function (Trail $trail, $badge) {
        return $trail
            ->parent('platform.marketing.list')
            ->push('Маркетинговый ярлык', route('platform.marketing.edit', $badge));
    });

// Platform > Marketing > Create
Route::screen('marketing/create', MarketingBadgeEditScreen::class)
    ->name('platform.marketing.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.marketing.list')
            ->push(__('Create'), route('platform.marketing.create'));
    });

// Platform > Category > List
Route::screen('categories', CategoryListScreen::class)
    ->name('platform.category.list')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push('Категории', route('platform.category.list'));
    });

// Platform > Category > Edit
Route::screen('categories/{badge}/edit', CategoryEditScreen::class)
    ->name('platform.category.edit')
    ->breadcrumbs(function (Trail $trail, $badge) {
        return $trail
            ->parent('platform.category.list')
            ->push('Редактировать категорию', route('platform.category.edit', $badge));
    });

// Platform > Video > List
Route::screen('videos', VideoList::class)
    ->name('platform.video.list')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push('Видео', route('platform.video.list'));
    });

// Platform > Video > Edit
Route::screen('videos/{video}/edit', VideoEditScreen::class)
    ->name('platform.video.edit')
    ->breadcrumbs(function (Trail $trail, $video) {
        return $trail
            ->parent('platform.video.list')
            ->push('Редактировать видео', route('platform.category.edit', $video));
    });

// Platform > Video > Create
Route::screen('videos/create', VideoEditScreen::class)
    ->name('platform.video.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.video.list')
            ->push(__('Create'), route('platform.video.create'));
    });

// Platform > Publication > List
Route::screen('publications', PublicationList::class)
    ->name('platform.publication.list')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push('Публикации', route('platform.publication.list'));
    });

// Platform > Publication > Edit
Route::screen('publications/{publication}/edit', PublicationEditScreen::class)
    ->name('platform.publication.edit')
    ->breadcrumbs(function (Trail $trail, $publication) {
        return $trail
            ->parent('platform.publication.list')
            ->push('Редактировать публикацию', route('platform.publication.edit', $publication));
    });

// Platform > Publication > Create
Route::screen('publications/create', PublicationEditScreen::class)
    ->name('platform.publication.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.publication.list')
            ->push('Создать публикацию', route('platform.publication.create'));
    });

// Route::get('/import_caregories_and_series', [TestImportController::class, 'import']);
// Route::get('/import_products', [ProductImportController::class, 'import']);
// Route::get('/anti_dubles', [AntiDublesController::class, 'anti']);
// Route::get('/import_marketing', [UpdateProductMarketingController::class, 'import']);
// Route::get('/import_clients', [ClientsImportController::class, 'import']);
// Route::get('/product_photos', [SyncProductMediaController::class, 'sync']);
// Route::get('/product_documents', [UploadNewDocumentsController::class, 'sync']);

Route::get('/123_test', [ProductFinalImportController::class, 'test']);
Route::get('/slug', [ForAnyTestController::class, 'createSlugController']);
Route::get('/series_slug', [ForAnyTestController::class, 'createSeriesLink']);

// Route::get('/test_product_route/{id}', [ProductController::class, 'show']);
// Route::get('/update_files', [UpdateProductFilesController::class, 'sync']);

// Route::get('/export_store', [Export1CController::class, 'export']);