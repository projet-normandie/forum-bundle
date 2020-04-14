<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\Logging\DebugStack;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class DefaultCommand extends ContainerAwareCommand
{
    private $sglLoggerEnabled = false;
    private $stack;

    protected function init(InputInterface $input)
    {
        if ($input->getOption('debug')) {
            $this->sglLoggerEnabled = true;
            // Start setup logger
            $doctrine = $this->getContainer()->get('doctrine');
            $doctrineConnection = $doctrine->getConnection();
            $this->stack = new DebugStack();
            $doctrineConnection->getConfiguration()->setSQLLogger($this->stack);
            // End setup logger
        }
    }

    protected function end(OutputInterface $output)
    {
        if ($this->sglLoggerEnabled) {
            $output->writeln(sprintf('%s queries', count($this->stack->queries)));
        }
    }
}
