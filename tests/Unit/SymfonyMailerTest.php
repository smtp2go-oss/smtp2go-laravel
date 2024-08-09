<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mime\Email;
use Symfony\Component\Translation\Provider\Dsn as ProviderDsn;

class SymfonyMailerTest extends TestCase
{

    public function testMailIsSentAndContentIsOk(): void
    {
        $factory = new \SMTP2GO\Transport\SMTP2GOTransportFactory();

        $dsnString = file_get_contents(__DIR__ . '/../fixtures/MAILER_DSN');
        $providerDsn = new ProviderDsn($dsnString);

        $dsn = new Dsn($providerDsn->getScheme(), $providerDsn->getHost(), $providerDsn->getUser());

        /** @var \SMTP2GO\Transport\ApiTransport  */
        $transport = $factory->create($dsn);

        $client = $transport->getApiClient();

        $client->setHttpClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(
                    200,
                    [],
                    json_encode(['message' => 'success'])
                ),
            ]),
        ]));

        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from('sender@test')
            ->to(new \Symfony\Component\Mime\Address('recipient@test', 'the recipient'))
            ->subject('Test subject')
            ->text('Test body')
            ->html('<p>Test body</p>');

        $mailer->send($email);

        $smtp2goMailService = $transport->getService();

        $this->assertEquals('sender@test', $smtp2goMailService->getSender());
        $this->assertEquals('the recipient <recipient@test>', $smtp2goMailService->getRecipients()[0]);
        $this->assertEquals('Test subject', $smtp2goMailService->getSubject());
        $this->assertEquals('Test body', $smtp2goMailService->getTextBody());
        $this->assertEquals('<p>Test body</p>', $smtp2goMailService->getHtmlBody());

        $this->assertJson(
            $client->getResponseBody(false),
            json_encode(['message' => 'success'])
        );
    }
}
