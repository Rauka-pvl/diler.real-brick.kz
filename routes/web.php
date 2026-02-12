<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DealerController;
use App\Http\Controllers\Admin\ObjectController as AdminObjectController;
use App\Http\Controllers\Admin\DealerPackageController as AdminDealerPackageController;
use App\Http\Controllers\Admin\PromoMaterialController as AdminPromoMaterialController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dealer\CabinetController;
use App\Http\Controllers\Dealer\ChangePasswordController;
use App\Http\Controllers\Dealer\ClientController as DealerClientController;
use App\Http\Controllers\Dealer\ObjectController as DealerObjectController;
use App\Http\Controllers\Dealer\ProductController as DealerProductController;
use App\Http\Controllers\Dealer\DealerPackageController as DealerDealerPackageController;
use App\Http\Controllers\Dealer\ProfileController;
use App\Http\Controllers\Dealer\PromoMaterialController as DealerPromoMaterialController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dealer.cabinet');
    }
    return redirect()->route('login');
});

Route::get('/test', function () {
    try {

        $response = Http::get(
            'https://realbrick.bitrix24.kz/rest/152/9erom1wk19zdbdbz/catalog.product.list.json',
            [
                'select' => ['id', 'name', 'price', 'active', 'available', 'dateCreate', 'iblockId'],
                'filter' => [
                    'active' => 'Y',
                    'iblockId' => 16,
                ],
                'order' => [
                    'name' => 'ASC',
                ],
                'start' => 0,
            ]
        );

        if ($response->failed()) {
            throw new \Exception($response->body());
        }

        return $response->json();

        // foreach ($data['result']['products'] ?? [] as $item) {

        //     echo "ID: {$item['id']}\n";
        //     echo "Name: {$item['name']}\n";
        //     echo "Active: {$item['active']}\n";
        //     echo "Available: {$item['available']}\n";
        //     echo "Date Created: " . \Carbon\Carbon::parse($item['dateCreate'])->toAtomString() . "\n";
        //     echo "----------------------\n";
        // }
    } catch (\Throwable $e) {
        echo "Error: {$e->getMessage()}";
    }
});

Route::get('register', fn() => redirect()->route('login'))->name('register');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('dealers/search', [DealerController::class, 'search'])->name('dealers.search');
    Route::resource('dealers', DealerController::class);
    Route::get('clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::resource('clients', ClientController::class);
    Route::get('objects', [AdminObjectController::class, 'index'])->name('objects.index');
    Route::get('objects/{object}', [AdminObjectController::class, 'show'])->name('objects.show');
    Route::resource('promo-materials', AdminPromoMaterialController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('dealer-package', AdminDealerPackageController::class)->name('dealer-package');
});

Route::middleware(['auth', 'dealer'])->prefix('dealer')->name('dealer.')->group(function () {
    Route::get('change-password', [ChangePasswordController::class, 'create'])->name('change-password');
    Route::post('change-password', [ChangePasswordController::class, 'store'])->name('change-password.store');
    Route::get('cabinet', CabinetController::class)->name('cabinet');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::resource('clients', DealerClientController::class);
    Route::get('objects/clients-search', [DealerObjectController::class, 'searchClients'])->name('objects.clients-search');
    Route::patch('objects/{object}/stage', [DealerObjectController::class, 'updateStage'])->name('objects.update-stage');
    Route::resource('objects', DealerObjectController::class);
    Route::get('products/catalog-children', [DealerProductController::class, 'catalogChildren'])->name('products.catalog-children');
    Route::get('products', [DealerProductController::class, 'index'])->name('products.index');
    Route::get('promo-materials', [DealerPromoMaterialController::class, 'index'])->name('promo-materials.index');
    Route::get('promo-materials/{promoMaterial}/download', [DealerPromoMaterialController::class, 'download'])->name('promo-materials.download');
    Route::get('dealer-package', DealerDealerPackageController::class)->name('dealer-package');
});
