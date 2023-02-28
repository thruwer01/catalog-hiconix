<?php

namespace App\Exports;

use App\Models\Series;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SeriesExport implements FromArray, WithHeadings, WithStyles
{
    public $seriesArray = [];

    public function __construct()
    {
        $allSeries = Series::with('brand')->get();
        foreach ($allSeries as $series)
        {
            $this->seriesArray[] = [
                $series->id,
                $series->name,
                $series->link,
                $series->brand->name,
                $series->brand->id,
                $series->status == false ? "0" : "1",
                $series->html_description,
                $series->html_description_other,
                $series->html_features,
                $series->h1_content,
                $series->meta_title,
                $series->meta_desc,
                $series->meta_keys,
                $series->meta_title_ecoclima,
                $series->meta_desc_ecoclima,
                $series->meta_keys_ecoclima,
                $series->meta_title_auxair,
                $series->meta_desc_auxair,
                $series->meta_keys_auxair
            ];
        }
    }

    public function array():array
    {
        return $this->seriesArray;
    }

    public function headings():array
    {
        return [
            "ID Серии",
            "Название",
            "Ссылка",
            "Бренд",
            "ID Бренда",
            "Статус (1=активная, 0=архивная)",
            "HTML Описание (Основное)",
            "HTML Описание (Прочее)",
            "HTML Преимущества серии",
            "H1 Серии",
            "Hiconix - Meta Title",
            "Hiconix - Meta Desc",
            "Hiconix - Meta Keywords",
            "Ecoclima - Meta Title",
            "Ecoclima - Meta Desc",
            "Ecoclima - Meta Keywords",
            "AuxAir - Meta Title",
            "AuxAir - Meta Desc",
            "AuxAir - Meta Keywords"
        ];
    }

    public function styles(Worksheet $sheet):array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFAF0'],
                ]
            ],
        ];
    }
}
