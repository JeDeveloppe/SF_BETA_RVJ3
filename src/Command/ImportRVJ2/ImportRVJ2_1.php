<?php

namespace App\Command\ImportRVJ2;

use App\Service\ImportRvj2\ImportBoitesService;
use App\Service\ImportRvj2\ImportPiecesService;
use App\Service\ImportRvj2\ImportOccasionsService;
use App\Service\ImportRvj2\ImportPartenairesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-RVJ2-1')]

class ImportRVJ2_1 extends Command
{
    public function __construct(
        private ImportBoitesService $importBoitesService,
        private ImportPiecesService $importPiecesService,
        private ImportOccasionsService $importOccasionsService,
        private ImportPartenairesService $importPartenairesService,
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input,$output);

        // //on importe les boites
        //$this->importBoitesService->importBoites($io);

        // //on importe le detail des boites
        // $this->importPiecesService->importPieces($io);

        // //on importe les jeux complet (occasions)
        $this->importOccasionsService->importOccasions($io);

        //on importe les partenaires
        // $this->importPartenairesService->importPartenaires($io);

        return Command::SUCCESS;
    }
}