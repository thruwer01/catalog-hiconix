<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        return Brand::paginate($request->get('per_page'));
    }

    public function show($id)
    {
        return Brand::find($id);
    }
}
