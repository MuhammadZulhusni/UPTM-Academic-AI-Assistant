<?php

use App\Http\Middleware\IsUser;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backend\Admin\AdminController;
use App\Http\Controllers\Backend\Client\UserController;
use App\Http\Controllers\Backend\Admin\DocumentController;
use App\Http\Controllers\Backend\Admin\TemplateController;
use App\Http\Controllers\Backend\Client\UserTemplateController;

// Public Routes
Route::get('/', function () {
    return view('auth.login');
});

// User Routes
Route::middleware(['auth', IsUser::class, 'verified'])->group(function () {
    // User Profile & Account Management
    Route::controller(UserController::class)->group(function () {
        Route::get('/user/dashboard', 'UserDashboard')->name('user.dashboard');
        Route::get('/user/logout', 'UserLogout')->name('user.logout');
        Route::get('/user/profile', 'UserProfile')->name('user.profile');
        Route::post('/user/profile/store', 'UserProfileStore')->name('user.profile.store');
        Route::get('/user/change/password', 'UserChangePassword')->name('user.change.password');
        Route::post('/user/password/update', 'UserPasswordUpdate')->name('user.password.update');
    });

    // User Template & Document Management
    Route::controller(UserTemplateController::class)->group(function () {
        Route::get('/user/template', 'UserTemplate')->name('user.template');
        Route::get('/user/details/template/{id}', 'UserDetailsTemplate')->name('user.details.template');
        Route::post('/user/content/generate/{id}', 'UserContentGenerate')->name('user.content.generate');
        
        Route::get('/user/document', 'UserDocument')->name('user.document');
        Route::get('/edit/user/document/{id}', 'EditUserDocument')->name('edit.user.document');
        Route::post('/user/update/document/{id}', 'UserUpdateDocument')->name('user.update.document');
        Route::delete('/delete/user/document/{id}', 'DeleteUserDocument')->name('delete.user.document');
    });
});


// Admin Routes
Route::middleware(['auth', IsAdmin::class])->prefix('admin')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');

    // Admin Account & User Management
    Route::controller(AdminController::class)->group(function () {
        Route::get('/profile', 'AdminProfile')->name('admin.profile');
        Route::post('/profile/store', 'AdminProfileStore')->name('admin.profile.store');
        Route::get('/change/password', 'AdminChangePassword')->name('admin.change.password');
        Route::post('/password/update', 'AdminPasswordUpdate')->name('admin.password.update');
        Route::get('/users', 'AdminUsers')->name('admin.users');
        Route::get('/logout', 'AdminLogout')->name('admin.logout');
        Route::delete('/users/delete/{id}', 'AdminDeleteUser')->name('admin.user.delete');
    });

    // Template Management
    Route::controller(TemplateController::class)->group(function () {
        Route::get('/template', 'AdminTemplate')->name('admin.template');
        Route::get('/add/template', 'AddTemplate')->name('add.template');
        Route::post('/store/template', 'StoreTemplate')->name('store.template');
        Route::get('/edit/template/{id}', 'EditTemplate')->name('edit.template');
        Route::post('/update/template/{id}', 'UpdateTemplate')->name('update.template');
        Route::get('/details/template/{id}', 'DetailsTemplate')->name('details.template');
        Route::post('/content/generate/{id}', 'AdminContentGenerate')->name('content.generate');
        Route::post('/templates/delete/{id}', 'DeleteTemplate')->name('delete.template');
    });

    // Document Management
    Route::controller(DocumentController::class)->group(function () {
        Route::get('/document', 'AdminDocument')->name('admin.document');
        Route::get('/edit/document/{id}', 'EditAdminDocument')->name('edit.admin.document');
        Route::post('/update/document/{id}', 'AdminUpdateDocument')->name('admin.update.document');
        Route::delete('/delete/document/{id}', 'DeleteAdminDocument')->name('delete.admin.document');
    });
});


// Laravel UI Routes (Default)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
