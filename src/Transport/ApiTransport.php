<?php

namespace SMTP2GO\Transport;

use SMTP2GO\Types\Mail\Address;
use SMTP2GO\Types\Mail\CustomHeader;
use SMTP2GO\Types\Mail\FileAttachment;
use SMTP2GO\Types\Mail\InlineAttachment;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\MessageConverter;
use SMTP2GO\Collections\Mail\AddressCollection;
use SMTP2GO\Collections\Mail\AttachmentCollection;
use Symfony\Component\Mailer\Header\MetadataHeader;
use SMTP2GO\Service\Mail\Send as SMTP2GOMailSendService;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Exception\TransportException;

class ApiTransport extends AbstractTransport
{
    private $service;
    public function __construct(private \SMTP2GO\ApiClient $client, private array $options = [])
    {
        parent::__construct();
    }

    /**
     * Get the underlying SMTP2GO API client
     */
    public function getApiClient(): \SMTP2GO\ApiClient
    {
        return $this->client;
    }

    /**
     * Get the underlying SMTP2GO service that builds the request consumed by the client
     */
    public function getService(): SMTP2GOMailSendService
    {
        return $this->service;
    }


    protected function doSend(SentMessage $sentMessage): void
    {
        $email = MessageConverter::toEmail($sentMessage->getOriginalMessage());

        $envelope = $sentMessage->getEnvelope();

        $this->service = new SMTP2GOMailSendService(
            new Address($envelope->getSender()->getAddress(), $envelope->getSender()->getName()),
            new AddressCollection(
                array_map(
                    fn($recipient) => new Address($recipient->getAddress(), $recipient->getName()),
                    $envelope->getRecipients(),
                )
            ),
            $email->getSubject(),
            $email->getHtmlBody()
        );
        $this->service->setTextBody($email->getTextBody() ?? '');

        //ccs
        foreach ($email->getCc() as $cc) {
            $this->service->addAddress('cc', new Address($cc->getAddress(), $cc->getName()));
        }

        //bcss
        foreach ($email->getBcc() as $bcc) {
            $this->service->addAddress('bcc', new Address($bcc->getAddress(), $bcc->getName()));
        }

        //external attchments
        /** @var \Symfony\Component\Mime\Part\DataPart $attachment */
        $attachmentCollection = new AttachmentCollection();
        foreach ($email->getAttachments() as $attachment) {
            if ($attachment->getDisposition() === 'inline') {

                $theAttachment = new InlineAttachment($attachment->getFilename(), $attachment->getBody(), $attachment->getMediaType());
            } else {
                $theAttachment = new FileAttachment($attachment->getBody(), $attachment->getFilename());
            }
            $attachmentCollection->add($theAttachment);
        }

        foreach ($email->getHeaders()->all() as $header) {
            if (is_a($header, MetadataHeader::class)) {
                $this->service->addCustomHeader(new CustomHeader($header->getName(), $header->getBodyAsString()));
            }
        }

        if (!empty($this->options['custom_headers'])) {
            foreach ($this->options['custom_headers'] as $headerName => $headerValue) {
                $this->service->addCustomHeader(new CustomHeader($headerName, $headerValue));
            }
        }

        if (!empty($this->options['api_region'])) {
            try {
                $this->client->setApiRegion($this->options['api_region']);
            } catch (\Exception $e) {
                throw new TransportException($e->getMessage());
            }
        }

        if (!$this->client->consume($this->service)) {
            $sentMessage->appendDebug($this->client->getResponseBody(false));
            throw new TransportException('Unable to send message via SMTP2GO');
        }

        $sentMessage->appendDebug($this->client->getResponseBody(false));
    }

    public function __toString(): string
    {
        return 'api://smtp2go';
    }
}
