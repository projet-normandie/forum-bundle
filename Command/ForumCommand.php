<?php

namespace ProjetNormandie\ForumBundle\Command;

use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ProjetNormandie\ForumBundle\Service\ForumService;

class ForumCommand extends Command
{
    private $forumService;

    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('pn-forum:forum')
            ->setDescription('Command for forum')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'What do you want to do?'
            )
            ->addOption(
                'idForum',
                null,
                InputOption::VALUE_OPTIONAL,
                ''
            );
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $function = $input->getArgument('function');

        switch ($function) {
            case 'maj-parent':
                $id = $input->getOption('idForum');
                $this->forumService->majParent($id);
                break;
        }

        return 0;
    }
}
