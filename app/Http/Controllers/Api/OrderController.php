<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'order_items' => 'required|array',
            'order_items.*.item_id' => 'required|exists:items,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.price' => 'required|numeric|min:0',
            'customer_address' => 'required',
            'latitude' =>'required',
            'longitude' => 'required',
            'total_price' => 'required|numeric|min:0',
            'delivery_note' => 'nullable|string',
            'delivery_cost' => 'required|numeric|min:0',
            'sub_total' => 'required|numeric|min:0',
            'status' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'customer_address' => $request->customer_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'customer_id' => auth()->user()->id,
                'total_price' => $request->total_price,
                'customer_id' => Auth::id(),
                'delivery_note' => $request->delivery_note,
                'delivery_cost' => $request->delivery_cost,
                'sub_total' => $request->sub_total,
                'status' => $request->status
            ]);

            foreach ($request->order_items as $item) {
                OrderItem::create([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'order_id' => $order->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while placing the order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $customer = Auth::user();
        
        $query = Order::where('customer_id', $customer->id)
                      ->with(['orderItems.item','customer']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        return response()->json($orders);
    }

    public function show($id)
    {
        $customer = Auth::user();
        
        $order = Order::where('user_id', $customer->id)
                      ->with(['orderItems.item','customer'])
                      ->findOrFail($id);
        
        return response()->json($order);
    }
}
