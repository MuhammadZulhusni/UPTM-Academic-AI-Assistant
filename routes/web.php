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
use App\Http\Controllers\Backend\SuperAdmin\SuperAdminController;
use App\Http\Controllers\Backend\SuperAdmin\SuperAdminTemplateController;
use App\Http\Controllers\Backend\SuperAdmin\SuperAdminDocumentController;

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
        Route::get('/user/template/{id}', 'UserDetailsTemplate')->name('user.details.template');
        Route::post('/user/content/generate/{id}', 'UserContentGenerate')->name('user.content.generate');
        Route::get('/user/document', 'UserDocument')->name('user.document');
        Route::get('/edit/user/document/{id}', 'EditUserDocument')->name('edit.user.document');
        Route::post('/user/update/document/{id}', 'UserUpdateDocument')->name('user.update.document');
        Route::delete('/delete/user/document/{id}', 'DeleteUserDocument')->name('delete.user.document');
        Route::post('/ai/suggestion', 'AISuggestion')->name('user.ai.suggestion');
    });
});

// Admin Routes
Route::middleware(['auth', IsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');

    Route::controller(AdminController::class)->group(function () {
        Route::get('/profile', 'AdminProfile')->name('admin.profile');
        Route::post('/profile/store', 'AdminProfileStore')->name('admin.profile.store');
        Route::get('/change/password', 'AdminChangePassword')->name('admin.change.password');
        Route::post('/password/update', 'AdminPasswordUpdate')->name('admin.password.update');
        Route::get('/users', 'AdminUsers')->name('admin.users');
        Route::get('/logout', 'AdminLogout')->name('admin.logout');
        Route::delete('/users/delete/{id}', 'AdminDeleteUser')->name('admin.user.delete');
    });

    Route::controller(TemplateController::class)->group(function () {
        Route::get('/template', 'AdminTemplate')->name('admin.template');
        Route::get('/add/template', 'AddTemplate')->name('add.template');
        Route::post('/store/template', 'StoreTemplate')->name('store.template');
        Route::get('/edit/template/{id}', 'EditTemplate')->name('edit.template');
        Route::post('/update/template/{id}', 'UpdateTemplate')->name('update.template');
        Route::get('/details/template/{id}', 'DetailsTemplate')->name('details.template');
        Route::post('/content/generate/{id}', 'AdminContentGenerate')->name('content.generate');
        Route::post('/templates/delete/{id}', 'DeleteTemplate')->name('delete.template');
        Route::post('/ai/suggestion', 'AISuggestion')->name('ai.suggestion');
        Route::post('/templates/toggle/{id}', 'toggleStatus')->name('admin.template.toggle');
    });

    Route::controller(DocumentController::class)->group(function () {
        Route::get('/document', 'AdminDocument')->name('admin.document');
        Route::get('/edit/document/{id}', 'EditAdminDocument')->name('edit.admin.document');
        Route::post('/update/document/{id}', 'AdminUpdateDocument')->name('admin.update.document');
        Route::delete('/delete/document/{id}', 'DeleteAdminDocument')->name('delete.admin.document');
    });
});

// Super Admin Routes
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'Dashboard'])->name('superadmin.dashboard');
    
    Route::controller(SuperAdminController::class)->group(function () {
        Route::get('/profile', 'Profile')->name('superadmin.profile');
        Route::post('/profile/store', 'ProfileStore')->name('superadmin.profile.store');
        Route::get('/change/password', 'ChangePassword')->name('superadmin.change.password');
        Route::post('/password/update', 'PasswordUpdate')->name('superadmin.password.update');
        Route::get('/logout', 'Logout')->name('superadmin.logout');
        Route::post('/superadmin/check-email', 'checkEmail')->name('superadmin.check.email');
    });

    
    Route::get('/users', [SuperAdminController::class, 'Users'])->name('superadmin.users');
    Route::get('/users/edit/{id}', [SuperAdminController::class, 'EditUser'])->name('superadmin.user.edit');
    Route::post('/users/update/{id}', [SuperAdminController::class, 'UpdateUser'])->name('superadmin.user.update');
    Route::post('/users/toggle-status/{id}', [SuperAdminController::class, 'ToggleUserStatus'])->name('superadmin.user.toggle.status');
    Route::delete('/users/delete/{id}', [SuperAdminController::class, 'DeleteUser'])->name('superadmin.user.delete');
    Route::get('/reset-password', [SuperAdminController::class, 'ResetPasswordPage'])->name('superadmin.reset.password');
    Route::post('/send-reset-link', [SuperAdminController::class, 'SendResetLink'])->name('superadmin.send.reset.link');
    Route::get('/superadmin/users/search', [SuperAdminController::class, 'searchUsers'])->name('superadmin.users.search');
    Route::get('/superadmin/create/user', [SuperAdminController::class, 'CreateUser'])->name('superadmin.create.user');
    Route::post('/superadmin/store/user', [SuperAdminController::class, 'StoreUser'])->name('superadmin.store.user');
    // Admin Activity Tracking Routes
    Route::get('/admin-activities', [SuperAdminController::class, 'AdminActivities'])->name('superadmin.admin.activities');
    Route::get('/admin-activity/{id}', [SuperAdminController::class, 'AdminActivityDetails'])->name('superadmin.admin.activity.details');
    Route::get('/admin-activities/export', [SuperAdminController::class, 'ExportAdminActivities'])->name('superadmin.admin.activities.export');
    // Activity Settings Routes (NEW)
    Route::get('/activity-settings', [SuperAdminController::class, 'ActivitySettings'])->name('superadmin.activity.settings');
    Route::post('/activity-settings/update', [SuperAdminController::class, 'UpdateActivitySettings'])->name('superadmin.activity.settings.update');
    Route::post('/activity-manual-cleanup', [SuperAdminController::class, 'ManualCleanup'])->name('superadmin.activity.manual.cleanup');
    
    Route::controller(SuperAdminTemplateController::class)->group(function () {
        Route::get('/template', 'Index')->name('superadmin.template');
        Route::get('/add/template', 'Create')->name('superadmin.add.template');
        Route::post('/store/template', 'Store')->name('superadmin.store.template');
        Route::get('/edit/template/{id}', 'Edit')->name('superadmin.edit.template');
        Route::post('/update/template/{id}', 'Update')->name('superadmin.update.template');
        Route::get('/details/template/{id}', 'Show')->name('superadmin.details.template');
        Route::post('/content/generate/{id}', 'ContentGenerate')->name('superadmin.content.generate');
        Route::post('/templates/delete/{id}', 'Destroy')->name('superadmin.delete.template');
        Route::post('/template/toggle-status/{id}', 'ToggleStatus')->name('superadmin.template.toggle-status');
        Route::post('/ai/suggestion', 'SuperAdmingetAISuggestion')->name('superadmin.ai.suggestion');
    });
    
    Route::controller(SuperAdminDocumentController::class)->group(function () {
        Route::get('/document', 'Index')->name('superadmin.document');
        Route::get('/edit/document/{id}', 'Edit')->name('superadmin.edit.document');
        Route::post('/update/document/{id}', 'Update')->name('superadmin.update.document');
        Route::delete('/delete/document/{id}', 'Destroy')->name('superadmin.delete.document');
        Route::get('/retention-settings',  'DocumentSettings')->name('superadmin.document.settings');
        Route::post('/document-settings/update','UpdateDocumentSettings')->name('superadmin.document.settings.update');
        Route::post('/document-manual-cleanup','ManualDocumentCleanup')->name('superadmin.document.manual.cleanup');
    });
});

// Laravel UI Routes (Default)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';