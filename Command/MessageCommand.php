<?php

namespace ProjetNormandie\ForumBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ProjetNormandie\ForumBundle\Service\MessageService;

class MessageCommand extends Command
{
    private $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('pn-forum:message')
            ->setDescription('Command for a message forum')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'What do you want to do?'
            )
            ->addOption(
                'debug',
                'd',
                InputOption::VALUE_NONE,
                'Debug option (sql)'
            );
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $function = $input->getArgument('function');

        switch ($function) {
            case 'maj-position':
                $this->messageService->majPosition();
                break;
        }

        return 0;
    }
}
