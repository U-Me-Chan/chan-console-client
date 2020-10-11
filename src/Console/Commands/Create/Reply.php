<?php

namespace PF\Console\Commands\Create;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use PF\Backend\Requestor;

class Reply extends Command
{
    protected function configure()
    {
        $this->setName('create:reply')
             ->setDescription('Создаёт новый ответ')
             ->addArgument('board', InputArgument::REQUIRED, 'Имя доски')
             ->addArgument('parent_id', InputArgument::REQUIRED, 'Идентификатор родительского поста')
             ->addArgument('message', InputArgument::REQUIRED, 'Сообщение');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $board     = $input->getArgument('board');
        $parent_id = $input->getArgument('parent_id');
        $message   = $input->getArgument('message');

        $io = new SymfonyStyle($input, $output);

        $requestor = new Requestor();

        try {
            $result = $requestor(Requestor::CREATE_POST, ['form_params' => [
                'board_name' => $board,
                'message'    => $message,
                'parent_id'  => $parent_id
            ]]);
        } catch (\Throwable $e) {
            $io->error('Ошибка создания ответа: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Ответ #' . $result['payload']['id'] . ' на тред #' . $parent_id . ' был отправлен');

        return Command::SUCCESS;

    }
}
