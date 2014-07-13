<?php

namespace GDC\WatchDog;

use DateTime;

class Messenger
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $senderAddress;

    /**
     * @var string
     */
    private $senderName;

    /**
     * @var string
     */
    private $recipientAddress;

    /**
     * @var string
     */
    private $recipientName;

    public function __construct(\Swift_Mailer $mailer, $senderAddress, $senderName, $recipientAddress, $recipientName)
    {
        $this->mailer = $mailer;
        $this->senderAddress = $senderAddress;
        $this->senderName = $senderName;
        $this->recipientAddress = $recipientAddress;
        $this->recipientName = $recipientName;
    }

    public function sendMessageOnWatchdogRestart()
    {
        $this->send('Watchdog restarted');
    }

    public function sendMessageOnDoorOpening()
    {
        $this->send('Door opening');
    }

    public function sendMessageAfterDoorClosed()
    {
        $this->send('Door closed');
    }

    public function sendHardwareError()
    {
        $this->send('Hardware error');
    }

    private function send($subject)
    {
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        // TODO: Make dynamic
        $message->setFrom($this->senderAddress, $this->senderName);
        $message->setTo($this->recipientAddress, $this->recipientName);
        $message->setBody((new DateTime())->format('Y-m-d H:i:s'));

        $this->mailer->send($message);
    }
}
