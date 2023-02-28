<?php

namespace App\Exports;

use App\Models\MarketingBadge;
use App\Models\Product;
use App\Models\ProductProperty;
use App\Models\PropertiesToProduct;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{

    public $products;
    public $productsArray = [];
    public $propertiesNames = [];
    public $marketingNames = [];

    public function propetiesGroupsName()
    {
        $props = ProductProperty::all();
        foreach($props as $prop)
        {
            if ($prop->units !== null) {
                $fullName = $prop->name.", ".$prop->units;
            } else {
                $fullName = $prop->name;
            }

            $this->propertiesNames[] = $fullName;
        }
    }

    public function __construct()
    {
        ini_set('memory_limit','2G');
        set_time_limit(0);

        $this->propetiesGroupsName();
        $marketingBadgesCollection = MarketingBadge::all()->sortBy('code', SORT_NATURAL);
        foreach ($marketingBadgesCollection as $badge) {
            $this->marketingNames[] = "(".$badge->code.") ".$badge->title;
        }


        $this->products = Product::with(['category', 'brand', 'producing_country', 'series', 'blockType'])->get();
        
        foreach($this->products as $product)
        {
            $status = ""; //0 - не доступен(not_avaible) 1-актуальный(avaible) 2-под заказ(on_order) 3=ликвидация, 4=под заказ-2)
            if ($product->status === "not_avaible") $status = "0";
            if ($product->status === "avaible") $status = 1;
            if ($product->status === "on_order") $status = 2;
            if ($product->status === "liquidation") $status = 3;
            if ($product->status === "on_order2") $status = 4;

            $props = [];
            
            foreach (ProductProperty::all() as $prop) {
                $productProperty = PropertiesToProduct::where('product_id', $product->id)->where('property_id', $prop->id)->get();
                if (count($productProperty) == 0)
                {
                    $props[] = "";
                } else {
                    $props[] = (string)$productProperty->first()->property_value;
                }
            }

            $badges = [];

            $prodMarketingBadges = [];

            foreach($product->badges()->get()->toArray() as $badge) {
                $badges[] = $badge['code'];
                if ($badge['code'][0] == "s") {
                    $tempCode = substr($badge['code'], 1);
                    $prodMarketingBadges[intval($tempCode)] = $badge['code'];
                }
            }

            $lastKey = array_key_last($prodMarketingBadges);

            for ($i = 1; $i <= $lastKey; $i++) {
                if (!isset($prodMarketingBadges[$i])) $prodMarketingBadges[$i] = "";
            }

            ksort($prodMarketingBadges);

            natcasesort($badges);

            $parentTree = array_reverse($product->getParentTree());

            $this->productsArray[] = array_merge([
                $product->id,
                $product->site_id ? $product->site_id : null,
                $product->old_id ? $product->old_id : null,
                $product->brand->id,
                $product->brand->name,
                $product->article,
                isset($parentTree[0]) ? $parentTree[0] : "",
                isset($parentTree[1]) ? $parentTree[1] : "",
                isset($parentTree[2]) ? $parentTree[2] : "",
                isset($parentTree[3]) ? $parentTree[3] : "",
                $product->model,
                $product->category->id,
                $product->category->title,
                $product->series ? $product->series->id : null,
                $product->series ? $product->series->name : null,
                (string)$product->is_invertor,
                $product->producing_country->id,
                $product->producing_country->name,
                $product->squere,
                $product->inner_block_color,
                $product->model_description,
                $product->model_features,
                $status,
                $product->status_new,
                (string)$product->in_way,
                $product->blockType ? $product->blockType->id : "",
                $product->blockType ? $product->blockType->name : "",
                $product->ric_current,
                $product->wholesale_price,
                (string)$product->is_in_stock,
                $product->stock,
                $product->human_stock,
                $product->sort,
                implode(',', $product->sets()),
                implode(',', $product->options_in_sets()),
                implode(',', $product->options_not_in_sets()),
            ],
                $props,
                [
                    implode(',', $badges),
                    "/products/".$product->slug
                ],
                $prodMarketingBadges
            );
        }

    }

    public function array():array
    {
        return $this->productsArray;
    }
    
    public function headings():array
    {
        return array_merge([
            "Код товара",
            "Код site_id",
            "Код b2b",
            "Бренд ID",
            "Бренд",
            "Артикул",
            'Родитель 0',
            'Родитель 1',
            'Родитель 2',
            'Родитель 3',
            "Модель",
            "Категория ID",
            "Категория",
            "Серия ID",
            "Серия",
            "Инвертор",
            "КОД страны",
            "Страна производитель",
            "Обслуживаемая площадь",
            "Цвет внутреннего блока",
            "Описание модели",
            "Особенности и преимущества", 
            "Статус начальное значение (0=Товар не доступен к заказу, 1=Товар доступен к заказу, 2=Товар под заказ, 3=Ликвидация склада, 4=Товар под заказ)",
            "Статус текущее значение",
            "Товар в пути (0=нет, 1=да)",
            "Тип блока ID (1=наружний,2=внутренний,3=доп.оборуд)",
            "Тип блока",
            "РИЦ, руб",
            "Опт. цена (Базовая 60), руб",
            "В наличии (0=нет,1=да)",
            "Остаток",
            "Склад",
            "Сортировка",
            "Комплекты",
            "Опции в комплекте",
            "Опции не в комплекте",
        ],

            $this->propertiesNames,
            [
                "Маркетинговые ссылки",
                "Ссылка на товар"
            ],
            $this->marketingNames,
        );
    }

    public function styles(Worksheet $sheet):array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFAF0'],
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 20,
            'D' => 20,
            'E' => 30,
            'F' => 15,
            'G' => 30,
            'H' => 15,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 40,
            'M' => 40,
            'N' => 20,
            'O' => 15,
            'P' => 20,
            'Q' => 20,
            'R' => 25,
            'S' => 20,
            'T' => 20,
            'U' => 25,
            'V' => 20,
            'W' => 30,
            'X' => 30,
        ];
    }
}
