<?php

namespace App\Http\Controllers;

use App\Models\Logger;
use App\Models\Product;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SyncPriceController extends Controller
{
    public function sync(Request $request)
    {
        ini_set('memory_limit','2G');
        set_time_limit(0);

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
        $forLog = "Начало синхронизации цен с API RUSKLIMAT";

        Logger::create([
            "user_id" => $user ? $user : null,
            "object" => $is_cron_or_system ? "CRON" : "user",
            "action" => $forLog,
            "ip" => $ip ? $ip : null
        ]);

        if ($is_cron_or_system === true) {
            $settings = Settings::find(1)->cron_sync_price;
            if ($settings === 0) {
                Logger::create([
                    "user_id" => $user ? $user : null,
                    "object" => $is_cron_or_system ? "CRON" : "user",
                    "action" => "Синхронизация цен с API RUSLIMAT остановлена, т.к. она отключена в найстроках ЕРК.",
                    "ip" => $ip ? $ip : null
                ]);
                exit();
            }
        }

        DB::update("UPDATE `products` SET `ric_current` = NULL, `wholesale_price` = NULL");

        $response = Http::withHeaders([
            "Authorization" => "SldhMUZDcTBrOWt5aFRTdkJlZEZOZz09"
        ])->connectTimeout(360)->timeout(360)->get('http://api.rusklimat.ru/rest/hks/ProductPrice');

        $get_currency = Http::withHeaders([
            "Authorization" => "SldhMUZDcTBrOWt5aFRTdkJlZEZOZz09"
        ])->get('http://api.rusklimat.ru/rest/hks/CurrencyCourses/')->collect();

        $courceUSD = $get_currency['data'][0]['course'];
        $courceEUR = $get_currency['data'][2]['course'];
        $courceRUB = 1;

        $response = $response->collect()->get('data');

        foreach ($response as $price)
        {
            $product_article = $price['hiconixcode'];
            $get_products = Product::where('article', $product_article)->get();

            //розница
            if ($price['uid_TipPrice'] == "cc181d01-c284-11ea-80c6-3863bb4497f1") {
                if ($price['currency'] == "USD") $priceNormal = round(intval($price['value']) * $courceUSD);
                if ($price['currency'] == "EUR") $priceNormal = round(intval($price['value']) * $courceEUR);
                if ($price['currency'] == "руб.") $priceNormal = round(intval($price['value']) * $courceRUB);

                if (!is_null($get_products->first())) {
                    $get_products->first()->update(['ric_current' => $priceNormal]);
                }
            }
            //диллерская (базовая 60)
            if ($price['uid_TipPrice'] == "0ec3c28b-2a7a-11e7-80c6-1402ec411fc5") {
                if ($price['currency'] == "USD") $priceNormal = round(intval($price['value']) * $courceUSD);
                if ($price['currency'] == "EUR") $priceNormal = round(intval($price['value']) * $courceEUR);
                if ($price['currency'] == "руб.") $priceNormal = round(intval($price['value']) * $courceRUB);

                if (!is_null($get_products->first())) {
                    $get_products->first()->update(['wholesale_price' => $priceNormal]);
                }
            }
        }

        $forLog = "Синхронизация цен с API RUSKLIMAT закончена успешно. " . 'Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.';

        Logger::create([
            "user_id" => $user ? $user : null,
            "object" => $is_cron_or_system ? "CRON" : "user",
            "action" => $forLog,
            "ip" => $ip ? $ip : null
        ]);

        return $forLog;
    }
}
