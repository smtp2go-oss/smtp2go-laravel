<?php

use PHPUnit\Framework\Attributes\CoversClass;
use \SMTP2GO\Transport\Transport\SMTP2GOTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use SMTP2GO\Transport\Transport\ApiTransport;
use Symfony\Component\Translation\Provider\Dsn as ProviderDsn;

#[CoversClass(SMTP2GOTransportFactory::class)]
class FactoryTest extends \PHPUnit\Framework\TestCase
{

    public function testCreate(): void
    {
        $dsnString = file_get_contents(__DIR__ . '/fixtures/MAILER_DSN');
        $dsn = new ProviderDsn($dsnString);
        $factory = new SMTP2GOTransportFactory();
        $dsn = new Dsn($dsn->getScheme(), $dsn->getHost(), $dsn->getUser());
        $transport = $factory->create($dsn);
        $this->assertInstanceOf(ApiTransport::class, $transport);
    }
}