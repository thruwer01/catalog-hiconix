<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class CategoriesExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{

    public $categories = [];

    public function __construct()
    {
        $allCat = Category::all();

        foreach ($allCat as $category)
        {
            $this->categories[] = [
                $category->id,
                $category->title,
                $category->parent_id,
                $category->link,
                (string)$category->is_private,
                $category->compatibility,
                $category->sort,
                (string)$category->is_active_hiconix,
                (string)$category->is_menu_hiconix,
                $category->html_description_header,
                $category->html_description_footer,
                $category->img_preview_path,
                $category->filter_string,
                $category->product_prefix,
                $category->meta_title,
                $category->meta_description,
                $category->meta_keys
            ];
        }
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function array():array
    {
        return $this->categories;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Название категории',
            'ID родителя',
            'Ссылка',
            'Приватность: Закрытая(и см еще)-1, Открытая (и уточнить)-0',
            'Совместимость',
            'Сортировка',
            'Активность Хиконикс (0 = нет | 1 = да)',
            'Меню Хиконикс (0 = нет | 1 = да)', 
            'Описание сверху блока анонсов товаров', 
            'Описание ниже блока анонсов товаров',
            'Превью',
            'Наименования в хк / фильтр / уточнить / см еще', 
            'Префикс товаров', 
            'META Title', 
            'META DESC', 
            'META KEYWORDS', 
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

    public function columnWidths(): array
    {
        $range = range('A', 'Q');

        $cWidth = [];

        foreach ($range as $r)
        {
            $cWidth[$r] = 20;
        }

        return $cWidth;
    }
}
