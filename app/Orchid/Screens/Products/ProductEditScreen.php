<?php

namespace App\Orchid\Screens\Products;

use App\Models\BlockType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Series;
use App\Models\Video;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Illuminate\Support\Facades\DB;
use Orchid\Support\Facades\Toast;

class ProductEditScreen extends Screen
{
    public $product;
    /**
     * Query data.
     *
     * @return array
     */
    public function query(Product $product, Request $request): iterable
    {
        $product->load('attachment');
        return [
            'product' => $product,
            'request' => $request
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->product->getFullTitle();
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Remove'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->product->exists && Auth::user()->hasAccess('platform.systems.delete_products')),

            Button::make(__('Save'))
                ->icon('check')
                ->method('createOrUpdate'),
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
            Layout::tabs([
                "Основная информация" => [
                    Layout::columns([
                        Layout::rows([
                            Input::make('product.model')
                                ->title('Модель')
                                ->type('text'),
                            Input::make('product.article')
                                ->title('Артикул')
                                ->type('text'),
                            Relation::make('product.brand_id')
                                ->fromModel(Brand::class, 'name')
                                ->title('Бренд'),
                            Relation::make('product.category_id')
                                ->fromModel(Category::class, 'title')
                                ->title('Категория'),
                            Relation::make('product.videos')
                                ->fromModel(Video::class, 'title')
                                ->multiple()
                                ->title('Видео'),
                        ]),
                        Layout::rows([
                            Input::make('product.sort')
                                ->title('Сортировка')
                                ->type('number'),
                            Input::make('product.squere')
                                ->title('Площадь')
                                ->type('number'),
                            Relation::make('product.series_id')
                                ->fromModel(Series::class, 'name')
                                ->title('Серия'),
                            Relation::make('product.block_type_id')
                                ->fromModel(BlockType::class, 'name')
                                ->title('Тип блока'),
                        ]),
                    ])->canSee(Auth::user()->hasAccess('platform.seealltabs')),
                    Layout::rows([
                        Quill::make('product.model_description')
                        ->title('Описание модели'),
                        Quill::make('product.model_features')
                            ->title('Особенности модели'),
                    ])
                ],
                "Медиа-файлы" => [
                    Layout::rows([
                        Upload::make('product.docs')
                            ->groups('documents')
                            ->title('Документы')
                            ->maxFileSize(50)
                            ->acceptedFiles('.pdf, .xlsx, .xls, .txt, .ppt, .pptx, .doc, .docx, .png, .jpg, .jpeg')
                            ->help('Загрузите документы, доступно: .pdf, .xlsx, .xls, .txt, .ppt, .pptx, .doc, .docx, .png, .jpg, .jpeg'),
        
                        Upload::make('product.images')
                            ->groups('photo')
                            ->title('Изображения')
                            ->maxFileSize(5)
                            ->acceptedFiles('image/*')
                            ->help('Для загрузки картинок, доступно: .png, .jpg, .jpeg'),

                        /* Upload::make('product.videos')
                            ->groups('videos')
                            ->title('Видео-материалы')
                            ->maxFileSize(100)
                            ->acceptedFiles('.mp4')
                            ->help('Для загрузки видео, доступно: .mp4') */
                    ])
                    ]
            ])
        ];
    }

    public function createOrUpdate(Product $product, Request $request)
    {
        if(!is_null($request->get('product'))) {
            $product->fill($request->get('product'))->save();
            $product->videos()->detach();

            if (isset($request->get('product')['videos'])) {
                $product->videos()->attach($request->get('product')['videos']);
            }
            
            $files_ids_array = array_merge($request->input('product.images', []), $request->input('product.docs', []));
            $product->attachment()->syncWithoutDetaching($files_ids_array);
            Toast::info('Товар обновлен');
        }

        return redirect()->route('platform.products');
    }

    public function remove(Product $product, Request $request) {
        $productID = $product->id;
        DB::delete("DELETE FROM `product_sets` WHERE `product_id` = '$productID'");
        DB::delete("DELETE FROM `product_option_in_sets` WHERE `product_id` = '$productID'");
        DB::delete("DELETE FROM `product_option_no_in_sets` WHERE `product_id` = '$productID'");
        DB::delete("DELETE FROM `product_badges` WHERE `product_id` = '$productID'");
        DB::delete("DELETE FROM `properties_to_products` WHERE `product_id` = '$productID'");

        $product->delete();

        Toast::info('Товар успешно удален!');

        return redirect()->route('platform.products');
    }
}
