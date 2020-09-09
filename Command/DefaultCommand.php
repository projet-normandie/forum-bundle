<?php

namespace ProjetNormandie\ForumBundle\Command;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class DefaultCommand extends Command
{
    protected static $defaultName = 'pn-forum:default';

    private $sglLoggerEnabled = false;
    private $stack;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function init(InputInterface $input)
    {
        if ($input->getOption('debug')) {
            $this->sglLoggerEnabled = true;
            // Start setup logger
            $doctrineConnection = $this->em->getConnection();
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
