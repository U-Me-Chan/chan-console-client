<?php

namespace PF\Console\Commands\Create;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use PF\Backend\Requestor;

class Post extends Command
{
    protected function configure()
    {
        $this->setName('create:post')
             ->setDescription('Создаёт новый пост')
             ->addArgument('board', InputArgument::REQUIRED, 'Имя доски')
             ->addArgument('subject', InputArgument::REQUIRED, 'Тема')
             ->addArgument('message', InputArgument::REQUIRED, 'Сообщение');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $board   = $input->getArgument('board');
        $subject = $input->getArgument('subject');
        $message = $input->getArgument('message');

        $io = new SymfonyStyle($input, $output);

        $requestor = new Requestor();

        try {
            $result = $requestor(Requestor::CREATE_POST, [
                'tag' => $board,
                'subject'    => $subject,
                'message'    => $message
            ])->getResponse();
        } catch (\Throwable $e) {
            $io->error('Ошибка создания поста: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $io->note('Тред #' . $result['post_id'] . ' был создан');

        return Command::SUCCESS;
    }
}
