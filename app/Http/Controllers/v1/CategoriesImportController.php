<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Imports\CategoryImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CategoriesImportController extends Controller
{
    public function import($path) 
    {
        Excel::import(new CategoryImport, $path);
    }
}
