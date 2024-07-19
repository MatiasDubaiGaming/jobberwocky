<?php

namespace App\Providers;

use App\Events\JobCreated;
use App\Listeners\SendJobCreatedNotification;
use App\Services\JobberwockyExtraSourceService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(JobberwockyExtraSourceService::class, function ($app) {
            return new JobberwockyExtraSourceService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            JobCreated::class,
            SendJobCreatedNotification::class,
        );
    }
}
