<?php

namespace phpunit\lib\mailer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require_once __DIR__ . '/fixtures/TestTransport.php';

class sfMailerTest extends TestCase
{
    private function createDispatcher(): \sfEventDispatcher
    {
        return new \sfEventDispatcher();
    }

    public function testInvalidDeliveryStrategyThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new \sfMailer($this->createDispatcher(), ['delivery_strategy' => 'foo']);
    }

    public function testDeliveryStrategyIsParsed(): void
    {
        $mailer = new \sfMailer($this->createDispatcher(), ['delivery_strategy' => 'realtime']);
        $this->assertSame('realtime', $mailer->getDeliveryStrategy());

        $mailerNone = new \sfMailer($this->createDispatcher(), ['delivery_strategy' => 'none']);
        $this->assertSame('none', $mailerNone->getDeliveryStrategy());
    }

    public function testComposeBuildsEmailWithProvidedFieldsAndCharset(): void
    {
        $mailer = new \sfMailer($this->createDispatcher(), [
            'delivery_strategy' => 'none',
            'charset' => 'ISO-8859-1',
            'dsn' => null,
        ]);

        $email = $mailer->compose('from@example.com', 'to@example.com', 'Subject', 'Body');
        $this->assertInstanceOf(Email::class, $email);
        $this->assertSame('from@example.com', $email->getFrom()[0]->toString());
        $this->assertSame('to@example.com', $email->getTo()[0]->toString());
        $this->assertSame('Subject', $email->getSubject());
        $this->assertSame('Body', $email->getTextBody());
        $this->assertSame('ISO-8859-1', $email->getTextCharset());
    }

    public function testComposeAndSendUsesInjectedTransportAndDoesNotDeliverRealEmail(): void
    {
        $dispatcher = $this->createDispatcher();
        $mailer = new \sfMailer($dispatcher, [
            'delivery_strategy' => 'realtime',
            // Make sure the constructor does not try to build a real transport from DSN when we are going to inject
            'dsn' => null,
        ]);

        $transport = new \TestTransport();
        // Inject our test transport into the internal Mailer instance via reflection
        $r = new \ReflectionClass($mailer);
        $p = $r->getProperty('mailer');
        $p->setAccessible(true);
        $p->setValue($mailer, new Mailer($transport));

        $mailer->composeAndSend('from@example.com', 'to@example.com', 'Subject', 'Body');
        $this->assertSame(1, $transport->getSentCount(), 'One message should have been captured by the test transport');

        $sent = $transport->getLastSent();
        $this->assertNotNull($sent);
        $this->assertSame("Subject", $sent->getOriginalMessage()->getHeaders()->get('Subject')->getBodyAsString());
    }
}
