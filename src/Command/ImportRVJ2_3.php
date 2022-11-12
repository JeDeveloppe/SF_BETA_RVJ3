<?php

namespace App\Command;


use App\Service\ImportClientsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-RVJ2-3')]

class ImportRVJ2_3 extends Command
{
    public function __construct(
        private ImportClientsService $importClientsService,
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input,$output);

        //on importe les documents
        $this->importDocumentsService->importDocuments($io);


        return Command::SUCCESS;
    }
}