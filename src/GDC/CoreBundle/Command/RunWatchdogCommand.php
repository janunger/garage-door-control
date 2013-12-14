<?php

namespace GDC\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunWatchdogCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('gdc:watchdog:run');
        $this->setDescription('Run watchdog and send mails about door state');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->isOtherInstanceRunning()) {
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln('Other instance detected, aborting');
            }
            return;
        }

        $watchdog = $this->getContainer()->get('gdc_core.watchdog');
        while (true) {
            $watchdog->execute();
            $this->sendMails();
            sleep(1);
        }
    }

    private function sendMails()
    {
        $transport = $this->getContainer()->get('mailer')->getTransport();
        if (!$transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }

        $spool = $transport->getSpool();
        if (!$spool instanceof \Swift_MemorySpool) {
            return;
        }

        $spool->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
    }

    private function isOtherInstanceRunning()
    {
        $process = new Process('ps ax | grep gdc:watchdog:run');
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        $outputLines = explode("\n", trim($process->getOutput()));

        return count($outputLines) > 3;
    }
}
