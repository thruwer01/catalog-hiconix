<?php

namespace App\Orchid\Screens;

use App\Http\Controllers\SyncPriceController;
use App\Http\Controllers\SyncStockController;
use App\Models\Settings;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SettingsScreen extends Screen
{
    public $asyncResult = "";
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            "settings" => Settings::find(1),
            "result" => $this->asyncResult,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Настройки';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить изменения')
                ->icon('save')
                ->method('save')
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
                    Group::make([
                        CheckBox::make('settings.cron_sync_prices')
                            ->title('Обновлять цены по CRON')
                            ->sendTrueOrFalse(),
                        Button::make('Обновить цены вручную')
                            ->icon('refresh')
                            ->title('Принудительное обновление')
                            ->method('updatePrices')
                            ->confirm('Вы действительно хотите обновить цены вручную?'),
                    ]),
                    Group::make([
                        CheckBox::make('settings.cron_sync_stock')
                            ->title('Обновлять остатки по CRON')
                            ->sendTrueOrFalse(),
                        Button::make('Обновить остатки вручную')
                            ->icon('refresh')
                            ->modal('resultSyncModal')
                            ->title('Принудительное обновление')
                            ->confirm('Вы действительно хотите обновить остатки вручную?')
                            ->method('updateStock'),
                    ]),
                    
                ])
            ])->title('Настройки синхронизации')
            ->description('Синронизация цен и остатков из 1C по крону'),
            Layout::block([
                Layout::rows([
                    Group::make([
                        CheckBox::make('settings.show_products_on_site')
                        ->title('Показывать архивные товары на сайте')
                        ->sendTrueOrFalse(),
                    ])
                ])
            ]),
            Layout::block([
                Layout::rows([
                    Button::make('Сгенерировать sitemap.xml')
                ])
            ])->title('Управление sitemap на сайте HICONIX')
        ];
    }

    public function updateStock(Request $request)
    {
        $this->asyncResult = (new SyncStockController)->sync($request);
        Toast::info($this->asyncResult)->autoHide(false);
    }

    public function updatePrices(Request $request)
    {
        $this->asyncResult = (new SyncPriceController)->sync($request);
        Toast::info($this->asyncResult)->autoHide(false);
    }

    public function save(Request $request)
    {
        $fill = [];
        foreach ($request->collect('settings')->toArray() as $key => $value) {
            $fill[$key] = (bool)$value;   
        }

        Settings::find(1)->update($fill);
        Toast::info('Данные обновлены!');
    }
}
