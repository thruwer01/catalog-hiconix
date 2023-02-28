<?php

namespace App\Http\Controllers\ProductFinal;

use App\Http\Controllers\Controller;
use App\Imports\CategoryFinalImport;
use App\Imports\ProductFinalImport;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductFinalImportController extends Controller
{
    /* public function import_products() 
    {
        Excel::import(new ProductFinalImport, public_path("/storage/products.xlsx"));
    }

    public function import()
    {
        Excel::import(new CategoryFinalImport, public_path("/storage/categories.xlsx"));
    } */

    public function test()
    {
        /* $links = [];
        foreach (Category::all() as $cat) {
            $links[$cat->id] = array_diff(explode('/', $cat->link), array(''));
        }

        dd($links); */
    }
}
