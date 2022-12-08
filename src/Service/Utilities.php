<?php

namespace App\Service;

use DateTimeImmutable;


class Utilities
{
    public function __construct(

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
}