<?php

namespace PF\Console\Commands\Read;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use PF\Backend\Requestor;

class Board extends Command
{
    protected function configure()
    {
        $this->setName('read:board')
             ->setDescription('Показывает посты с выбранной доски')
             ->addArgument('board', InputArgument::REQUIRED, 'Имя доски');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $board = $input->getArgument('board');
        $io = new SymfonyStyle($input, $output);

        $requestor = new Requestor();

        try {
            $result = $requestor(Requestor::FETCH_BOARD, $board)->getResponse();
        }catch (\OutOfBoundsException $e) {
            $io->caution('На доске ещё нет постов');

            return Command::FAILURE;
        } catch (\Throwable $e) {
            $io->error('Ошибка чтения доски ' . $e->getMessage());

            return Command::FAILURE;
        }

        $io->title('Доска: ' . $board);

        if (!empty($result['board_data']['threads'])) {
            $io->table(['#', 'Имя', 'Тема', 'Сообщение'], array_map(function ($thread) {
                return [$thread['id'], $thread['poster'], $thread['subject'], substr($thread['message'], 0, 30)];
            }, $result['board_data']['threads']));
        }
        
        return Command::SUCCESS;
    }
}
