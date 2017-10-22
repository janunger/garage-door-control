<?php

namespace GDCBundle\Console;

use Symfony\Component\Console\Output\OutputInterface;

class Application extends \Symfony\Bundle\FrameworkBundle\Console\Application
{
    /**
     * Renders a caught exception.
     *
     * @param \Exception      $e      An exception instance
     * @param OutputInterface $output An OutputInterface instance
     */
    public function renderException($e, $output)
    {
        $output->writeln(date(DATE_ISO8601));
        parent::renderException($e, $output);
    }
}