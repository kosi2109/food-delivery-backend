<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Livewire\RestaurantAdminRegister;
use App\Http\Controllers\RestaurantAdminRegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});


// Route::get('/admin', function () {
//     return redirect('/admin/login');
// });

Route::get('register/restaurant', [RestaurantAdminRegisterController::class, 'show'])->name('register.restaurant.form');
Route::post('register/restaurant', [RestaurantAdminRegisterController::class, 'registerRestaurant'])->name('register.restaurant');