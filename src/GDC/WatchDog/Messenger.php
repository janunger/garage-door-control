<?php

namespace GDC\WatchDog;

class Messenger
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $environment;

    public function __construct(\Swift_Mailer $mailer, $environment)
    {
        $this->mailer = $mailer;
        $this->environment = $environment;
    }

    public function send($state, \DateTime $stateChangeDate)
    {
        $message = \Swift_Message::newInstance();
        $message->setSubject('GDC Status [' . $this->environment . ']');
        $message->setFrom('outgoing_vo9h9r7y@friedelsbruecke.de', 'Garage Door Control');
        $message->setTo('ju@juit.de', 'Jan Unger');
        $message->setBody('Status: ' . $state . ' seit ' . $stateChangeDate->format('Y-m-d H:i:s'));

        $this->mailer->send($message);
    }
}
