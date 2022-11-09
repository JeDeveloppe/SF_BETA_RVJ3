<?php

namespace App\Command;


use App\Service\ImportClientsService;
use App\Service\ImportAdressesService;
use App\Service\ImportDepartementsService;
use App\Service\ImportVillesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-RVJ2-2')]

class ImportRVJ2_2 extends Command
{
    public function __construct(
        private ImportClientsService $importClientsService,
        private ImportAdressesService $importAdressesService,
        private ImportDepartementsService $importDepartementsService,
        private ImportVillesService $importVillesService
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input,$output);

        //on importe les clients
        $this->importClientsService->importClients($io);

        //on importe les departementss
        $this->importDepartementsService->importDepartements($io);

        //on importe les villes
        $this->importVillesService->importVilles($io);

        //on importe les adresses (facturation et livraison)
        $this->importAdressesService->importAdresses($io);

        return Command::SUCCESS;
    }
}