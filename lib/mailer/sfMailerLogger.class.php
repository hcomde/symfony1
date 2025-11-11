<?php

use Symfony\Component\Mime\Email;


/**
 * Description of sfMailerLogger
 *
 * @author h.com networkers
 */
class sfMailerLogger
{

    private $messages = [];

    public function log(Email $email): void
    {
        $this->messages[] = $email;
    }

    public function countMessages(): int
    {
        return count($this->messages);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

}
