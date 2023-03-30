<?php

namespace PF\Console\Commands\Read;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use PF\Backend\Requestor;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

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

        if (!empty($result['board_data']['threads'])) {
            $table = new Table($output);
            $table->setHeaders(['#', 'Автор', 'Тема', 'Сообщение']);

            $rows = array_map(function ($thread) {
                return [
                    $thread['id'],
                    $this->formatText($thread['poster']),
                    !empty($thread['subject']) ? $this->formatText($thread['subject']) : $this->formatText($thread['truncated_message']),
                    $this->formatText($thread['truncated_message'])
                ];
            }, $result['board_data']['threads']);

            $table->setRows($rows);
            $table->setStyle('box-double');

            $table->render();
        }
        
        return Command::SUCCESS;
    }

    private function formatText(string $text): string
    {
        if (mb_strlen($text) > 50) {
            $text = mb_substr($text, 0, 50);
        }

        $text = trim($text);
        $text = rtrim($text);
        $text = preg_replace('~[\r\n\t]+~', '', $text);

        return $text;
    }
}
