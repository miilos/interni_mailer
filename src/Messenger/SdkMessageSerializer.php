<?php

namespace App\Messenger;

use App\Message\SendSdkEmail;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class SdkMessageSerializer implements SerializerInterface
{
    public function __construct(
        private EncoderInterface $encoder,
        private DecoderInterface $decoder,
    ) {}

    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'] ?? null;

        if (!$body) {
            throw new MessageDecodingFailedException('Invalid message body!');
        }

        $data = $this->decoder->decode($body, 'json');

        if (!$data) {
            throw new MessageDecodingFailedException('Empty message body!');
        }

        $message = new SendSdkEmail(
            subject: $data['subject'],
            from: $data['from'],
            to: $data['to'],
            cc: $data['cc'],
            bcc: $data['bcc'],
            body: $data['body'],
            bodyTemplate: $data['bodyTemplate'],
            emailTemplate: $data['emailTemplate'],
            variables: $data['variables'],
        );

        return new Envelope($message);
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        return [
            'body' => $this->encoder->encode([
                'id' => $message->getId(),
                'subject' => $message->getSubject(),
                'from' => $message->getFrom(),
                'to' => $message->getTo(),
                'cc' => $message->getCc(),
                'bcc' => $message->getBcc(),
                'body' => $message->getBody(),
                'bodyTemplate' => $message->getBodyTemplate(),
                'emailTemplate' => $message->getEmailTemplate(),
                'variables' => $message->getVariables(),
            ], 'json'),
            'headers' => [],
        ];
    }
}
