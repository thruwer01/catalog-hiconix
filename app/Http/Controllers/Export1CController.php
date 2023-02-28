<?php

namespace App\Http\Controllers;

use App\Exports\Export1CStore;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class Export1CController extends Controller
{
    public function export()
    {
        return Excel::download(new Export1CStore, 'export_1c_store.xlsx');
        // (new Export1CStore())->array();
    }
}
