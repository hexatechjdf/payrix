<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



Route::group(['as' => 'location.', 'prefix' => 'location'], function () {
    Route::get('/integrations', [SubaccountController::class, 'index'])->name('integrations');
    Route::get('/integrations/list', [SubaccountController::class, 'list'])->name('fetch.integrations.list');

    Route::post('/token/by/company', [SubaccountController::class, 'setToken']);
});
