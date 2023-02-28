<?php

namespace App\Http\Controllers;

use App\Imports\ClientsImport;
use Maatwebsite\Excel\Facades\Excel;

class ClientsImportController extends Controller
{
    public function import()
    {
        return Excel::import(new ClientsImport, storage_path('/app/public/users.xlsx'));
    }   
}
