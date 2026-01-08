<?php

use App\Http\Controllers\Location\SubaccountController;
use App\Http\Controllers\Location\MappingController;
use Illuminate\Support\Facades\Route;


Route::group(['as' => 'location.', 'prefix' => 'location'], function () {
    Route::get('/settings', [SubaccountController::class, 'index'])->name('index');


});

Route::name('mappings.')->prefix('mappings')->group(function () {
    Route::name('employees.')->prefix('employees')->group(function () {
        Route::get('/', [MappingController::class, 'employees'])->name('index');
        Route::get('/fetch/data', [MappingController::class, 'fetchEmployeeMappingData'])->name('fetch.data');
        Route::post('/store/data', [MappingController::class, 'storeEmployeeMappingData'])->name('store.data');
    });

    Route::name('calendar.')->prefix('calendar')->group(function () {
        Route::get('/', [MappingController::class, 'calendars'])->name('index');
        Route::get('/fetch/data', [MappingController::class, 'fetchCalendarMappingData'])->name('fetch.data');
        Route::post('/store/data', [MappingController::class, 'storeCalendarMappingData'])->name('store.data');
    });
});

Route::group(['as' => 'location.', 'prefix' => 'location', 'middleware' => 'verify.ghl.sso'], function () {
    Route::middleware('role:2')->group(function () {
        Route::get('/verify', [SubaccountController::class, 'verify'])->name('verify');
    });
});

Route::post('/decrypt-sso', [SubaccountController::class, 'verifySso'])->name('verify.sso');
