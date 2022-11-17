<?php

namespace App\Command\ImportRVJ2;

use App\Service\ImportRvj2\ImportDocumentsLignesService;
use App\Service\ImportRvj2\ImportDocumentsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-RVJ2-3')]

class ImportRVJ2_3 extends Command
{
    public function __construct(
        private ImportDocumentsService $importDocumentsService,
        private ImportDocumentsLignesService $importDocumentsLignesService
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input,$output);

        //on importe les documents
        $this->importDocumentsService->importDocuments($io);

        //on importe les ligne boite des documents
        $this->importDocumentsLignesService->importDocumentsLigneBoites($io);

        //on importe les lignes occasion des documents
        $this->importDocumentsLignesService->importDocumentsLigneOccasion($io);


        return Command::SUCCESS;
    }
    
}