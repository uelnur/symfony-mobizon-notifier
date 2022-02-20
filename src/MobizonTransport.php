<?php

namespace Uelnur\SymfonyMobizonNotifier;

use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\TransportInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function json_decode;

class MobizonTransport implements TransportInterface {
    public const NAME = 'mobizon';

    public function __construct(private string $apiKey, private HttpClientInterface $client) {
    }

    /**
     * @param \Symfony\Component\Notifier\Message\MessageInterface $message
     * @return \Symfony\Component\Notifier\Message\SentMessage|null
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function send(MessageInterface $message): ?SentMessage {
        if ( !$message instanceof SmsMessage ) {
            throw new UnsupportedMessageTypeException(self::NAME, SmsMessage::class, $message);
        }

        $url = sprintf('https://api.mobizon.kz/service/Message/SendSmsMessage?apiKey=%s', $this->apiKey);
        $response = $this->client->request('POST', $url, [
            'body' => [
                'recipient' => $message->getPhone(),
                'text' => $message->getSubject(),
            ],
        ]);

        $json = json_decode($response->getContent(), true);

        $sentMessage = new SentMessage($message, self::NAME);
        $messageID = $json['data']['messageId'] ?? null;
        $sentMessage->setMessageId($messageID);

        return $sentMessage;
    }

    public function supports(MessageInterface $message): bool {
        return $message->getTransport() === self::NAME && $message instanceof SmsMessage;
    }

    public function __toString(): string {
        return self::NAME;
    }
}
