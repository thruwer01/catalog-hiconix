<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Imports\CategoryImport;
use Maatwebsite\Excel\Facades\Excel;

class TestImportController extends Controller
{
    public function import() 
    {
        Excel::import(new CategoryImport, storage_path('/app/public/categories.xlsx'));
    }
}
