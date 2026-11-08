<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryPageController extends Controller
{
     public function index()
    {
        return inertia('Categories/Index', [
            'categories' => CategoryResource::collection(
                Category::get()
            )
        ]);
    }
}
