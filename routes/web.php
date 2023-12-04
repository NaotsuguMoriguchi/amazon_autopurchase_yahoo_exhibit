<?php

use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\YahooOrderItemController;

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


Route::get('/', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    
    Route::prefix('item')->group(function () {

        // Register management
        Route::get('/amazon_register/{store_id}', [SettingController::class, 'item_register'])->name('item_register');
		Route::post('/amazon_register/add_amazon_setting', [SettingController::class, 'add_amSetting'])->name('add_amSetting');
        Route::post('/amazon_register/edit_amazon_setting', [SettingController::class, 'edit_amSetting'])->name('edit_amSetting');
		Route::post('/amazon_register/delete_amazon_setting', [SettingController::class, 'delete_amSetting'])->name('delete_amSetting');
		Route::post('/amazon_register/save_register_history', [SettingController::class, 'save_history'])->name('save_register_history');



        // Exhibit management
        Route::get('/yahoo_exhibit/{store_id}', [SettingController::class, 'item_exhibit'])->name('item_exhibit');
        Route::post('/yahoo_exhibit/edit_yahoo_setting', [SettingController::class, 'edit_yaSetting'])->name('edit_yaSetting');



        // Order management
        Route::get('/yahoo_order/{store_id}', [SettingController::class, 'item_order'])->name('item_order');
        Route::get('/yahoo_order/csv_download/{ids}', [SettingController::class, 'csv_download'])->name('csv_download');

	});



    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
