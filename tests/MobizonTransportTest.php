<?php

namespace Uelnur\SymfonyMobizonNotifier\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Uelnur\SymfonyMobizonNotifier\MobizonTransport;

class MobizonTransportTest extends TestCase {
    public function testTransport(): void {
        $messageID = '1';
        $phone = '+71112223344';
        $subject = 'subject_text';
        $token = 'token';

        $client = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'data'=>['messageId' => $messageID],
        ]));

        $client->method('request')->willReturn($response);

        $transport = new MobizonTransport($token, $client);

        $message = new SmsMessage($phone, $subject);
        $message->transport('mobizon');
        $this->assertTrue($transport->supports($message));

        $sentMessage = $transport->send($message);
        $this->assertEquals('1', $sentMessage->getMessageId());
    }
}
