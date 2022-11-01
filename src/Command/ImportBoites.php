<?php

namespace App\Command;


use App\Service\ImportBoitesService;
use App\Service\ImportPiecesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-boites')]

class ImportBoites extends Command
{
    public function __construct(
        private ImportBoitesService $importBoitesService,
        private ImportPiecesService $importPiecesService
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input,$output);
        $this->importBoitesService->importBoites($io);
        $this->importPiecesService->importPieces($io);

        return Command::SUCCESS;
    }
}