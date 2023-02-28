<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return Category::paginate($request->get('per_page'));
    }

    public function show($id)
    {
        return Category::find($id);
    }
}
