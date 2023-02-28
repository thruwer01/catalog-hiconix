<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AntiDublesController extends Controller
{
    public function anti()
    {
        $dubles = DB::select("SELECT `model`, COUNT(`model`) AS `count` FROM `products` GROUP BY `model` HAVING `count` > 1");
        
        $count = 0;

        foreach ($dubles as $duble)
        {
            $dModel = $duble->model;
            $productsWithModel = Product::where('model', $dModel)->get()->collect()->toArray();


            $toDeleteProductID = $productsWithModel[1]['id'];
            DB::delete("DELETE FROM `properties_to_products` WHERE `product_id` = '$toDeleteProductID'");
            DB::delete("DELETE FROM `products` WHERE `id` = '$toDeleteProductID'");

            $count++;
        }

        return $count;
    }
}
