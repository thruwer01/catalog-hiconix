<?php

namespace App\Imports\Site;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Http\UploadedFile;
use Orchid\Attachment\File;

class SiteImportPhotos implements ToCollection
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
            $images = explode(',', $row[1]);

            $tryGetProduct = Product::where('site_id', $siteID)->get();

            if (count($tryGetProduct) !== 0) {
                $product = $tryGetProduct->first();
                $tryGetProductImages = $product->images()->get()->collect();
                if (count($tryGetProductImages) !== 0)
                {
                    //у товара есть картинки - проверяем каждую на соответствие имени
                    $imagesProductDB = [];
                    $imagesProductExcel = [];

                    foreach($tryGetProductImages as $image) {
                        $imageNameDB = $image->original_name;

                        $imagesProductDB[] = $imageNameDB;
                    }

                    foreach($images as $excelImage)
                    {
                        $imagesProductExcel[] = collect(explode('/', $excelImage))->last();
                    }

                    $diffImages = array_keys(array_diff($imagesProductExcel, $imagesProductDB));

                    $tempImages = [];

                    foreach($diffImages as $imageIndex)
                    {
                        $imagePath = $images[$imageIndex];
                        $tempImages[] = $imagePath;
                    }
                    $images = $tempImages;
                }

                foreach($images as $imagePath)
                {
                    $imageURL = "https://hiconix.ru$imagePath";

                    $fileData = file_get_contents($imageURL);

                    $fileName = md5((string) collect(explode('/', $imagePath))->last());
                    $extension = collect(explode('.', $imagePath))->last();

                    $fileNameFull = $fileName.".".$extension;

                    // $fileRealName = $document->name;
                    Storage::put('public/newphotos/' . $fileNameFull, $fileData);
    
                    $file = new UploadedFile(public_path('/storage/newphotos/' . $fileNameFull), $fileNameFull, "image/$extension");
                    $attachment = (new File($file, null, 'photo'))->load();
                    $product->attachment()->syncWithoutDetaching([$attachment->id]);
                }
            }
        }
    }
}
