<?php

namespace App\Http\Controllers;

use App\Imports\Site\SiteImportDocuments;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UploadNewDocumentsController extends Controller
{
    public function sync()
    {
        Excel::import(new SiteImportDocuments, storage_path('/app/public/siteDocs.xlsx'));
    }
}
