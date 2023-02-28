<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductExportController extends Controller
{
    public function export()
    {
        return Excel::download(new ProductExport, 'products-'.date('d-m-Y-H-i-s').'.xlsx');
    }
}
