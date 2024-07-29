<?php

namespace SMTP2GO\Transport\Providers\Laravel;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot(): void
    {
        $config = config('smtp2go');
        Mail::extend('smtp2go', function () use ($config) {
            
            return (new \SMTP2GO\Transport\SMTP2GOTransportFactory())->create(
                new Dsn(
                    'smtp2go+api',
                    'default',
                    $config['key'],
                    null,
                    null,
                    $config
                )
            );
        });
        $this->publishes([
            __DIR__ . '/../../../config/' => config_path('smtp2go.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/smtp2go.php', 'smtp2go');
    }
}
