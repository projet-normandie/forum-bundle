<?php

namespace ProjetNormandie\ForumBundle\Command;

use ProjetNormandie\CommonBundle\Command\DefaultCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ProjetNormandie\ForumBundle\Filter\Bbcode as BbcodeFilter;

class MessageCommand extends DefaultCommand
{
    protected function configure()
    {
        $this
            ->setName('pj-forum:message')
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
                $this->migrate($output);
                break;
        }
        $this->end($output);

        return true;
    }


    /**
     * @param OutputInterface $output
     */
    private function migrate(OutputInterface $output)
    {
        /** @var \ProjetNormandie\ForumBundle\Repository\MessageRepository $messageRepository */
        $messageRepository = $this->getContainer()->get('doctrine')->getRepository('ProjetNormandieForumBundle:Message');

        $bbcodeFiler = new BbcodeFilter();
        $message = $messageRepository->find(1);


        var_dump($bbcodeFiler->filter($message->getMessage()));

        $output->writeln(sprintf('%d chart(s) updated', $message->getId()));
    }
}
