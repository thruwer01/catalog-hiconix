<?php

namespace App\Orchid\Screens\Products;

use App\Http\Controllers\SeriesImportController;
use App\Http\Controllers\v1\CategoriesImportController;
use App\Http\Controllers\v1\ProductImportController;
use App\Models\Category;
use App\Models\Product;
use App\Orchid\Filters\BrandFilter;
use App\Orchid\Filters\CategoryFilter;
use App\Orchid\Filters\SeriesFilter;
use App\Orchid\Filters\StatusFilter;
use App\Orchid\Layouts\ProductPerPage;
use App\Orchid\Layouts\Products\ProductsListLayout;
use App\Orchid\Layouts\ProductSelection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ProductListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        return [
            'products' => Product::filtersApply([BrandFilter::class, CategoryFilter::class, SeriesFilter::class, StatusFilter::class])
                            ->defaultSort('id')
                            ->with(['category', 'brand', 'producing_country', 'series'])
                            ->paginate(!is_null($request->query('per_page')) ?(int)$request->query('per_page'): 500)
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Products');
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Excel')
                ->modal('excelModal')
                ->icon('faw.file-excel')
                ->canSee(Auth::user()->hasAccess('platform.systems.exportandimport'))
                ->method('import')
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        // Toast::warning('test');

        return [
            Layout::modal('excelModal', [
                Layout::tabs([
                    'Товары' => Layout::columns([
                        Layout::rows([
                            Link::make('Скачать таблицу товаров')->route('platform.export.products')->download()->icon('save')->title('Экспорт'),
                        ]),
                        Layout::rows([
                            Upload::make('products')
                                ->title('Импорт')
                                ->groups('import-products')
                                ->help("Загрузите таблицу товаров")
                                ->maxFiles(1)
                                ->acceptedFiles('.xlsx'),
                        ])
                    ]),
                    'Родители' => Layout::columns([
                        Layout::rows([
                            Link::make('Скачать таблицу категорий')->route('platform.export.categories')->download()->icon('save')->title('Экспорт'),
                            Link::make('Скачать таблицу серий')->route('platform.export.series')->download()->icon('save')
                        ]),
                        Layout::rows([
                            Upload::make('categories')
                                ->title('Импорт')
                                ->help("Загрузите таблицу категорий")
                                ->maxFiles(1)
                                ->acceptedFiles('.xlsx'),
                            Upload::make('series')
                                ->title('Импорт')
                                ->help("Загрузите таблицу серий")
                                ->maxFiles(1)
                                ->acceptedFiles('.xlsx'),
                        ])
                    ])
                ])
            ])
            ->size(Modal::SIZE_LG)
            ->title('Excel - экспорт и импорт')
            ->applyButton('Загрузить'),
            ProductSelection::class,
            ProductsListLayout::class,
        ];
    }

    public function import(Request $request)
    {
        $importCategoriesFile = $request->input('categories', []);
        $importSeriesFile = $request->input('series', []);
        $importProductsFile = $request->input('products', []);

        if (count($importSeriesFile) === 1) {
            //обновляем серии
            $attachID = $importSeriesFile[0];
            $attach = Attachment::find($attachID);
            $attachPath = storage_path("app/public/".$attach->path . $attach->name . "." . $attach->extension);
            (new SeriesImportController)->import($attachPath);
        } 
        if (count($importCategoriesFile) === 1) {
            //обновляем категорий
            $attachID = $importCategoriesFile[0];
            $attach = Attachment::find($attachID);
            $attachPath = storage_path("app/public/".$attach->path . $attach->name . "." . $attach->extension);
            (new CategoriesImportController)->import($attachPath);
        }

        if (count($importProductsFile) === 1) {
            //обновляем товары
            $attachID = $importProductsFile[0];
            $attach = Attachment::find($attachID);
            $attachPath = storage_path("app/public/".$attach->path . $attach->name . "." . $attach->extension);
            (new ProductImportController)->import($attachPath);
        }
        Toast::info('Импорт завершен, информацию смотрите в лог-журнале.');
    }
}
