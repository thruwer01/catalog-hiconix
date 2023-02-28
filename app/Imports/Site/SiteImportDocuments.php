<?php

namespace App\Imports\Site;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Http\UploadedFile;
use Orchid\Attachment\File;
use Illuminate\Support\Facades\Storage;

class SiteImportDocuments implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        set_time_limit(0);
        ini_set('memory_limit','2G');
        foreach($rows as $index => $row)
        {
            if ($index < 2) continue 1;
            $siteID = $row[0];
            $fileREALNAME = $row[2];
            $fileURL = $row[8];
            if ($fileURL === 0 OR $fileURL == "0") continue 1;

            $tryGetProduct = Product::where('site_id', $siteID)->get();
            if (count($tryGetProduct) !== 0) {
                $product = $tryGetProduct->first();

                $fileData = file_get_contents($fileURL);
                $fileName = md5((string) collect(explode('/', $fileURL))->last());
                $extension = collect(explode('.', $fileURL))->last();

                $fileNameFull = $fileName.".".$extension;

                // $fileRealName = $document->name;
                Storage::put('public/documents/' . $fileNameFull, $fileData);

                $file = new UploadedFile(public_path('/storage/documents/' . $fileNameFull), $fileREALNAME, "image/$extension");
                $attachment = (new File($file, null, 'documents'))->load();
                $product->attachment()->syncWithoutDetaching([$attachment->id]);
            }
        }
    }
}
