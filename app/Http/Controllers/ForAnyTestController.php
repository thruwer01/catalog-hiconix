<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForAnyTestController extends Controller
{
    public function createSlugController()
    {
        $products = Product::where("slug", NULL)->get();

        foreach ($products as $product)
        {
            $slug = Str::slug($product->getFullTitle(),'-','ru');

            if (count(Product::where('slug', $slug)->get()) > 0) {
                $slug .= "-".count(Product::where('slug', $slug)->get());
            }

            $product->update(['slug' => $slug]);
        }   
    }

    public function createSeriesLink()
    {
        $series = Series::where('link', NULL)->get();

        foreach ($series as $serie)
        {
            $slug = Str::slug($serie->h1_content,'-','ru');

            if (count(Series::where('link', '/series/'.$slug)->get()) > 0)
            {
                $slug .= "-1";
            }

            $serie->update(['link' => "/series/".$slug]);
        }
    }
}
