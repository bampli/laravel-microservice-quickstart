<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BasicCrudController
{
    private $rules = [
        'name' => 'required|max:255',
        'description' => 'nullable',
        'is_active' => 'boolean'
    ];

    // public function index()
    // {
    //     $collection = parent::index();
    //     return new CategoryCollection($collection);
    //     //return CategoryResource::collection($collection);
    // }

    protected function model()
    {
        return Category::class;
    }

    protected function resource()
    {
        return CategoryResource::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }
}
