<?php

namespace App\Http\Controllers;

use App\Exports\SeriesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SeriesExportController extends Controller
{
    public function export()
    {
        return Excel::download(new SeriesExport, 'series'.date('d-m-Y-H-i-s').'.xlsx');
    }
}
