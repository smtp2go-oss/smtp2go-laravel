<?php

namespace Tests;

use Illuminate\Contracts\Config\Repository;

class BaseTestCase extends \Orchestra\Testbench\TestCase
{
    use \Orchestra\Testbench\Concerns\WithWorkbench;
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        tap($app['config'], function (Repository $config) {
            $config->set('mail.mailers.smtp2go', [
                'transport' => 'smtp2go',
                'key' => env('SMTP2GO_API_KEY'),
            ]);
            $config->set('mail.default', 'smtp2go');
        });
    }

    public function tearDown(): void
    {
        parent::tearDown();

        restore_error_handler();
        restore_exception_handler();
    }
}
