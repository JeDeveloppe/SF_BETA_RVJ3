<?php

namespace App\Service;

use App\Repository\ConfigurationRepository;
use App\Repository\InformationsLegalesRepository;
use DateTimeImmutable;

class Utilities
{
    public function __construct(
        private ConfigurationRepository $configurationRepository,
        private InformationsLegalesRepository $informationsLegalesRepository
        ){
    }

    public function getDateTimeImmutableFromTimestamp($timestamp)
    {
        $tps = (int) $timestamp;
        $date = new DateTimeImmutable();

        if($timestamp !== null){
            return $date->setTimestamp($tps);
        }else{
            return null;
        }
    }

    public function importConfigurationAndInformationsLegales(): Array
    {
        $infosAndConfig = [];

        $infosAndConfig['legales'] = $this->informationsLegalesRepository->findOneBy([]);
        $infosAndConfig['config'] = $this->configurationRepository->findOneBy([]);

        return $infosAndConfig;
    }
}