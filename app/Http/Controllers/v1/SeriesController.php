<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Series;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index(Request $request)
    {
        return Series::paginate($request->get('per_page'));
    }

    public function show($id)
    {
        return Series::with('brand')->find($id);
    }
}
