<?php

use App\Http\Middleware\IsUser;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backend\Admin\AdminController;

Route::get('/', function () {
    return view('welcome');
});

/// User Routes 
// Group of routes for authenticated users only, protected by the IsUser middleware.
Route::middleware(['auth', IsUser::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

/// Admin Routes 
// Group of routes for authenticated admins only, protected by the IsAdmin middleware.
Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.index');
    })->name('admin.dashboard');
});

// Logout route for admin 
Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');
// Admin Profile
Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
// Store Admin Profile
Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
