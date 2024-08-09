<?php

namespace SMTP2GO\Transport;

use Symfony\Component\Mailer\Transport\Dsn;
use SMTP2GO\Transport\ApiTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\Transport\AbstractTransportFactory;

final class SMTP2GOTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        // parse the given DSN, extract data/credentials from it
        $apiKey = $dsn->getUser();
        $client = new \SMTP2GO\ApiClient($apiKey);


        // and then, create and return the transport
        return new ApiTransport($client);
    }

    protected function getSupportedSchemes(): array
    {
        // this supports DSN starting with `SMTP2GO://`
        return ['SMTP2GO'];
    }
}
