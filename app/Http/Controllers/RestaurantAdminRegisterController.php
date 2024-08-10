<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RestaurantAdminRegisterController extends Controller
{
    public function show()
    {
        return view('auth.restaurant-admin-register');
    }

    public function registerRestaurant(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'restaurant_name' => 'required|string|max:255',
            'shop_type' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'rating' => 'required|numeric|min:0|max:5',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png', // added max file size
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'restaurant_owner',
        ]);

        // Handle file upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        // Create the restaurant
        Restaurant::create([
            'name' => $request->restaurant_name,
            'shop_type' => $request->shop_type,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'rating' => $request->rating,
            'description' => $request->description,
            'is_popular' => $request->is_popular,
            'logo' => $logoPath, // Store the path in the database
            'created_by' => $user->id,
            'is_approved' => 0,
        ]);

        return redirect('/admin/login')->with('success', 'Registration successful. Please log in.');
    }
}
