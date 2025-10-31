<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', function () {
    return view('index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/',[HomeController::class,'index'])->name('home.index');

Route::middleware('auth')->group(function () {
    Route::get('/account.dashboard', [UserController::class,'index'])->name('user.index');
});

Route::middleware('auth', AuthAdmin::class)->group(function () {
    Route::get('/admin', [AdminController::class,'index'])->name('admin.index');
    
    // Admin Brand Routes
    Route::get('/admin/brands', [AdminController::class,'brands'])->name('admin.brands.index');
    Route::get('admin.brands.add', [AdminController::class,'add_brands'])->name('admin.brands.add');
    Route::post('admin.brands.store', [AdminController::class,'brand_store'])->name('admin.brands.store');
    Route::get('admin.brands.edit/{id}', [AdminController::class,'brand_edit'])->name('admin.brands.edit');
    Route::put('admin.brands.update/{id}', [AdminController::class,'brand_update'])->name('admin.brands.update');
    Route::delete('admin.brands.delete/{id}', [AdminController::class,'brand_delete'])->name('admin.brands.delete');

    // Admin Category Routes
    Route::get('/admin/categories', [AdminController::class,'categories'])->name('admin.categories.index');
    Route::get('/admin/categories.add', [AdminController::class,'category_add'])->name('admin.categories.add');
    Route::post('admin.categories.store', [AdminController::class,'category_store'])->name('admin.categories.store');
    Route::get('admin.categories.edit/{id}', [AdminController::class,'category_edit'])->name('admin.categories.edit');
    Route::put('admin.categories.update/{id}', [AdminController::class,'category_update'])->name('admin.categories.update');
    Route::delete('admin.categories.delete/{id}', [AdminController::class,'category_delete'])->name('admin.categories.delete');
});
