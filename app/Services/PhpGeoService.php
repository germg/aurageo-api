<?php

namespace App\Services;

use Location\Coordinate;
use Location\Distance\Vincenty;
use App\Repositories\PlacesRepository as PlacesRepository;

class PhpGeoService
{
    private $placesRepository;
    private $vicentyCalculator;

    public function __construct()
    {
        $this->placesRepository = new PlacesRepository();
        $this->vicentyCalculator = new Vincenty();
    }

    /**
     * Obtiene la distancia en metros entre dos puntos definidos por latitud y longitud
     *
     * @param $latitud1
     * @param $longitud1
     * @param $latitud2
     * @param $longitud2
     * @return float
     */
    public function getDistanceBetweenTwoPoints($latitud1, $longitud1, $latitud2, $longitud2)
    {
        $coordinate1 = new Coordinate($latitud1, $longitud1);
        $coordinate2 = new Coordinate($latitud2, $longitud2);
        return $this->vicentyCalculator->getDistance($coordinate1, $coordinate2);
    }
}