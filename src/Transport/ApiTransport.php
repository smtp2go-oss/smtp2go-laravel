<?php

namespace SMTP2GO\Transport;

use SMTP2GO\Types\Mail\Address;
use SMTP2GO\Types\Mail\Attachment;
use Illuminate\Support\Facades\Http;
use SMTP2GO\Types\Mail\CustomHeader;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\RawMessage;
use SMTP2GO\Types\Mail\InlineAttachment;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\MessageConverter;
use SMTP2GO\Collections\Mail\AddressCollection;
use SMTP2GO\Collections\Mail\AttachmentCollection;
use SMTP2GO\Service\Mail\Send as SMTP2GOMailSendService;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Header\MetadataHeader;
use Symfony\Component\Mailer\Transport\TransportInterface;

class ApiTransport extends AbstractTransport
{
    public function __construct(private \SMTP2GO\ApiClient $client)
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

    protected function doSend(SentMessage $sentMessage): void
    {
        $email = MessageConverter::toEmail($sentMessage->getOriginalMessage());

        $envelope = $sentMessage->getEnvelope();

        $service = new SMTP2GOMailSendService(
            new Address($envelope->getSender()->getAddress(), $envelope->getSender()->getName()),
            new AddressCollection(
                array_map(
                    fn ($recipient) => new Address($recipient->getAddress(), $recipient->getName()),
                    $envelope->getRecipients(),
                )
            ),
            $email->getSubject(),
            $email->getHtmlBody()
        );
        $service->setTextBody($email->getTextBody() ?? '');

        //ccs
        foreach ($email->getCc() as $cc) {
            $service->addAddress('cc', new Address($cc->getAddress(), $cc->getName()));
        }

        //bcss
        foreach ($email->getBcc() as $bcc) {
            $service->addAddress('bcc', new Address($bcc->getAddress(), $bcc->getName()));
        }

        //external attchments
        /** @var \Symfony\Component\Mime\Part\DataPart $attachment */
        $attachmentCollection = new AttachmentCollection();
        foreach ($email->getAttachments() as $attachment) {
            if ($attachment->getDisposition() === 'inline') {
                $theAttachment = new InlineAttachment($attachment->getFilename(), $attachment->getBody(), $attachment->getMediaType());
            } else {
                $theAttachment = new Attachment($attachment->getFilename(), $attachment->getBody(), $attachment->getMediaType());
            }
            $attachmentCollection->add($theAttachment);
        }

        /**  @todo headers - to confirm which ones we can set - this maybe a config option
         * where you predefined any headers you want to send
         */
        // $headers = $email->getHeaders();

        foreach ($email->getHeaders()->all() as $header) {            
            if (is_a($header, MetadataHeader::class)) {
                $service->addCustomHeader(new CustomHeader($header->getName(), $header->getBodyAsString()));
            }            
        }

        if (!$this->client->consume($service)) {
            $sentMessage->appendDebug($this->client->getResponseBody(false));
            throw new TransportException('Unable to send message via SMTP2GO');
        }
    }

    public function __toString(): string
    {
        return 'api://smtp2go';
    }
}
