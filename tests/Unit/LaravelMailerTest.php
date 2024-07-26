<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\CoversClass;
use SMTP2GO\Transport\Providers\Laravel\ServiceProvider;

#[CoversClass(ServiceProvider::class)]
class LaravelMailerTest extends \Orchestra\Testbench\TestCase
{
    public function testSmtp2goMailerIsUsed()
    {
        Mail::fake();
    }
}
