<?php

namespace DeveloperDynamo\PushNotification;

use Illuminate\Support\ServiceProvider;

class PushNotificationProvider extends ServiceProvider
{
    /**
     * Bootstrap the PushNotification services.
     *
     * @return void
     */
    public function boot()
    {
    	$this->publishes([
        	__DIR__.'/config/pushnotification.php' => config_path('pushnotification.php'),
    	]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
