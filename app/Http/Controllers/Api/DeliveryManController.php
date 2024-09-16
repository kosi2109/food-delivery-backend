<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DeliveryManController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // Logout function
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function orderList(Request $request)
    {
        // $query = OrderItem::query()
        //     ->with(['order', 'portion', 'order.customer']);
        $query = Order::query()
            ->with(['orderItems', 'customer']);

        if ($request->has('status')) {
            $query->whereHas('orderItems', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // if ($request->has('status')) {
        //     $query->where('status', $request->status);
        // }

        $orders = $query->get();

        $paymentTypes = config('payment_types.types');

        // $orderItems = $orderItems->map(function ($orderItem) use ($paymentTypes) {
        //     // Add payment type to the order
        //     $orderItem->payment_type = $paymentTypes[$orderItem->order->payment_type_id] ?? 'Unknown';

        //     // Add portion name
        //     $orderItem->portion_name = $orderItem->portion->name ?? 'Unknown';

        //     // Include delivery_man_id from the order
        //     $orderItem->delivery_man_name = $orderItem->order->delivery_man_id;

        //     // Optionally include customer details if needed
        //     $orderItem->customer = $orderItem->order->customer;

        //     return $orderItem;
        // });

        $orders = $orders->map(function ($order) use ($paymentTypes) {
            // Add payment type to the order
            $order->payment_type = $paymentTypes[$order->payment_type_id] ?? 'Unknown';

            $order->grand_total = $order->orderItems->sum('total');

            $order->delivery_cost = $order->delivery_cost - ($order->delivery_cost * 0.2);

            return $order;
        });

        return response()->json($orders);
    }

    public function orderDetail($id)
    {
        $order = Order::with([
            'orderItems',
            'customer'
        ])
            ->findOrFail($id);

        $paymentTypes = config('payment_types.types');

        $order->payment_type = $paymentTypes[$order->payment_type_id] ?? 'Unknown';

        $order->delivery_cost = $order->delivery_cost - ($order->delivery_cost * 0.2);

        $order->order_items = $order->orderItems->map(function ($orderItem) {
            $orderItem->portion_name = $orderItem->portion->name ?? 'Unknown';
            $orderItem->item = $orderItem->item;
            $orderItem->restaurant = $orderItem->item->restaurant;
            return $orderItem;
        });

        return response()->json($order);
    }


    // Take Order function
    public function takeOrder(Request $request, $orderItemId)
    {
        $orderItem = OrderItem::find($orderItemId);

        if (!$orderItem) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($orderItem->status === 'cancel') {
            return response()->json(['error' => 'Order is cancel'], 400);
        }

        if ($orderItem->status === 'delivered') {
            return response()->json(['error' => 'Order already delivered'], 400);
        }

        $orderItem->status = $request->status;
        $orderItem->delivery_man_id = $request->user()->id;
        $orderItem->save();

        return response()->json(['message' => 'Order taken successfully', 'orderItem' => $orderItem]);
    }

    public function markDelivered($orderId)
    {
        $order = Order::with('orderItems')->findOrFail($orderId);

        // $order->delivery_man_id = auth()->id();
        $order->update(['status' => 'delivered']);
        $order->save();

        $order->orderItems()->update(['status' => 'delivered']);

        return response()->json([
            'message' => 'Order marked as delivered successfully.',
            'order_id' => $orderId
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $order = Order::with('orderItems')->findOrFail($id);
        $newStatus = $request->status;

        if ($order->status !== 'cancel') {
            $order->update(['status' => $newStatus]);
        } else {
            return response()->json([
                'error' => 'Order is already cancelled and cannot be updated.',
                'order_id' => $id
            ], 400);
        }

        $updatedCount = $order->orderItems()
            ->where('status', '!=', 'cancel')
            ->update(['status' => $newStatus]);

        return response()->json([
            'message' => "Order is successfully {$newStatus}. {$updatedCount} item(s) updated.",
            'order_id' => $id
        ]);
    }
}
