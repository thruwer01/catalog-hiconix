<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Logger;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class CategoryImport implements ToCollection
{
    public $warnings = [];

    public $updated = 0;
    public $created = 0;
    public $deleted = 0;
    public $cantDelete = [];
    public $user = null;

    public $toCreate = [];

    public function collection(Collection $rows)
    {
        $start = microtime(true);

        foreach ($rows as $index => $row) 
        {
            if ($index < 1) continue 1;

            $allIDS[] = $row[0];

            $categoryData = [
                "id" => $row[0],
                "title" => $row[1],
                "parent_id" => $row[2],
                "link" => $row[3],
                "is_private" => $row[4] === 0 ? false : true,
                "compatibility" => $row[5],
                "sort" => $row[6],
                "is_active_hiconix" => $row[7] === 0 ? false : true,
                "is_menu_hiconix" => $row[8] === 0 ? false : true,
                "html_description_header" => $row[9],
                "html_description_footer" => $row[10],
                "img_preview_path" => $row[11],
                "filter_string" => $row[12],
                "product_prefix" => $row[13],
                "meta_title" => $row[14],
                "meta_description" => $row[15],
                "meta_keys" => $row[16]
            ];

            if (!Category::find($row[0])) {
                //создаем категорию
                $this->createCategory($categoryData);
            } else {
                //обновляем категорию
                $categoryID = $row[0];

                /* if (!Category::find($categoryID)) {
                    $this->warnings[] = "Не удалось найти категорию с ID $categoryID и обновить ее, для начала ее необходимо создать! Строка $index";
                    continue 1;
                } */
                
                $this->updateCategory($categoryData, $categoryID);
            }            
        }
        $this->deleteCategories($allIDS);

        $action = "Произвел импорт категорий.<br>" . implode("<br>", $this->warnings);
        $action .= "<br> Категорий создано: " . $this->created;
        $action .= "<br> Категорий обновлено: " . $this->updated;
        $action .= "<br> Категорий удалено: " . $this->deleted;
        $action .= "<br> Не удалось удалить категории с ID: " . implode(',', $this->cantDelete);

        $action .= "<br> Время выполнения скрипта: " . round(microtime(true) - $start, 4)." сек.";

        Logger::create([
            "user_id" => $this->user,
            "object" => "user",
            "action" => $action
        ]);
    }

    public function createCategory($data)
    {
        $newCat = Category::create($data);

        $this->toCreate[] = $newCat->id;
        $this->created += 1;
    }

    public function updateCategory($data, $id)
    {
        Category::find($id)->update($data);
        $this->updated += 1;
    }

    public function deleteCategories($cats)
    {
        //получаем загружаемые категории - сверяем с базой, чего нет в импорте - удаляем
        $categories = Category::all();
        $idsFromDB = [];

        foreach ($categories as $cat)
        {
            $idsFromDB[] = $cat->id;
        }

        $toDelete = array_diff($idsFromDB, $cats);

        foreach($toDelete as $toDelCategory)
        {
            $i = 0;
            if (count(Category::where('parent_id', $toDelCategory)->get()) === 0) {
                if (!in_array($toDelCategory, $this->toCreate)) {
                    Category::find($toDelCategory)->delete();
                    $i++;
                }
            } else {
                $this->cantDelete[] = $toDelCategory;
            }
            $this->deleted = $i;
        }
    }

    public function __construct()
    {
        ini_set('memory_limit','2G');
        set_time_limit(0);

        $this->user = Auth::user()->id;
    }
}
