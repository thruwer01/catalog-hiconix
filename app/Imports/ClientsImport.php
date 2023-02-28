<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClientsImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $clientName = $row[0];
            $clientMailingList = $row[1];
            $mailingArray = explode(',', $clientMailingList);
            
            if ($clientName !== null)
            {
                $user_id = User::create([
                    'name' => $clientName,
                    'email' => $mailingArray[0],
                    'mailing_list' => $clientMailingList,
                    'is_full_export' => false
                ])->id;

                DB::insert('insert into role_users (user_id,role_id) values (?,?)', [$user_id, 4]);
            }
        }
    }
}
