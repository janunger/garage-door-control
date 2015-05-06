<?php

namespace GDCBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunEventLoopCommand extends ContainerAwareCommand
{
    /**
     * @var \DateTime
     */
    private $startDate;

    protected function configure()
    {
        $this->setName('gdc:event-loop:run');
        $this->setDescription('Run event loop that processes commands and sends mails about door state');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startDate = new \DateTime();
        $container = $this->getContainer();
        $commandProcessor = $container->get('gdc.command_processor');
        $watchdog = $container->get('gdc.watchdog');
        $sensorLogger = $this->getContainer()->get('gdc.sensor_logger');

        while (true) {
            $commandProcessor->execute();
            $watchdog->execute();
            $sensorLogger->execute();
            $this->flushMailQueue();
            if ($this->mustForceRestart()) {
                break;
            }
            usleep(300000);
        }
    }

    private function flushMailQueue()
    {
        /** @var $spoolTransport \Swift_SpoolTransport */
        $spoolTransport = $this->getContainer()->get('mailer')->getTransport();
        $spool = $spoolTransport->getSpool();
        $smtpTransport = $this->getContainer()->get('swiftmailer.transport.real');

        $spool->flushQueue($smtpTransport);
        $smtpTransport->stop();
    }

    /**
     * @return bool
     */
    private function mustForceRestart()
    {
        $now = new \DateTime();

        return $now->format('Hi') === '0500' && $this->calculateUpTime() > 2 * 60 * 60;
    }

    /**
     * @return int
     */
    private function calculateUpTime()
    {
        $now = new \DateTime();

        return (int)$now->format('U') - (int)$this->startDate->format('U');
    }
}
