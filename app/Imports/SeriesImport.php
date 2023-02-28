<?php

namespace App\Imports;

use App\Http\Controllers\ForAnyTestController;
use App\Models\Brand;
use App\Models\Logger;
use App\Models\Series;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class SeriesImport implements ToCollection
{
    public $warnings = [];

    public $updated = 0;
    public $created = 0;

    public $user = null;

    public function collection(Collection $rows)
    {
        $start = microtime(true);

        foreach ($rows as $index => $row) 
        {
            if ($index < 1) continue 1;

            if ($row[1] == null) continue 1;

            $tryGetBrand = Brand::where('name', $row[3])->get();
            if (count($tryGetBrand) > 0) 
            {
                $brand_id = $tryGetBrand->first()->id;
            } else {
                $brand_id = Brand::create(["name" => $row[3]])->id;
            }

            $seriesData = [
                "name" => $row[1],
                "link" => $row[2],
                "brand_id" => $brand_id,
                "status" => $row[5] === 0 ? false : true,
                "html_description" => $row[6],
                "html_description_other" => $row[7],
                "html_features" => $row[8],
                "h1_content" => $row[9],
                "meta_title" => $row[10],
                "meta_desc" => $row[11],
                "meta_keys" => $row[12],
                "meta_title_ecoclima" => $row[13],
                "meta_desc_ecoclima" => $row[14],
                "meta_keys_ecoclima" => $row[15],
                "meta_title_auxair" => $row[16],
                "meta_desc_auxair" => $row[17],
                "meta_keys_auxair" => $row[18]
            ];

            if ($row[0] === null) {
                //создаем категорию
                $this->createSeries($seriesData);
            } else {
                //обновляем категорию
                $seriesID = $row[0];

                if (!Series::find($seriesID)) {
                    $this->warnings[] = "Не удалось найти серию с ID $seriesID и обновить ее, для начала ее необходимо создать! Строка $index";
                    continue 1;
                }
                
                $this->updateSeries($seriesData, $seriesID);
            }

        }

        $action = "Произвел импорт серий.<br>" . implode("<br>", $this->warnings);
        $action .= "<br> Серий создано: " . $this->created;
        $action .= "<br> Серий обновлено: " . $this->updated;
        $action .= "<br> Время выполнения скрипта: " . round(microtime(true) - $start, 4)." сек.";

        Logger::create([
            "user_id" => $this->user,
            "object" => "user",
            "action" => $action
        ]);

        (new ForAnyTestController)->createSeriesLink();
    }

    public function createSeries($data)
    {
        Series::create($data);
        $this->created += 1;
    }

    public function updateSeries($data, $id)
    {
        Series::find($id)->update($data);
        $this->updated += 1;
    }

    public function __construct()
    {
        ini_set('memory_limit','2G');
        set_time_limit(0);

        $this->user = Auth::user()->id;
    }
}
