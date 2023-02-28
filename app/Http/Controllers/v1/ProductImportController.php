<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductImportController extends Controller
{
    public function import($path) 
    {
        Excel::import(new ProductImport, $path);
    }
}
