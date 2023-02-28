<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class GetManagerAmoController extends Controller
{
    public function get(Request $request)
    {
        $managerID = $request->query('manager_id');

        if(!isset($managerID)) return;
        if(is_null($managerID)) return;
        if(strlen($managerID) !== 3) return;

        $tryUser = User::where('manager_number', $managerID)->get();

        if (count($tryUser) === 0) return;
        
        return $tryUser->first()->manager_amo_id;
    }
}
