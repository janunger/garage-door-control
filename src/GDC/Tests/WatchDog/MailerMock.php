<?php

namespace GDC\Tests\WatchDog;

use Swift_Mime_Message;

class MailerMock extends \Swift_Mailer
{
    public function __construct()
    {}

    /**
     * @var Swift_Mime_Message|null
     */
    private $message = null;

    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->message = $message;

        return 1;
    }

    /**
     * @return null|Swift_Mime_Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}