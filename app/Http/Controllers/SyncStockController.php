<?php

namespace App\Http\Controllers;

use App\Models\Logger;
use App\Models\Product;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SyncStockController extends Controller
{
    public function sync(Request $request)
    {
        $start = microtime(true);
        $user = null;
        $is_cron_or_system = false;
        $ip = null;
        
        if (Auth::user()?->id) {
            $user = Auth::user()->id;
            $ip = $request->ip();
        } else {
            $is_cron_or_system = true;
            $ip = $request->ip();
        }
        $forLog = "Начало синхронизации остатков с API RUSKLIMAT";

        Logger::create([
            "user_id" => $user ? $user : null,
            "object" => $is_cron_or_system ? "CRON" : "user",
            "action" => $forLog,
            "ip" => $ip ? $ip : null
        ]);

        if ($is_cron_or_system === true) {
            $settings = Settings::find(1)->cron_sync_stock;
            if ($settings === 0) {
                Logger::create([
                    "user_id" => $user ? $user : null,
                    "object" => $is_cron_or_system ? "CRON" : "user",
                    "action" => "Синхронизация остатков с API RUSLIMAT остановлена, т.к. она отключена в найстроках ЕРК.",
                    "ip" => $ip ? $ip : null
                ]);
                exit();
            }
        }

        DB::update("UPDATE `products` SET `stock` = 0, `is_in_stock` = 0, `human_stock` = ''");

        $response = Http::withHeaders([
            "Authorization" => "SldhMUZDcTBrOWt5aFRTdkJlZEZOZz09"
        ])->get('http://api.rusklimat.ru/rest/hks/ProductStore');
        
        $response = $response->collect()->get('data');

        foreach ($response as $product)
        {
            $product_article = $product['hiconixcode'];
            $get_products = Product::where('article', $product_article)->get();

            if (count($get_products) > 0) {
                $p = $get_products->first();
                $product_stock = (int)$product['store'];
                $human_stock = null;
                $is_in_stock = null;
                if ($product_stock <= 0) {
                    $human_stock = "Отсутствует";
                    $is_in_stock = false;
                    
                    if ($p->status_new == "1") {
                        $p->update(["status_new" => 2]);
                    }
                    
                    if($p->status_new == "3") {
                        $p->update(["status_new" => 0]);
                    }

                } else {
                    $is_in_stock = true;
                    if ($p->status_new == "2" || $p->status_new == "4") {
                        $p->update(["status_new" => "1"]);
                    }

                }
                if ($product_stock > 0 && $product_stock <= 5) $human_stock = "мало";
                if ($product_stock > 5 && $product_stock <= 10) $human_stock = "достаточно";
                if ($product_stock > 10) $human_stock = "много";
                $get_products[0]->update(['stock' => $product_stock, 'human_stock' => $human_stock, 'is_in_stock' => $is_in_stock]);
            }

        }

        $forLog = "Синхронизация остатков с API RUSKLIMAT закончена успешно. " . 'Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.';

        Logger::create([
            "user_id" => $user ? $user : null,
            "object" => $is_cron_or_system ? "CRON" : "user",
            "action" => $forLog,
            "ip" => $ip ? $ip : null
        ]);

        return $forLog;
    }
}
