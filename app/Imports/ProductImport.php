<?php

namespace App\Imports;

use App\Http\Controllers\ForAnyTestController;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Logger;
use App\Models\ProducingCountry;
use App\Models\Product;
use App\Models\ProductBadge;
use App\Models\ProductOptionInSet;
use App\Models\ProductOptionNoInSet;
use App\Models\ProductSet;
use App\Models\PropertiesToProduct;
use App\Models\Series;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection
{
    public $warnings = [];

    public $updated = 0;
    public $created = 0;
    public $user = null;

    public $productComplects = [];
    public $productOptionsInComplect = [];
    public $productOptionsNoInComplect = [];

    public function collection(Collection $rows)
    {
        $start = microtime(true);

        foreach ($rows as $index => $row)
        {
            if ($index < 1) continue 1;
            
            if (!isset($row[11])) {
                $this->warnings[] = "Вы пытаетесь создать/обновить товар, не указав в нем родительскую категорию - это фатальная ошибка! Строка $index";
                continue 1;
            } else {
                $categoryID = $row[11];
                if (!Category::find($categoryID)) {
                    $this->warnings[] = "Не удалось найти категорию с ID $categoryID, для начала его необходимо ее создать! Строка $index";
                    continue 1;
                }
            }

            $tryGetBrand = Brand::where('name', $row[4])->get();
            if (count($tryGetBrand) > 0) 
            {
                $brand_id = $tryGetBrand->first()->id;
            } else {
                $brand_id = Brand::create(["name" => $row[4]])->id;
                $this->warnings[] = "Не удалось найти бренд с названием " . $row[4] . ". Он был создан автоматически";
            }

            $tryGetCountry = ProducingCountry::where('name', $row[17])->get();
            if (count($tryGetCountry) > 0)
            {
                $countryID = $tryGetCountry->first()->id;
            } else {
                $countryID = ProducingCountry::create(['name' => $row[17]])->id;
                $this->warnings[] = "Не удалось найти страницу-производитель с названием ". $row[17] . ". Она была создана автоматически";
            }

            $seriesID = null;
            if (!is_null($row[14])) {
                $tryGetSeries = Series::where('name', $row[14])->get();

                if (count($tryGetSeries) > 0) {
                    $seriesID = $tryGetSeries->first()->id;
                } else {
                    $this->warnings[] = "Серия с названием ". $row[14] ." не была найдена. Товары этой серии добавлены без нее.";
                }
            }
            
            $status = null;
            $status_new = null;

            $productID = $row[0];

            $product = Product::find($productID);

            if(isset($row[22])) {
                $statusID = $row[22];

                if ($statusID == 0) {
                    $status = "not_avaible";
                    $status_new = 0;
                }
                if ($statusID == 1) {
                    $status = "avaible";
                    $status_new = 1;
                    if ($product) {
                        if ($product->stock <= 0) {
                            $status_new = 2;
                        }
                    }
                }
                if ($statusID == 2) {
                    $status = "on_order";
                    $status_new = 2;
                    if ($product) {
                        if ($product->stock <= 0) {
                            $status_new = 2;
                        } else {
                            $status_new = 1;
                        }
                    }
                };

                if ($statusID == 3) {
                    $status = "liquidation";
                    if ($product) {
                        if ($product->stock <= 0) {
                            $status_new = 0;
                        } else {
                            $status_new = 3;
                        }
                    }
                }
                if ($statusID == 4) {
                    $status = "on_order2";
                    $status_new = 4;
                    if ($product) {
                        if ($product->stock <= 0) {
                            $status_new = 4;
                        } else {
                            $status_new = 1;
                        }
                    }
                };

            } else {
                $status = null;
                $status_new = 1;
            }

            $productData = [
                "site_id" => $row[1],
                "old_id" => $row[2],
                "brand_id" => $brand_id,
                "article" => $row[5],
                "model" => $row[10],
                "category_id" => $categoryID,
                "series_id" => $seriesID,
                "is_invertor" => $row[15] === 1 ? true : false,
                "producing_country_id" => $countryID,
                "squere" => (int)$row[18],
                "inner_block_color" => $row[19],
                "model_description" => $row[20],
                "model_features" => $row[21],
                "status" => $status,
                "status_new" => $status_new,
                "in_way" => $row[24] === 1 ? true : false,
                "block_type_id" => $row[25],
                "sort" => $row[32],
            ];


            //характеристики начинаются с row[36]
            $properties = [];

            for($i = 1; $i <= 49; $i++) {
                $rowID = $i-1+36;
                $properties[$i] = strval($row[$rowID]);
            }

            //маркетинговые ярлыки            
            $badgeArr = [];

            if ($row[85] != null) {
                $badgeArr = explode(',',$row[85]);
            }
            
            $badgeArr = array_unique($badgeArr);

            if ($productID == null) {
                //создаем товар
                $productID = $this->createProduct($productData, $properties, $badgeArr);
            } else {

                if (!Product::find($productID)) {
                    $this->warnings[] = "Не удалось найти товар с ID $productID и обновить его, для начала его необходимо создать! Строка $index";
                    continue 1;
                }
                
                $this->updateProduct($productData, $productID, $properties, $badgeArr);
            }

            //Комплекты
            $complects = explode(',', $row[33]);
            if (count($complects) > 0) {
                foreach ($complects as $pComplectID)
                {
                    if ($pComplectID !== "") {
                        $this->productComplects[intval($productID)][] = intval($pComplectID);
                        $this->productComplects[intval($pComplectID)][] = intval($productID);
                    }
                }
            }

            //Опции в комплекте
            $optionsInComplects = explode(',', $row[34]);
            if (count($optionsInComplects) > 0) {
                foreach ($optionsInComplects as $pOptionID)
                {
                    if ($pOptionID !== "") {
                        $this->productOptionsInComplect[intval($productID)][] = intval($pOptionID);
                        $this->productOptionsInComplect[intval($pOptionID)][] = intval($productID);
                    }
                }
            }

            //Опции не в комплекте
            $optionsNoInComplects = explode(',', $row[35]);
            if (count($optionsNoInComplects) > 0) {
                foreach ($optionsNoInComplects as $pOptionID)
                {
                    if ($pOptionID !== "") {
                        $this->productOptionsNoInComplect[intval($productID)][] = intval($pOptionID);
                        $this->productOptionsNoInComplect[intval($pOptionID)][] = intval($productID);
                    }
                }
            }
        }

        //Создаем новые пары комплектов
        DB::delete("DELETE FROM `product_sets`");
        foreach ($this->productComplects as $productComplectID => $productComplectPairs)
        {
            foreach($productComplectPairs as $set_id)
            {
                $set_id = intval($set_id);
            }
            $complectIDs = array_unique($productComplectPairs);
            foreach($complectIDs as $set_id)
            {
                $set_id = intval($set_id);
                $productComplectID = intval($productComplectID);
                if (!Product::find($set_id)) {
                    if (count(Product::where('old_id', $set_id)->get()) > 0) {
                        $set_id = Product::where('old_id', $set_id)->get()->first()->id;
                    } else {
                        continue 1;
                    }
                }
                if (!Product::find($productComplectID)) {
                    if (count(Product::where('old_id', $set_id)->get()) > 0) {
                        $productComplectID = Product::where('old_id', $set_id)->get()->first()->id;
                    } else {
                        continue 1;
                    }
                }
                ProductSet::create(['product_id' => $productComplectID, 'set_product_id' => $set_id]);
            }
        }
        DB::delete("DELETE FROM `product_option_in_sets`");
        foreach ($this->productOptionsInComplect as $productComplectID => $productComplectPairs)
        {
            foreach($productComplectPairs as $set_id)
            {
                $set_id = intval($set_id);
            }
            $complectIDs = array_unique($productComplectPairs);
            foreach($complectIDs as $set_id)
            {
                $set_id = intval($set_id);
                $productComplectID = intval($productComplectID);
                if (!Product::find($set_id)) {
                    if (count(Product::where('old_id', $set_id)->get()) > 0) {
                        $set_id = Product::where('old_id', $set_id)->get()->first()->id;
                    }  else {
                        continue 1;
                    }
                }
                if (!Product::find($productComplectID)) {
                    if (count(Product::where('old_id', $set_id)->get()) > 0) {
                        $productComplectID = Product::where('old_id', $set_id)->get()->first()->id;
                    }  else {
                        continue 1;
                    }
                }
                ProductOptionInSet::create(['product_id' => $productComplectID, 'set_option_id' => $set_id]);
            }
        }
        DB::delete("DELETE FROM `product_option_no_in_sets`");
        foreach ($this->productOptionsNoInComplect as $productComplectID => $productComplectPairs)
        {
            foreach($productComplectPairs as $set_id)
            {
                $set_id = intval($set_id);
            }
            $complectIDs = array_unique($productComplectPairs);
            foreach($complectIDs as $set_id)
            {
                $set_id = intval($set_id);
                $productComplectID = intval($productComplectID);
                if (!Product::find($set_id)) {
                    if (count(Product::where('old_id', $set_id)->get()) > 0) {
                        $set_id = Product::where('old_id', $set_id)->get()->first()->id;
                    } else {
                        continue 1;
                    }
                }
                if (!Product::find($productComplectID)) {
                    if (count(Product::where('old_id', $set_id)->get()) > 0) {
                        $productComplectID = Product::where('old_id', $set_id)->get()->first()->id;
                    } else {
                        continue 1;
                    }
                }
                ProductOptionNoInSet::create(['product_id' => $productComplectID, 'set_option_id' => $set_id]);
            }
        }

        $action = "Произвел импорт товаров.<br>" . implode("<br>", $this->warnings);
        $action .= "<br> Товаров создано: " . $this->created;
        $action .= "<br> Товаров обновлено: " . $this->updated;
        $action .= "<br> Время выполнения скрипта: " . round(microtime(true) - $start, 4)." сек.";

        Logger::create([
            "user_id" => $this->user,
            "object" => "user",
            "action" => $action
        ]);
        (new ForAnyTestController)->createSlugController();
    }

    public function createProduct($data, $props, $badges)
    {
        $product_id = Product::create($data)->id;

        foreach ($props as $prop_id => $prop_value) {
            PropertiesToProduct::create([
                "product_id" => $product_id,
                "property_id" => $prop_id,
                "property_value" => $prop_value
            ]);
        }

        foreach ($badges as $badge) {
            ProductBadge::create([
                "product_id" => $product_id,
                "code" => $badge
            ]);
        }

        $this->created += 1;
        return $product_id;
    }

    public function updateProduct($data, $productID, $props, $badges)
    {
        Product::find($productID)->update($data);

        foreach ($props as $prop_id => $prop_value) {
            $tryGetProp = PropertiesToProduct::where('product_id', $productID)->where('property_id', $prop_id)->get();
            if(count($tryGetProp) > 0) {
                $tryGetProp->first()->update([
                    "property_value" => $prop_value
                ]);
            } else {
                PropertiesToProduct::create([
                    "product_id" => $productID,
                    "property_id" => $prop_id,
                    "property_value" => $prop_value
                ]);
            }
                
        }

        DB::delete("DELETE FROM `product_badges` WHERE `product_id` = '$productID'");
        foreach ($badges as $badge) {
            ProductBadge::create([
                "product_id" => $productID,
                "code" => $badge
            ]);
        }
        
        $this->updated += 1;
    }

    public function __construct()
    {
        ini_set('memory_limit','2G');
        set_time_limit(0);

        $this->user = Auth::user()->id;
    }
}
