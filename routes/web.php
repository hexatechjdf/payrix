<?php

use App\Http\Controllers\OAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::prefix('authorization')->name('crm.')->group(function () {
    Route::get('/crm/oauth/callback', [OAuthController::class, 'crmCallback'])->name('oauth_callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



});

use App\Jobs\Pulling\CustomField\ManageFiltersJob;
use App\Jobs\Pulling\Customers\PullDataJob;
use App\Jobs\Pulling\Customers\PullFlagsJob;
use App\Jobs\Pulling\Customers\PullSubscriptionsJob;

Route::get('/test/job', function(){

    // $params['dateAdded'] = [
    //     'operator' => '>=',
    //     'value' => [now()]
    // ];
    // $params['dateUpdated'] = [
    //     'operator' => '>=',
    //     'value' => [now()]
    // ];

    // ManageFiltersJob::dispatchSync('services');
    // return;

    PullSubscriptionsJob::dispatchSync('flags');
    return;
    PullFlagsJob::dispatchSync('flags');
    return;
    PullDataJob::dispatchSync();
    return;




    ManageFiltersJob::dispatchSync('services');
    // ManageFiltersJob::dispatchSync('flags');
});




require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/location.php';
