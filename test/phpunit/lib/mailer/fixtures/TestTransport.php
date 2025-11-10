<?php

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;

/**
 * A test transport for Symfony Mailer 7 that records sent messages and never performs I/O.
 */
class TestTransport implements TransportInterface
{
    /** @var SentMessage[] */
    private array $sent = [];

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        // Minimal SentMessage creation requires an Envelope; when not provided, build a basic one
        if (null === $envelope) {
            // Build the simplest possible envelope by introspecting the headers
            // but for tests, we can use reflection to create Envelope is internal; however
            // Envelope class is public with constructor (addresses). Let's use it if available.
            if (class_exists('Symfony\\Component\\Mailer\\Envelope')) {
                $r = new ReflectionClass('Symfony\\Component\\Mailer\\Envelope');
                if ($r->isInstantiable()) {
                    // Try to construct with empty addresses (allowed since Symfony 5.4+ for tests)
                    try {
                        $envelope = $r->newInstanceArgs([null, []]);
                    } catch (\Throwable $e) {
                        // Fallback: use default sender/recipients via helpers
                        $envelope = new Envelope(new Address('from@example.com'), [new Address('to@example.com')]);
                    }
                }
            }
        }

        if (!$envelope) {
            $envelope = new Envelope(new Address('from@example.com'), [new Address('to@example.com')]);
        }

        $sent = new SentMessage($message, $envelope);
        $this->sent[] = $sent;

        return $sent;
    }

    /**
     * Returns the number of messages recorded by this transport.
     */
    public function getSentCount(): int
    {
        return count($this->sent);
    }

    /**
     * Returns the last sent message instance or null if none.
     */
    public function getLastSent(): ?SentMessage
    {
        return $this->sent ? end($this->sent) : null;
    }

    public function __toString(): string
    {
        return 'test+transport://';
    }
}
