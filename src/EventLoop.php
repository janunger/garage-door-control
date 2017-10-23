<?php

declare(strict_types=1);

namespace JUIT\GDC;

use JUIT\GDC\WatchDog\WatchDog;

class EventLoop
{
    /**
     * @var WatchDog
     */
    private $watchDog;

    /**
     * @var \Swift_SmtpTransport
     */
    private $mailerTransport;

    /**
     * @var int
     */
    private $timeStart;

    public function __construct(WatchDog $watchDog, \Swift_SmtpTransport $mailerTransport)
    {
        $this->watchDog        = $watchDog;
        $this->mailerTransport = $mailerTransport;
    }

    public function run()
    {
        $this->timeStart = time();

        while (true) {
            $this->watchDog->execute();
            usleep(300000);
            if ($this->mailerTransport->isStarted()) {
                $this->mailerTransport->stop();
            }
        }
    }
}
