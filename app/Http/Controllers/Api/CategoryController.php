<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::all();
            
            $categories = $categories->map(function ($category) {
                $category->cover_image = getFullImageUrl($category->cover_image);
                return $category;
            });
    
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch categories', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Category $category)
    {
        try {
            $category->cover_image = getFullImageUrl($category->cover_image);
            return response()->json($category, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch category', 'message' => $e->getMessage()], 500);
        }
    }
}
