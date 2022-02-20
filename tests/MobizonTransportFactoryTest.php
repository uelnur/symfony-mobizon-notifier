<?php

namespace Uelnur\SymfonyMobizonNotifier\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Transport\Dsn;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Uelnur\SymfonyMobizonNotifier\MobizonTransport;
use Uelnur\SymfonyMobizonNotifier\MobizonTransportFactory;

class MobizonTransportFactoryTest extends TestCase {
    public function testFactory() {
        $client = $this->createMock(HttpClientInterface::class);

        $factory = new MobizonTransportFactory(null, $client);

        $dsn = new Dsn('mobizon://token@default');
        $supports = $factory->supports($dsn);
        $this->assertTrue($supports);

        $transport = $factory->create($dsn);
        $this->assertInstanceOf(MobizonTransport::class, $transport);

        $dsn = new Dsn('mobizon2://token@default');
        $supports = $factory->supports($dsn);
        $this->assertFalse($supports);
    }
}
