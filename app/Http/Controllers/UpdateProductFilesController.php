<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductMediaFile;
use Illuminate\Http\Request;
use Orchid\Attachment\File;
use Illuminate\Http\UploadedFile;

class UpdateProductFilesController extends Controller
{
    public function sync()
    {
        $productImages = ProductMediaFile::all()->collect();

        foreach ($productImages as $productImage)
        {
            $productID = $productImage->product_id;
            $product = Product::find($productID);

            $file = new UploadedFile(public_path($productImage->url), $productImage->file_name);
            $attachment = (new File($file, null, 'documents'))->load();
            
            $product->attachment()->syncWithoutDetaching([$attachment->id]);
        }
    }
}
