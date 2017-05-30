<?php

namespace App\Services;

use Location\Coordinate;
use Location\Distance\Vincenty;

class PhpGeoService
{
    // En esta clase se agregarán los cálculos de distancia.

    public function getDistance(){
        $coordinate1 = new Coordinate(-34.6704099,-58.5651175); // UNLaM
        $coordinate2 = new Coordinate(-34.6729647,-58.5617647); // Av Peron y Av Illia
        $calculator = new Vincenty();
        return $calculator->getDistance($coordinate1, $coordinate2);
    }
}