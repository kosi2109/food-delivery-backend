<?php

namespace App\Http\Controllers\Api;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::query();

        if ($request->has('is_popular')) {
            $query->where('is_popular', $request->is_popular);
        }

        if ($request->has('shop_type')) {
            $query->where('shop_type', 'like', '%' . $request->food_type . '%');
        }

        if ($request->has('shop_name')) {
            $query->where('name', 'like', '%' . $request->shop_name . '%');
        }

        $restaurants = $query->get();

        $restaurants = $restaurants->map(function ($restaurant) {
            $restaurant->logo = getFullImageUrl($restaurant->logo);
            return $restaurant;
        });

        return response()->json($restaurants);
    }

    public function show(Restaurant $restaurant)
    {
        return response()->json($restaurant);
    }
}
