<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * List categories
     *
     * Active categories with the number of books in each one.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount('books')
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }
}
