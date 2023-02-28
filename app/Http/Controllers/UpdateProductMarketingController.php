<?php

namespace App\Http\Controllers;

use App\Imports\UpdateProductMarketing;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UpdateProductMarketingController extends Controller
{
    public function import()
    {
        return Excel::import(new UpdateProductMarketing, storage_path('/app/public/marketing.xlsx'));
    }
}
