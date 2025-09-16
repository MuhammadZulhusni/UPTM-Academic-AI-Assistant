<?php

use App\Http\Middleware\IsUser;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backend\Admin\AdminController;
use App\Http\Controllers\Backend\Admin\DocumentController;
use App\Http\Controllers\Backend\Admin\TemplateController;

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

// Group all admin
Route::controller(AdminController::class)->group(function () {
    // Admin Profile Routes
    Route::get('/admin/profile', 'AdminProfile')->name('admin.profile');
    Route::post('/admin/profile/store', 'AdminProfileStore')->name('admin.profile.store');
    // Admin Password Routes
    Route::get('/admin/change/password', 'AdminChangePassword')->name('admin.change.password');
    Route::post('/admin/password/update', 'AdminPasswordUpdate')->name('admin.password.update');
    // Admin User Routes
    Route::get('/admin/users', 'AdminUsers')->name('admin.users');
    // Admin Logout Route
    Route::get('/admin/logout', 'AdminLogout')->name('admin.logout');
    // Admin delete user
    Route::delete('/admin/users/delete/{id}', 'AdminDeleteUser')->name('admin.user.delete');
    // Admin Dashboard For Display Counts
    Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
});

// Route for Template Management
Route::controller(TemplateController::class)->group(function(){
    Route::get('/admin/template', 'AdminTemplate')->name('admin.template'); 
    Route::get('/add/template', 'AddTemplate')->name('add.template'); 
    Route::post('/store/template', 'StoreTemplate')->name('store.template');
    Route::get('/edit/template/{id}', 'EditTemplate')->name('edit.template'); 
    Route::post('/update/template/{id}', 'UpdateTemplate')->name('update.template'); 
    Route::get('/details/template/{id}', 'DetailsTemplate')->name('details.template');
    Route::post('/content/generate/{id}', 'AdminContentGenerate')->name('content.generate');
});

// Route for Document Management
Route::controller(DocumentController::class)->group(function(){
    Route::get('/admin/document', 'AdminDocument')->name('admin.document'); 
    Route::get('/edit/admin/document/{id}', 'EditAdminDocument')->name('edit.admin.document'); 
    Route::post('/admin/update/document/{id}', 'AdminUpdateDocument')->name('admin.update.document'); 
    Route::delete('/delete/admin/document/{id}', 'DeleteAdminDocument')->name('delete.admin.document');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
