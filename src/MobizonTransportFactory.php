<?php

namespace Uelnur\SymfonyMobizonNotifier;

use Symfony\Component\Notifier\Exception\IncompleteDsnException;
use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Transport\Dsn;
use Symfony\Component\Notifier\Transport\TransportInterface;

class MobizonTransportFactory extends AbstractTransportFactory {
    protected function getSupportedSchemes(): array {
        return [MobizonTransport::NAME];
    }

    public function create(Dsn $dsn): TransportInterface {
        $scheme = $dsn->getScheme();

        if (MobizonTransport::NAME !== $scheme) {
            throw new UnsupportedSchemeException($dsn, MobizonTransport::NAME, $this->getSupportedSchemes());
        }
        if (null === $dsn->getUser()) {
            throw new IncompleteDsnException('Missing api key.', $dsn->getOriginalDsn());
        }
        $token = $dsn->getUser();

        return new MobizonTransport($token, $this->client);
    }

}
