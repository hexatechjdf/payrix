<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\MappingController;




Route::name('admin.')->middleware(['auth'])->group(function () {

    Route::get('/', [IndexController::class, 'index'])->name('index');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'save'])->name('settings.save');

    Route::name('mappings.')->prefix('mappings')->group(function () {
        Route::get('/offices', [MappingController::class, 'offices'])->name('offices');
        Route::get('/offices/fetch/data', [MappingController::class, 'fetchOfficeMappingData'])->name('offices.fetch.data');
        Route::post('/offices/store/data', [MappingController::class, 'storeOfficeMappingData'])->name('offices.store.data');
    });

});

