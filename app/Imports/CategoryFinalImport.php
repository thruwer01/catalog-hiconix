<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CategoryFinalImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row)
        {
            if ($index < 1) continue 1;
            $id = $row[1];
            $compatibility = $row[9];

            $cat = Category::find($id);
            if (!is_null($cat))
            {
                $cat->update(['compatibility' => $compatibility]);
            }
        }
    }
}
