<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Upload;

class ProductsListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'products';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', 'Модель')->render(function (Product $product) {
                return Link::make($product->model)
                    ->route('platform.products.edit', $product->id);
            })->width('200px'),
            TD::make('brand', 'Бренд')->render(function (Product $product) {
                return $product->brand ? $product->brand->name : null;
            })->width('200px'),
            TD::make('category', 'Категория')->render(function (Product $product) {
                return $product->category ? $product->category->title : null;
            })->width('200px'),
            TD::make('series', 'Серия')->render(function (Product $product) {
                return $product->series ?  $product->series->name : null;
            })->width('200px'),
            TD::make('block_type', 'Тип блока')->render(function (Product $product) {
                return $product->block_type()->get()->first()?->name;
            })->width('400px'),
            TD::make('attach', 'Картинки')->render(function (Product $product) {
                $attachments = [];

                foreach ($product->attachment('photo')->get() as $attach) {
                    $attachments[] = "
                        <div class=\"d-inline-flex align-items-center\" style=\"position:relative;\"data-id-attach=\"$attach->id\" data-id-product=\"$product->id\" >
                            <img class=\"p-1\" src=\"storage/".$attach->path . $attach->name.".".$attach->extension . "\" height=\"45px\">
                            <svg id=\"imgAttach\" style=\"position:absolute; border-radius: 100%; top: 0; right: 0; cursor: pointer; display:none;\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 32 32\" class=\"ms-1 border bg-light\" width=\"1em\" height=\"1em\" role=\"img\" fill=\"currentColor\" componentname=\"orchid-icon\">
                                <path d=\"M18.8,16l5.5-5.5c0.8-0.8,0.8-2,0-2.8l0,0C24,7.3,23.5,7,23,7c-0.5,0-1,0.2-1.4,0.6L16,13.2l-5.5-5.5  c-0.8-0.8-2.1-0.8-2.8,0C7.3,8,7,8.5,7,9.1s0.2,1,0.6,1.4l5.5,5.5l-5.5,5.5C7.3,21.9,7,22.4,7,23c0,0.5,0.2,1,0.6,1.4  C8,24.8,8.5,25,9,25c0.5,0,1-0.2,1.4-0.6l5.5-5.5l5.5,5.5c0.8,0.8,2.1,0.8,2.8,0c0.8-0.8,0.8-2.1,0-2.8L18.8,16z\"></path>
                            </svg>
                        </div>
                    ";
                }

                return implode("",$attachments);
            })->width('400px')->canSee(Auth::user()->hasAccess('platform.productstable.images')),
            TD::make('attach-docs', 'Документы')->render(function(Product $product) {
                $docs = [];
                $documents = $product->attachment('documents')->get()->sortBy('size', SORT_REGULAR, true);

                foreach($documents as $doc)

                $docs[] = "<div class=\"badge bg-light border d-block m-1 text-start\" data-id-attach=\"$doc->id\" data-id-product=\"$product->id\" >
                                $doc->original_name (". round($doc->size/1024/1024, 2) ." МБ)
                                <svg id=\"docAttach\" style=\"cursor: pointer; display:none;\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 32 32\" class=\"ms-1\" width=\"1em\" height=\"1em\" role=\"img\" fill=\"currentColor\" componentname=\"orchid-icon\">
                                    <path d=\"M18.8,16l5.5-5.5c0.8-0.8,0.8-2,0-2.8l0,0C24,7.3,23.5,7,23,7c-0.5,0-1,0.2-1.4,0.6L16,13.2l-5.5-5.5  c-0.8-0.8-2.1-0.8-2.8,0C7.3,8,7,8.5,7,9.1s0.2,1,0.6,1.4l5.5,5.5l-5.5,5.5C7.3,21.9,7,22.4,7,23c0,0.5,0.2,1,0.6,1.4  C8,24.8,8.5,25,9,25c0.5,0,1-0.2,1.4-0.6l5.5-5.5l5.5,5.5c0.8,0.8,2.1,0.8,2.8,0c0.8-0.8,0.8-2.1,0-2.8L18.8,16z\"></path>
                                </svg>
                            </div>";

                return implode('', $docs);

            })->width('300px')->canSee(Auth::user()->hasAccess('platform.productstable.documents')),
        ];
    }
}
