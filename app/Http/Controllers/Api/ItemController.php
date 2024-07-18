<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Item::query()->with('restaurant', 'category');

            if ($request->has('restaurant_id')) {
                $query->where('restaurant_id', $request->restaurant_id);
            }

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('is_offer_item')) {
                $query->where('is_offer_item', $request->is_offer_item);
            }

            $items = $query->get();

            $items = $items->map(function ($item) {
                $item->cover_image = getFullImageUrl($item->cover_image);

                if ($item->restaurant) {
                    $item->restaurant->logo = getFullImageUrl($item->restaurant->logo);
                }
                
                if ($item->category) {
                    $item->category->cover_image = getFullImageUrl($item->category->cover_image);
                }

                return $item;
            });

            return response()->json($items,200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch items', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Item $item)
    {
        try {
            $item = $item->load('restaurant', 'category');
            $item->cover_image = getFullImageUrl($item->cover_image);
            if ($item->restaurant) {
                $item->restaurant->logo = getFullImageUrl($item->restaurant->logo);
            }
            
            if ($item->category) {
                $item->category->cover_image = getFullImageUrl($item->category->cover_image);
            }

            return response()->json($item,200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch item', 'message' => $e->getMessage()], 500);
        }
    }
}
