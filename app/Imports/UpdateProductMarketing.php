<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductBadge;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UpdateProductMarketing implements ToCollection
{
    /**
    * @param Collection $collection
    */

    public function collection(Collection $rows)
    {
        ini_set('memory_limit','2G');
        set_time_limit(0);

        $i = 0;
        foreach($rows as $row)
        {
            $i++;
            $product = Product::all()->where('model', $row[5])->first();
            if ($i < 5) continue 1;
            if (is_null($product)) continue 1;

            for ($a = 13; $a <= 24; $a++) {
                if (!is_null($row[$a])) {
                    $ProductBadge = new ProductBadge(['code' => $row[$a], 'product_id' => (int) $product->id]);
                    $ProductBadge->save();
                }
            }


        }
    }
}
