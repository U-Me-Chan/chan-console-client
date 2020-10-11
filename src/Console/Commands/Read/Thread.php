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
            $result = $requestor(Requestor::FETCH_THREAD, ['id' => $id])->getResponse();
        } catch (\Throwable $e) {
            $io->error('Ошибка чтения треда: ' . $e->getMessage());

            return Command::FAILURE;
        }
        
        $posts = array_merge([reset($result['thread_data'])], $result['thread_data']['replies']);

        foreach ($posts as $post) {
            $io->section('Пост #' . $post['id']);
            $io->text('Имя: ' . $post['poster']);
            $io->text('Сообщение: ' . $post['message']);
        }

        return Command::SUCCESS;
    }
}
