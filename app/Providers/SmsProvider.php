<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Jokam\AfricasTalkingGateway;

class SmsProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Jokam\AfricasTalkingGateway', function(){
            $username = config('sms.username');
            $api_key = config('sms.api_key');

            return new AfricasTalkingGateway($username,$api_key);
        });

        $this->app->bind('Jokam\Interfaces\SmsInterface',
            'Jokam\Repositories\SmsRepository');

        $this->app->bind('sms','Jokam\AfricasTalkingGateway');
    }
}
