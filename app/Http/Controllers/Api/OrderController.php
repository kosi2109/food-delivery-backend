<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Portion;
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
            'order_items.*.total' => 'required|numeric|min:0',
            'order_items.*.status' => 'required|numeric|min:0',
            'customer_address' => 'required',
            // 'latitude' =>'required',
            // 'longitude' => 'required',
            'delivery_note' => 'nullable|string',
            'delivery_cost' => 'required|numeric|min:0',
            'payment_type_id' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'customer_address' => $request->customer_address,
                // 'latitude' => $request->latitude,
                // 'longitude' => $request->longitude,
                'customer_id' => auth()->user()->id,
                'customer_id' => Auth::id(),
                'delivery_note' => $request->delivery_note,
                'delivery_cost' => $request->delivery_cost,
                'payment_type_id' => $request->payment_type_id,
            ]);

            foreach ($request->order_items as $item) {
                $portion = Portion::findOrFail($item['portion_id']);
                $price = $portion->price;
                
                OrderItem::create([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'portion_id' => $item['portion_id'],
                    'price' => $item['price'],
                    'order_id' => $order->id,
                    'total' => $item['total'],
                    'status' => $item['status']
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

        $paymentTypes = config('payment_types.types');

        $orders = $orders->map(function ($order) use ($paymentTypes) {
            $order->payment_type = $paymentTypes[$order->payment_type_id] ?? 'Unknown';
            return $order;
        });

        return response()->json($orders);
    }

    public function show($id)
    {
        $customer = Auth::user();
        
        $order = Order::where('customer_id', $customer->id)
                ->where('id', $id)
                ->with(['orderItems.item', 'customer'])
                ->firstOrFail();
        
        $paymentTypes = config('payment_types.types');

        $order->payment_type = $paymentTypes[$order->payment_type_id] ?? 'Unknown';

        return response()->json($order);
    }

    public function paymentTypeList()
    {
        return response()->json(config('payment_types'));
    }
}
