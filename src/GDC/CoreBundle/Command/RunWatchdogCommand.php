<?php

namespace GDC\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunWatchdogCommand extends ContainerAwareCommand
{
    /**
     * @var \DateTime
     */
    private $startDate;

    protected function configure()
    {
        $this->setName('gdc:watchdog:run');
        $this->setDescription('Run watchdog and send mails about door state');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startDate = new \DateTime();
        $watchdog = $this->getContainer()->get('gdc_core.watchdog');
        while (true) {
            $watchdog->execute();
            $this->flushMailQueue();
            if ($this->mustForceRestart()) {
                break;
            }
            sleep(1);
        }
    }

    private function flushMailQueue()
    {
        /** @var $transport \Swift_SpoolTransport */
        $transport = $this->getContainer()->get('mailer')->getTransport();

        /** @var $spool \Swift_Spool */
        $spool = $transport->getSpool();

        $spool->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
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
