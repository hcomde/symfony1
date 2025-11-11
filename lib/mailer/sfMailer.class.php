<?php

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\NullTransport;
use Symfony\Component\Mime\Email;


/**
 * Mailer based on Symfony 7's mailer component.
 *
 * @author h.com networkers
 */
class sfMailer
{
    public const REALTIME = 'realtime';
    public const NONE = 'none';

    protected string $strategy = 'realtime';
    protected string $address = '';
    protected bool $force = false;

    private ?MailerInterface $mailer = null;
    private string $charset = 'UTF-8';
    private ?sfMailerLogger $logger = null;

    public function __construct(sfEventDispatcher $dispatcher, $options)
    {
        // options
        $options = array_merge([
            'charset' => 'UTF-8',
            'logging' => false,
            'delivery_strategy' => self::REALTIME,
            'dsn' => 'smtp://null:null@example.com:25',
        ], $options);

        $constantName = 'sfMailer::' . strtoupper($options['delivery_strategy']);
        $this->strategy = defined($constantName) ? constant($constantName) : false;
        if (!$this->strategy) {
            throw new InvalidArgumentException(sprintf('Unknown mail delivery strategy "%s" (should be one of realtime or none)', $options['delivery_strategy']));
        }

        $this->charset = $options['charset'];
        $dsn = $options['dsn'];
        if($dsn && $this->strategy === self::REALTIME) {
            $transport = Transport::fromDsn($dsn);
        } else {
            $transport = new NullTransport();
        }
        $this->mailer = new Mailer($transport);

        if($options['logging']) {
            $this->logger = new sfMailerLogger();
        }

        $dispatcher->notify(new sfEvent($this, 'mailer.configure'));
    }

    public function getLogger(): ?sfMailerLogger
    {
        return $this->logger;
    }

    public function getRealtimeTransport()
    {
        return null;
    }

    public function setRealtimeTransport($transport)
    {
    }

    public function getDeliveryStrategy(): string
    {
        return $this->strategy;
    }

    public function getDeliveryAddress()
    {
        return null;
    }

    public function setDeliveryAddress($address)
    {
    }

    public function compose(?string $from = null, string|array|null $to = null, ?string $subject = null, ?string $body = null): Email
    {
        if(! is_array($to)) {
            $to = [$to];
        }
        return new Email()
            ->from($from)
            ->to(...$to)
            ->subject($subject)
            ->text($body, $this->charset);
    }

    public function composeAndSend(string $from, string|array $to, string $subject, string $body): void
    {
        $this->send($this->compose($from, $to, $subject, $body));
    }

    public function sendNextImmediately()
    {
        return null;
    }

    public function send(Email $email): void
    {
        if ($this->mailer) {
            $this->mailer->send($email);
            if($this->logger) {
                $this->logger->log($email);
            }
        }
    }

    public function flushQueue(&$failedRecipients = null)
    {
        return null;
    }

    public function getSpool()
    {
        return null;
    }

}
