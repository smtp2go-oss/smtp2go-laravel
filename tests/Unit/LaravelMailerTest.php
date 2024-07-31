<?php

namespace Tests\Unit;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\CoversClass;
use SMTP2GO\Transport\Providers\Laravel\ServiceProvider;
use Tests\BaseTestCase;


#[CoversClass(ServiceProvider::class)]
class LaravelMailerTest extends BaseTestCase
{
    public function testSmtp2goMailerIsUsed()
    {
        /** @var \Illuminate\Mail\Mailer $mailer */
        $mailer = Mail::mailer();
        $transport = $mailer->getSymfonyTransport();
        $this->assertInstanceOf(\SMTP2GO\Transport\ApiTransport::class, $transport);
    }

    public function testItSendsAnEmail()
    {
        /** @var \Illuminate\Mail\Mailer $mailer */
        Mail::fake();

        $mailable = $this->makeMailable();

        Mail::to('to@example')->send($mailable);
        Mail::assertSent(Mailable::class, function (\Illuminate\Mail\Mailable $mail) {
            return $mail->hasFrom('to@example') &&
                $mail->hasTo('from@example') &&
                $mail->subject === 'Test subject';
        });
    }

    public function testSuccessWithMockGuzzleClient()
    {
        /** @var \Illuminate\Mail\Mailer $mailer */
        $mailer = Mail::mailer();
        /** @var \SMTP2GO\Transport\ApiTransport */
        $transport = $mailer->getSymfonyTransport();

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

        $mailable = $this->makeMailable();

        Mail::to(env('TEST_RECIPIENT'))->send($mailable);

        $this->assertJson(
            $client->getResponseBody(false),
            json_encode(['message' => 'success'])
        );
    }

    public function testFailureWithMockGuzzleClient()
    {
        /** @var \Illuminate\Mail\Mailer $mailer */
        $mailer = Mail::mailer();
        /** @var \SMTP2GO\Transport\ApiTransport */
        $transport = $mailer->getSymfonyTransport();

        $client = $transport->getApiClient();

        $client->setHttpClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(
                    400,
                    [],
                    json_encode(['message' => 'failure'])
                ),
            ]),
        ]));

        $mailable = $this->makeMailable();

        $this->expectException(\Symfony\Component\Mailer\Exception\TransportException::class);

        Mail::to(env('TEST_RECIPIENT'))->send($mailable);

        $this->assertJson(
            $client->getResponseBody(false),
            json_encode(['message' => 'failure'])
        );
    }

    private function makeMailable(): Mailable
    {
        $mailable = new Mailable();
        $mailable->subject('Test subject');
        $mailable->html('<p>Test body</p>');
        $mailable->from('to@example');
        $mailable->to('from@example');
        return $mailable;
    }
}
