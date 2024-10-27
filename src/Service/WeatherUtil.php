<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Location;
use App\Entity\Measurement;
use App\Repository\MeasurementRepository;
use App\Repository\LocationRepository;

class WeatherUtil
{
    private MeasurementRepository $measurementRepository;
    private LocationRepository $locationrepository;

    public function __construct(MeasurementRepository $measurementRepository, LocationRepository $locationrepository)
    {
        $this->measurementRepository = $measurementRepository;
        $this->locationrepository = $locationrepository;
    }

    public function getWeatherForLocation(Location $location): array
    {
        return $this->measurementRepository->findByLocation($location);
    }

    public function getWeatherForCountryAndCity(string $countryCode, string $city): array
    {
        $location = $this->locationrepository->findOneByCityAndCountry($countryCode, $city);

        if (!$location) {
            return []; // Zwraca pustą tablicę, jeśli lokalizacja nie została znaleziona
        }

        return $this->measurementRepository->findByLocation($location); // Zwraca tablicę pomiarów
    }

}