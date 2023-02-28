<?php

namespace App\Http\Controllers;

use App\Exports\CategoriesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CategoriesExportController extends Controller
{
    public function export()
    {
        return Excel::download(new CategoriesExport, 'categories'.date('d-m-Y-H-i-s').'.xlsx');
    }
}
