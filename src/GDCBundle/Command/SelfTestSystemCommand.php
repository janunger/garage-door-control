<?php

namespace GDCBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfTestSystemCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('gdc:system:self-test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $piFace = $this->getContainer()->get('gdc.piface');
        foreach (range(0, 7) as $i) {
            $output->write("PIN $i: ");
            $output->writeln($piFace->getInputPin($i)->isOn() ? 'On' : 'Off');
        }
    }
}