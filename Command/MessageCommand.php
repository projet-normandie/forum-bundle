<?php

namespace ProjetNormandie\ForumBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ProjetNormandie\ForumBundle\Repository\MessageRepository;
use ProjetNormandie\ForumBundle\Filter\Bbcode as BbcodeFilter;

class MessageCommand extends DefaultCommand
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($em);
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
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool|int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $function = $input->getArgument('function');

        switch ($function) {
            case 'migrate':
                $this->migrate();
                break;
        }
        $this->end($output);

        return 0;
    }


    /**
     *
     */
    private function migrate()
    {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->em->getRepository('ProjetNormandieForumBundle:Message');

        $bbcodeFiler = new BbcodeFilter();
        $messages = $messageRepository->findAll();
        foreach ($messages as $message) {
            $message->setMessage($bbcodeFiler->filter($message->getMessage()));
        }
        $this->em->flush();
    }
}
