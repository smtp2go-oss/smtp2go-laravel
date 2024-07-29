<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\CoversClass;
use SMTP2GO\Transport\Providers\Laravel\ServiceProvider;
use Tests\BaseTestCase;


#[CoversClass(ServiceProvider::class)]
class LaravelMailerTest extends BaseTestCase
{
    public function testSmtp2goMailerIsUsed()
    {
        // dd(config('mail'));
        Mail::mailer();
    }
}
