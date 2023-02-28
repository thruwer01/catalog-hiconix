<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;


class Export1CStore implements FromArray
{

    public $data = [];

    public function __construct()
    {
        $file = storage_path('app/public/1cStore-1.json');
        $data = json_decode(file_get_contents($file));

        foreach ($data as $key => $value)
        {
            $this->data[] = [$key, $value->store];
        }
    }

    public function array():array
    {
        return $this->data;
    }
}
