<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductMediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SyncMediaFilesController extends Controller
{
    public function sync()
    {
        ini_set('memory_limit','2G');
        set_time_limit(0);
        $client = Http::withHeaders([
            "Authorization" => "Token AcBo2r6DkUgX3U7BYvvVZAN3Ue3MS5e2",
        ]);
        foreach (Product::all() as $product)
        {
            if ($product->old_id)
            {
                $product_id = $product->id;

                $response = $client->get('https://api.hiconix.ru/api/v3/products/' . $product->old_id);

                $image_media_files = $response['image_media_file_ids'];
                $non_image_media_files = $response['non_image_media_file_ids'];

                if (count($image_media_files) > 0) {
                    foreach ($image_media_files as $img_media_file)
                    {
                        $img_data = $client->get('https://api.hiconix.ru/api/v3/media_files/' . $img_media_file);
                        
                        if (str_contains($img_data['file_path'], "new_media_file")) {
                            $img_real_file_path = str_replace("new_media_file", $img_data['title'], $img_data['file_path']);
                        } else {
                            $img_real_file_path = $img_data['file_path'];
                        }

                        $imgUrl = 'https://api.hiconix.ru' . $img_real_file_path;
                        $contents = file_get_contents($imgUrl);
                        $imgName = substr($imgUrl, strrpos($imgUrl, '/') + 1);                
                        
                        $path = 'media_files/images/' . $imgName;
                        Storage::put('public/'.$path, $contents);
                        
                        $imgData = [
                            "product_id" => $product_id,
                            "title" => $img_data['title'],
                            "url" => '/storage/' . $path,
                            "file_name" => $imgName
                        ];
                        if (count(ProductImage::where([["product_id", $product_id], ["file_name", $imgName]])->get()) === 0) {
                            ProductImage::create($imgData);
                        }
                    }
                }
                
                if (count($non_image_media_files) > 0) {
                    foreach ($non_image_media_files as $non_img_media_file)
                    {
                        $non_img_data = $client->get('https://api.hiconix.ru/api/v3/media_files/' . $non_img_media_file);

                        if (str_contains($non_img_data['file_path'], "new_media_file")) {
                            $non_img_real_file_path = str_replace("new_media_file", $non_img_data['title'], $non_img_data['file_path']);
                        } else {
                            $non_img_real_file_path = $non_img_data['file_path'];
                        }

                        $fileUrl = $imgUrl = 'https://api.hiconix.ru' . $non_img_real_file_path;
                        $contents = file_get_contents($fileUrl);
                        $fileName = substr($fileUrl, strrpos($fileUrl, '/') + 1);     

                        if (str_contains($fileName, '.pdf')) {
                            $fileNameForPath = $fileName;
                        } else {
                            $fileNameForPath = Str::slug($fileName, '_', 'ru');
                        }

                        $path = 'media_files/non_images/' . $fileNameForPath . '.pdf';
                        Storage::put('public/'.$path, $contents);

                        $nonImgData = [
                            "product_id" => $product_id,
                            "title" => $non_img_data['title'],
                            "url" => '/storage/' . $path,
                            "file_name" => $fileName
                        ];

                        if (count(ProductMediaFile::where([["product_id", $product_id], ["file_name", $fileName]])->get()) === 0) {
                            ProductMediaFile::create($nonImgData);
                        }
                    }
                }
            }
        }
    }
}
