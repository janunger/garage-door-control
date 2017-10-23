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
            if ($this->mustForceRestart()) {
                break;
            }
        }
    }

    private function mustForceRestart(): bool
    {
        return date('Hi') === '0500' && $this->uptime() > 70;
    }

    private function uptime(): int
    {
        return time() - $this->timeStart;
    }

}
