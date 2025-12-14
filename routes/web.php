<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentController as ControllersPaymentController;
//use App\Http\Controllers\PaystackController;
use App\Http\Controllers\SearchController;
use App\Http\Middleware\AuthAdmin;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Surfsidemedia\Shoppingcart\Facades\Cart;

// Route::get('/migrate', function () {
//     Artisan::call('migrate:fresh', ['--force' => true]);
//     return 'Migrations completed successfully';
// });

Route::get('/test-sale-products', function () {
    return \App\Models\Product::whereNotNull('sale_price')
        ->where('sale_price', '>', 0)
        ->select('id', 'name', 'sale_price')
        ->get();
});

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
Route::get('/shop',[ShopController::class,'index'])->name('shop.index');
Route::get('/shop/{product_slug}',[ShopController::class,'product_details'])->name('shop.product.details');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// Cart Routes
Route::get('/cart',[CartController::class,'index'])->name('cart.index');
Route::post('/cart',[CartController::class,'add_to_cart'])->name('cart.add');
Route::put('/cart/qty/increase/{rowId}', [CartController::class, 'increase_cart_quantity'])
    ->name('cart.qty.increase');
Route::put('/cart/qty/decrease/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('/cart/remove/{rowId}', [CartController::class,'remove_item'])->name('cart.item.remove');
Route::delete('/cart/clear', [CartController::class,'empty_cart'])->name('cart.empty');

// Apply coupons to cart route
Route::post('/cart/apply-coupon',[CartController::class,'apply_coupon_code'])->name('cart.coupon.apply');
Route::delete('/cart/remove-coupon',[CartController::class,'remove_coupon_code'])->name('cart.coupon.remove');

// Wishlist Routes
Route::post('/wishlist/add', [App\Http\Controllers\WishlistController::class,'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist', [App\Http\Controllers\WishlistController::class,'index'])->name('wishlist.index');
Route::delete('/wishlist/item/remove/{rowId}', [App\Http\Controllers\WishlistController::class,'remove_item'])->name('wishlist.item.remove');
Route::delete('/wishlist/clear', [App\Http\Controllers\WishlistController::class,'empty_wishlist'])->name('wishlist.item.clear');
Route::post('/wishlist/move-to-cart/{rowId}', [App\Http\Controllers\WishlistController::class,'move_to_cart'])->name('wishlist.move.to.cart');

// Route for checkout page
Route::get('/checkout', [App\Http\Controllers\CartController::class,'checkout'])->name('cart.checkout');
Route::post('/place-an-order', [App\Http\Controllers\CartController::class,'place_an_order'])->name('cart.place.an.order');
Route::get('/payment/callback', [CartController::class, 'paystackCallback'])->name('payment.callback');
Route::get('/order-confirmation', [App\Http\Controllers\CartController::class,'order_confirmation'])->name('cart.order-confirmation');

// Route for contact us page
Route::get('/contact-us', [App\Http\Controllers\HomeController::class,'contact'])->name('home.contact');
Route::post('/contact-store', [App\Http\Controllers\HomeController::class,'contact_store'])->name('home.contact.store');


// user Route Group
Route::middleware('auth')->group(function () {
    Route::get('/account-dashboard', [UserController::class,'index'])->name('user.index');
    Route::get('/account-orders', [UserController::class,'orders'])->name('user.orders');
    Route::get('/account-orders/{order_id}/details', [UserController::class,'order_details'])->name('user.order-details');
    Route::put('/account-orders/cancel-order', [UserController::class,'order_cancel'])->name('user.order.cancel');
});

Route::middleware(['auth', AuthAdmin::class])->group(function () {
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


    // Admin Products Routes
    Route::get('/admin/products', [AdminController::class,'products'])->name('admin.products.index');
    Route::get('/admin/products.add', [AdminController::class,'product_add'])->name('admin.products.add');
    Route::post('admin.products.store', [AdminController::class,'product_store'])->name('admin.products.store');
     Route::get('admin.products.edit/{id}', [AdminController::class,'product_edit'])->name('admin.products.edit');
     Route::put('admin.products.update/{id}', [AdminController::class,'product_update'])->name('admin.products.update');
     Route::delete('admin.products.delete/{id}', [AdminController::class,'product_delete'])->name('admin.products.delete');

    //  Admin Coupons Routes
    Route::get('/admin/coupons', [AdminController::class,'coupons'])->name('admin.coupons.index');
    Route::get('/admin/coupons.add', [AdminController::class,'coupon_add'])->name('admin.coupons.add');
    Route::post('admin.coupons.store', [AdminController::class,'coupon_store'])->name('admin.coupons.store');
    Route::get('admin.coupons.edit/{id}', [AdminController::class,'coupon_edit'])->name('admin.coupons.edit');
    Route::put('admin.coupons.update/{id}', [AdminController::class,'coupon_update'])->name('admin.coupons.update');
    Route::delete('admin.coupons.delete/{id}', [AdminController::class,'coupon_delete'])->name('admin.coupons.delete');

    // Admin Orders Route
    Route::get('/admin/orders', [AdminController::class,'orders'])->name('admin.orders.index');
    Route::get('/admin/orders/{order_id}', [AdminController::class,'order_details'])->name('admin.orders.details');
    Route::put('/admin/orders/update-status',[AdminController::class,'update_order_status'])->name('admin.orders.status.update');

    // Route to display home page slides
    Route::get('/admin/slides', [AdminController::class,'slides'])->name('admin.slides.index');
    Route::get('/admin/slides/add', [AdminController::class,'slides_add'])->name('admin.slides.add');
    Route::post('/admin/slides/store', [AdminController::class,'slides_store'])->name('admin.slides.store');
    Route::get('admin.slides.edit/{id}', [AdminController::class,'slides_edit'])->name('admin.slides.edit');
    Route::put('admin.slides.update/{id}', [AdminController::class,'slides_update'])->name('admin.slides.update');
    Route::delete('admin.slides.delete/{id}', [AdminController::class,'slides_delete'])->name('admin.slides.delete');

    // Route to view and delete all contact request
    Route::get('/admin/contact', [AdminController::class,'contacts'])->name('admin.contacts');
    Route::delete('/admin/contact/delete/{id}', [AdminController::class,'contact_delete'])->name('admin.contact.delete');

    // Paystack payment routes
// Route::get('/payment', [PaystackController::class, 'paystackInitialize'])
//      ->name('paystack.init');

// Route::post('/paystack/verify', [PaystackController::class, 'paystackVerify'])
//      ->name('paystack.verify');

// Route::post('/paystack/webhook', [PaystackController::class, 'paystackWebhook'])
//      ->name('paystack.webhook');

 });