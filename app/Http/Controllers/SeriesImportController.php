<?php

namespace App\Http\Controllers;

use App\Imports\SeriesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SeriesImportController extends Controller
{
    public function import($path) 
    {
        Excel::import(new SeriesImport, $path);
    }
}
