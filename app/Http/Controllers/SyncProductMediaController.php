<?php

namespace App\Http\Controllers;

use App\Imports\Site\SiteImportPhotos;
use App\Models\Product;
use Orchid\Attachment\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SyncProductMediaController extends Controller
{
    public function sync()
    {
        set_time_limit(0);
        ini_set('memory_limit','2G');

        Excel::import(new SiteImportPhotos, storage_path('/app/public/sitePhotos.xlsx'));
    }
}
