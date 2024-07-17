<?php

use Symfony\Component\Mailer\Transport\Dsn;
use SMTP2GO\Transport\Transport\ApiTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\Transport\AbstractTransportFactory;

final class SMTP2GOTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        // parse the given DSN, extract data/credentials from it
        // $scheme = $dsn->getScheme();

        $key = $dsn->getUser();
        $client = new SMTP2GO\ApiClient($key);


        // and then, create and return the transport
        return new ApiTransport($client);
    }

    protected function getSupportedSchemes(): array
    {
        // this supports DSN starting with `SMTP2GO://`
        return ['SMTP2GO'];
    }
}
