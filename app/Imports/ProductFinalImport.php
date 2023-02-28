<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductFinalImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row)
        {
            if ($index < 3) continue 1;
            $model = $row[5];
            $is_invertor = $row[19];
            $product = Product::where('model', $model)->get()->first();

            if(!is_null($product))
            {
                $product->update(['is_invertor' => $is_invertor]);
            }
        }
    }
}
