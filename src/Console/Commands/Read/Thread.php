<?php

namespace PF\Console\Commands\Read;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use PF\Backend\Requestor;

class Thread extends Command
{
    protected function configure()
    {
        $this->setName('read:thread')
             ->setDescription('Показывает посты и его ответы')
             ->addArgument('id', InputArgument::REQUIRED, 'Идентификатор поста');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        $io = new SymfonyStyle($input, $output);

        $requestor = new Requestor();

        try {
            $result = $requestor(Requestor::FETCH_THREAD, $id)->getResponse();
        } catch (\Throwable $e) {
            $io->error('Ошибка чтения треда: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $replies = $result['thread_data']['replies'];
        $parent_post = $result['thread_data'];

        $posts = array_merge([$parent_post], $replies);

        foreach ($posts as $post) {
            $io->section('Пост #' . $post['id']);
            $io->text('Имя: ' . $post['poster']);
            $io->text('Тема: ' . $post['subject']);
            $io->text('Сообщение: ' . $post['message']);
        }

        return Command::SUCCESS;
    }
}
