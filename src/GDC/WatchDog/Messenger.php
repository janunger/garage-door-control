<?php

namespace GDC\WatchDog;

use GDC\Door\State;
use GDCBundle\Event\WatchDogRestartedEvent;

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
        $this->mailer           = $mailer;
        $this->senderAddress    = $senderAddress;
        $this->senderName       = $senderName;
        $this->recipientAddress = $recipientAddress;
        $this->recipientName    = $recipientName;
    }

    public function onWatchdogRestart(WatchDogRestartedEvent $event)
    {
        switch ($event->getCurrentState()) {
            case State::CLOSED():
                $state = 'door closed';
                break;
            case State::OPENED():
                $state = 'DOOR OPENED';
                break;
            default:
                $state = 'DOOR UNKNOWN';
                break;
        }
        $this->send('Watchdog restarted, ' . $state);
    }

    public function onDoorOpening()
    {
        $this->send('Door opening');
    }

    public function onDoorClosed()
    {
        $this->send('Door closed');
    }

    public function onHardwareError()
    {
        $this->send('Hardware error');
    }

    private function send($subject)
    {
        $now  = date('Y-m-d H:i:s');
        $text = $subject . ' - ' . $now;

        $message = \Swift_Message::newInstance();
        $message->setSubject($text);
        $message->setFrom($this->senderAddress, $this->senderName);
        $message->setTo($this->recipientAddress, $this->recipientName);
        $message->setBody($text);

        $this->mailer->send($message);
    }
}
