<?php

namespace App\Command\ImportRVJ2;

use App\Service\CreationAdministrateurAdresseService;
use App\Service\ImportRvj2\ImportVillesService;
use App\Service\ImportRvj2\ImportClientsService;
use App\Service\ImportRvj2\ImportAdressesService;
use App\Service\ImportRvj2\ImportDepartementsService;
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
        private ImportVillesService $importVillesService,
        private CreationAdministrateurAdresseService $creationAdministrateurAdresseService
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input,$output);

        //on importe les clients
        //$this->importClientsService->importClients($io);

        //on importe les departementss
        // $this->importDepartementsService->importDepartements($io);

        //on importe les villes
        // $this->importVillesService->importVilles($io);

        //on importe les adresses (facturation et livraison)
        // $this->importAdressesService->importAdresses($io);

        //on crée user administrateur et adresse de retrait
        // $this->CreationAdministrateurAdresseService->creationAdminAdresse($io);

        return Command::SUCCESS;
    }
}