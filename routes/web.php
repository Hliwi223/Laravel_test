<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/catalogue', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/packs', [PackController::class, 'index'])->name('packs.index');
Route::get('/packs/{pack}', [PackController::class, 'show'])->name('packs.show');

Route::get('/calculator', [CalculatorController::class, 'index'])->name('calculator.index');
Route::post('/calculator/calculate', [CalculatorController::class, 'calculate'])->name('calculator.calculate');
Route::get('/calculator/quote/{quote}', [CalculatorController::class, 'show'])->name('calculator.quote');
Route::get('/calculator/appliances', [CalculatorController::class, 'appliancesJson'])->name('calculator.appliances');

Route::get('/reservation', [ReservationController::class, 'create'])->name('reservation.create');
Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
Route::get('/reservation/{reservation}/confirmation', [ReservationController::class, 'success'])->name('reservation.success');

Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::get('/conditions', [HomeController::class, 'conditions'])->name('conditions');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

/*
|--------------------------------------------------------------------------
| Authentification admin
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Administration (protégée par le middleware 'admin')
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', Admin\ProductController::class)->except(['show']);
    Route::resource('packs', Admin\PackController::class)->except(['show']);
    Route::resource('appliances', Admin\ApplianceController::class)->except(['show', 'edit', 'create']);

    Route::get('/reservations', [Admin\ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [Admin\ReservationController::class, 'show'])->name('reservations.show');
    Route::patch('/reservations/{reservation}/status', [Admin\ReservationController::class, 'updateStatus'])->name('reservations.status');

    Route::get('/availability', [Admin\AvailabilityController::class, 'index'])->name('availability');

    Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
});
