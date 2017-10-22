<?php

declare(strict_types=1);

namespace JUIT\GDC\WatchDog;

use JUIT\GDC\Door\State;
use JUIT\GDC\Event\WatchDogRestartedEvent;

class Messenger
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    public function __construct(\Swift_Mailer $mailer, MessageFactory $messageFactory)
    {
        $this->mailer         = $mailer;
        $this->messageFactory = $messageFactory;
    }

    public function onWatchdogRestart(WatchDogRestartedEvent $event)
    {
        $text = 'Watchdog restarted, ';
        switch ($event->getDoorState()->getValue()) {
            case State::CLOSED:
                $text .= 'door closed';
                break;
            case State::OPENED:
                $text .= 'DOOR OPENED';
                break;
            default:
                $text .= 'DOOR UNKNOWN';
                break;
        }
        $this->sendMessage($text);
    }

    public function onDoorOpening()
    {
        $this->sendMessage('DOOR OPENING');
    }

    public function onDoorClosed()
    {
        $this->sendMessage('Door closed');
    }

    public function onHardwareError()
    {
        $this->sendMessage('HARDWARE ERROR');
    }

    private function sendMessage(string $text)
    {
        $text .= ' - ' . date('Y-m-d H:i:s');
        $message = $this->messageFactory->createMessage($text);
        $this->mailer->send($message);
    }
}
