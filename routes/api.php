<?php

use App\Http\Controllers\JobListingController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'job_listings'], function () {
    Route::get('/', [JobListingController::class, 'index']);
    Route::post('/', [JobListingController::class, 'create']);
    Route::put('{jobListing}', [JobListingController::class, 'update']);
    Route::get('combined_jobs', [JobListingController::class, 'combinedJobs']);
    Route::get('external_jobs', [JobListingController::class, 'fetchExternalJobs']);
    Route::delete('{id}', [JobListingController::class, 'destroy']);
    Route::get('{id}', [JobListingController::class, 'show']);
});

Route::post('subscriptions', [SubscriptionController::class, 'store']);

