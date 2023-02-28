<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductMediaFile;
use App\Models\ProductOptionInSet;
use App\Models\ProductOptionNoInSet;
use App\Models\ProductProperty;
use App\Models\ProductSet;
use App\Models\PropertiesGroup;
use App\Models\PropertiesToProduct;
use Illuminate\Http\Request;
use stdClass;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $accessLevel = $request->user->get('export_type');
        if ($accessLevel === 1) return $this->indexWithFullAccess($request);
        if ($accessLevel === 0) return $this->indexWithoutFullAccess($request);
    }

    public function show($id, Request $request)
    {
        $accessLevel = $request->user->get('export_type');

        if ($accessLevel === 1) return $this->showWithFullAccess($id);
        if ($accessLevel === 0) return $this->showWithoutFullAccess($id);
    }

    public function indexWithFullAccess(Request $request)
    {
        return Product::with(['category', 'brand', 'producing_country', 'series'])->paginate($request->get('per_page'));
    }
    
    public function indexWithoutFullAccess(Request $request)
    {
        return Product::with(['category', 'brand', 'producing_country', 'series', 'badges', 'images'])
            ->whereIn('status', '!=', ['not_avaible', 'on_order2','liquidation'])
            ->whereIn('brand_id', '!=', 2071)
            ->paginate($request->get('per_page'));
    }

    public function showWithFullAccess($id)
    {
        $product = Product::with(['category', 'brand', 'producing_country', 'series', 'badges'])->find($id);
        $product->properties = $this->properties($product);

        $product->images = $product->images()->get();
        $product->videos = $product->videos()->get();
        $product->documents = $product->docs()->get();


        //получаем основную родительскую категорию 
        $mainParent = Category::find($product->getParentCategory());
        $product->links = $this->getSimilarLinks($product, $mainParent);

        return $product;
    }

    public function showWithoutFullAccess($id)
    {
        $product = new stdClass;
        $productModel = Product::with(['category', 'brand', 'producing_country', 'series', 'badges'])->find($id);


        return $product;
    }

    public function stock($id, Request $request)
    {
        $accessLevel = $request->user->get('export_type');

        if ($accessLevel === 0) return ["error" => "You dont have access to this data"];

        $product = Product::find($id);
        if (!$product) {
            return [
                "error" => "Product with id $id not found"
            ];
        }

        if ($accessLevel === 1) {
            return [
                "product" => [
                    "id" => $id,
                ],
                "stock" => [
                    "is_in_stock" => $product->is_in_stock,
                    "stock" => $product->human_stock,
                ]
            ];
        }

        return [
            "error" => "Bad request"
        ];
    }

    public function sets($id, Request $request)
    {
        $accessLevel = $request->user->get('export_type');

        $product = Product::find($id);
        if (!$product) {
            return [
                "error" => "Product with id $id not found"
            ];
        }
        $sets = [];
        $optionsInSets = [];
        $optionNoInSets = [];

        $dbSets = ProductSet::all()->where('product_id', $product->id)->collect();
        $dbOptionsInSet = ProductOptionInSet::all()->where('product_id', $product->id)->collect();
        $dbOptionsNoInSet = ProductOptionNoInSet::all()->where('product_id', $product->id)->collect();

        if ($dbSets) {
            foreach ($dbSets as $set) {
                $sets[] = $set->set_product_id;
            }
        }

        if ($dbOptionsInSet) {
            foreach ($dbOptionsInSet as $set) {
                $optionsInSets[] = $set->set_option_id;
            }
        }

        if ($dbOptionsNoInSet) {
            foreach ($dbOptionsNoInSet as $set) {
                $optionNoInSets[] = $set->set_option_id;
            }
        }

        return [
            "sets" => $sets,
            "options_in_set" => $optionsInSets,
            "options_not_in_set" => $optionNoInSets,
        ];
    }

    public function properties($product)
    {
        $property_info = PropertiesToProduct::where('product_id', $product->id)->get();

        $product_properties = [];

        foreach (PropertiesGroup::all() as $properties_group_temp) {
            $product_properties[$properties_group_temp->slug] = [
                "real_properties_group_name" => $properties_group_temp->name,
                "properties" => []
            ];
        }
        
        foreach ($property_info as $property) {
            $property_value = $property->property_value;
            $property_id = $property->property_id;

            $property_main_info = ProductProperty::where('id', $property_id)->get()[0];
            $property_group_info = PropertiesGroup::where('id', $property_main_info->group_id)->get()[0];
            $property_name = $property_main_info->name;
            
            $product_properties[$property_group_info->slug]["properties"][] = [
                "property_id" => $property_id,
                "property_units" => $property_main_info->units,
                "property_name" => $property_name,
                "property_value" => $property_value
            ];
        }
    
        return $product->properties = $product_properties;
    }

    public function getSimilarLinks(Product $product, Category $category)
    {
        $category_id = $category->id;
        $series = $product->series;
        $block_type_id = $product->block_type_id;
        $product_id = $product->id;

        $text_prefix = "";

        if ($block_type_id == 2) {
            $text_prefix = "";
        }
        $prop_id = 0;

        if ($block_type_id === 1) {
            if($category_id !== 26) {
                //комнаты = 25 ид
                //блоки = 26 ид
                $text_prefix = "блок";
                $prop_id = 26;
                if ($category_id == 23) {
                    $prop_id = 25;
                    $text_prefix = "комнат";
                }
            }
        }
        if ($series) {
            if ($series->id) {
                $similarProducts = Product::orderBy('squere')->where('series_id', $series->id)->where('block_type_id', $block_type_id)->get();
            }
        }
        
        
        $response = [];
        if (isset($similarProducts)) {
            foreach ($similarProducts as $prod)
            {
                $active = $product_id === $prod->id ? true: false;
    
                if (is_null($prod->squere)) continue 1;
                if ($prop_id > 0) {
                    $propValue = PropertiesToProduct::where('product_id', $prod->id)->where('property_id', $prop_id)->get()->first()->property_value;
                    $propPostfix = null;
                    if ($prop_id == 25) {
                        if ($propValue === 1) {
                            $propPostfix = "y";
                        }
                        if ($propValue > 1 && $propValue < 5) {
                            $propPostfix = "ы";
                        }
                    }
    
                    if ($prop_id == 26) {
                        if ($propValue > 1 && $propValue < 5) {
                            $propPostfix = "а";
                        }
                        if ($propValue >= 5) {
                            $propPostfix = "ов";
                        }
                    }
                    
    
                }
                $data = [
                    "text" => $prod->squere . " м²",
                    "link" => [
                        "product" => $prod->id,
                    ],
                    "active" => $active,
                    "crossed" => $prod->status === "not_avaible" ? true : false,
                ];
    
                if ($prop_id > 0) {
                   $data["text"] .= " на $propValue $text_prefix$propPostfix";
                }
    
                $response[] = $data;
            }
        }

        return $response;
    }
}
