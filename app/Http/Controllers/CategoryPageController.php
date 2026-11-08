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

    public function show(Category $category)
    {
        return inertia('Categories/Show', [
            'id' => $category->id
        ]);
    }

    public function create()
    {
        return inertia('Categories/Create');
    }

    public function edit(Category $category)
    {
        return inertia('Categories/Edit', [
            'id' => $category->id
        ]);
    }
}
