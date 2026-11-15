
<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\Admin\ShippingSettingController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\ReviewController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;


// =====================================================
// HALAMAN PUBLIK
// =====================================================

Route::get('/', function () {
    $categories = Category::query()
        ->select(['cat_id', 'cat_title'])
        ->orderBy('cat_title')
        ->limit(6)
        ->get();

    $products = Product::query()
        ->select(['id', 'product_cat', 'brand', 'name', 'price', 'stock', 'image', 'created_at'])
        ->with('category:cat_id,cat_title')
        ->where('stock', '>', 0)
        ->latest()
        ->limit(8)
        ->get();

    return view('welcome', compact('categories', 'products'));
})->name('home');

// REGISTER
Route::get('/register', [RegisterController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [RegisterController::class, 'register']);

// LOGIN
Route::get('/login', [LoginController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login']);

// LOGOUT
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// LUPA PASSWORD
Route::get('/lupa-password', [PasswordResetRequestController::class, 'create'])
    ->name('password.request');

Route::post('/lupa-password', [PasswordResetRequestController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('password.request.store');

// WEBHOOK MIDTRANS
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])
    ->name('midtrans.webhook');


// =====================================================
// HALAMAN ADMIN
// =====================================================

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', AdminDashboardController::class)
            ->name('dashboard');

        // Pencarian Lokasi RajaOngkir
        Route::get('/shipping-settings/search-location', [
            ShippingSettingController::class,
            'searchLocation'
        ])->name('shipping-settings.search-location');

        // Pengaturan Lokasi Pengiriman
        Route::get('/shipping-settings', [
            ShippingSettingController::class,
            'edit'
        ])->name('shipping-settings.edit');

        Route::put('/shipping-settings', [
            ShippingSettingController::class,
            'update'
        ])->name('shipping-settings.update');

        // Produk
        Route::resource('products', ProductController::class);

        // Kategori
        Route::get('categories/search', [
            CategoryController::class,
            'search'
        ])->name('categories.search');

        Route::resource('categories', CategoryController::class);

        // Pesanan
        Route::resource('orders', OrderController::class)
            ->only(['index', 'show', 'update']);

        // Pengguna
        Route::resource('users', UserController::class)
            ->except(['create', 'store', 'show']);

        // Persetujuan Reset Password
        Route::get('password-resets', [
            PasswordResetRequestController::class,
            'index'
        ])->name('password-resets.index');

        Route::post('password-resets/{resetRequest}/approve', [
            PasswordResetRequestController::class,
            'approve'
        ])->name('password-resets.approve');

        Route::post('password-resets/{resetRequest}/reject', [
            PasswordResetRequestController::class,
            'reject'
        ])->name('password-resets.reject');

        // Letakkan di dalam grup auth dan role:admin.
        Route::get('/notifications', [AdminNotificationController::class, 'index'])
            ->name('notifications.index');

        Route::patch('/notifications/read-all', [AdminNotificationController::class, 'readAll'])
            ->name('notifications.read-all');

        Route::patch('/notifications/{notification}/read', [AdminNotificationController::class, 'read'])
            ->name('notifications.read');
    });


// =====================================================
// HALAMAN USER
// =====================================================

Route::middleware(['auth', 'role:user'])
    ->group(function () {

        Route::get('/dashboard', function () {
            $user = request()->user();

            $categories = Category::query()
                ->select(['cat_id', 'cat_title'])
                ->orderBy('cat_title')
                ->get();

            $products = Product::query()
                ->select(['id', 'product_cat', 'brand', 'name', 'price', 'stock', 'image', 'created_at'])
                ->with('category:cat_id,cat_title')
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->where('stock', '>', 0)
                ->whereHas('reviews')
                ->orderByDesc('reviews_avg_rating')
                ->orderByDesc('reviews_count')
                ->latest()
                ->limit(4)
                ->get();

            $orders = $user->orders()
                ->select(['id', 'status', 'total', 'created_at'])
                ->withCount('items')
                ->latest()
                ->limit(3)
                ->get();

            $orderCount = $user->orders()->count();
            $activeOrderCount = $user->orders()
                ->whereIn('status', ['pending', 'processing', 'shipped'])
                ->count();

            return view('dashboard', compact(
                'categories',
                'products',
                'orders',
                'orderCount',
                'activeOrderCount'
            ));
        })->name('dashboard');

        Route::get('/products', [ProductController::class, 'userIndex'])
            ->name('products.index');

        Route::get('/products/{product}', [ProductController::class, 'userShow'])
            ->name('products.show');


        // =================================================
        // PROFIL
        // =================================================

        Route::get('/profile', [
            ProfileController::class,
            'show'
        ])->name('profile');

        Route::get('/profile/edit', [
            ProfileController::class,
            'edit'
        ])->name('profile.edit');

        Route::put('/profile', [
            ProfileController::class,
            'update'
        ])->name('profile.update');

        Route::post('/profile/photo', [
            ProfileController::class,
            'updatePhoto'
        ])->name('profile.photo.update');

        Route::delete('/profile/photo', [
            ProfileController::class,
            'destroyPhoto'
        ])->name('profile.photo.destroy');


        // =================================================
        // BUKU ALAMAT
        // =================================================

        Route::get('/addresses/create', [
            AddressController::class,
            'create'
        ])->name('addresses.create');

        Route::post('/addresses', [
            AddressController::class,
            'store'
        ])->name('addresses.store');

        Route::get('/addresses/{address}/edit', [
            AddressController::class,
            'edit'
        ])->name('addresses.edit');

        Route::put('/addresses/{address}', [
            AddressController::class,
            'update'
        ])->name('addresses.update');

        Route::delete('/addresses/{address}', [
            AddressController::class,
            'destroy'
        ])->name('addresses.destroy');

        Route::put('/addresses/{address}/default', [
            AddressController::class,
            'setDefault'
        ])->name('addresses.default');


        // =================================================
        // API WILAYAH
        // =================================================

        Route::prefix('/api/wilayah')->name('wilayah.')->group(function () {

            Route::get('/provinces', [
                AddressController::class,
                'provinces'
            ])->name('provinces');

            Route::get('/regencies/{code}', [
                AddressController::class,
                'regencies'
            ])->name('regencies');

            Route::get('/districts/{code}', [
                AddressController::class,
                'districts'
            ])->name('districts');

            Route::get('/villages/{code}', [
                AddressController::class,
                'villages'
            ])->name('villages');
        });


        // =================================================
        // KATEGORI
        // =================================================

        Route::get('/categories', [
            CategoryController::class,
            'userIndex'
        ])->name('categories.index');

        Route::get('/categories/{cat_id}', [
            CategoryController::class,
            'userShow'
        ])->name('categories.show');


        // =================================================
        // KERANJANG
        // =================================================

        Route::get('/cart', [
            CartController::class,
            'index'
        ])->name('cart.index');

        Route::post('/cart/add/{product}', [
            CartController::class,
            'add'
        ])->name('cart.add');

        // Simpan pilihan checkbox produk
        Route::post('/cart/selection', [
            CartController::class,
            'updateSelection'
        ])->name('cart.selection');

        // Pilih semua produk
        Route::post('/cart/select-all', [
            CartController::class,
            'selectAll'
        ])->name('cart.select-all');

        Route::post('/cart/{id}/increase', [
            CartController::class,
            'increase'
        ])->name('cart.increase');

        Route::post('/cart/{id}/decrease', [
            CartController::class,
            'decrease'
        ])->name('cart.decrease');

        Route::delete('/cart/{id}', [
            CartController::class,
            'remove'
        ])->name('cart.remove');


        // =================================================
        // CHECKOUT
        // =================================================

        Route::get('/checkout', [
            CheckoutController::class,
            'index'
        ])->name('checkout.index');

        Route::post('/checkout/shipping-cost', [
            CheckoutController::class,
            'shippingCost'
        ])->name('checkout.shipping-cost');

        Route::post('/checkout', [
            CheckoutController::class,
            'store'
        ])->name('checkout.store');


        // =================================================
        // PESANAN SAYA
        // =================================================

        Route::get('/orders', [
            UserOrderController::class,
            'index'
        ])->name('orders.index');

        Route::get('/orders/{order}', [
            UserOrderController::class,
            'show'
        ])->name('orders.show');

        Route::post('/orders/{order}/pay', [
            UserOrderController::class,
            'pay'
        ])->name('orders.pay');

        Route::post('/orders/{order}/cancel', [
            UserOrderController::class,
            'cancel'
        ])->name('orders.cancel');


        // =================================================
        // PUSAT BANTUAN & CHATBOT
        // =================================================

        Route::get('/bantuan', [
            KnowledgeController::class,
            'index'
        ])->name('knowledge.index');

        Route::post('/chatbot', [
            ChatbotController::class,
            'send'
        ])->middleware('throttle:30,1')
          ->name('chatbot.send');

        Route::post('/products/{product}/reviews', [
            ReviewController::class,
            'store'
        ])->name('products.reviews.store');
    });
