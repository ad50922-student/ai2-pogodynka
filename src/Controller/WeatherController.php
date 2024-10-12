<?php
namespace App\Controller;

use App\Repository\MeasurementRepository;
use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route('/weather/{city}/{country?}', name:'app_weather')]
    public function city(string $city, ?string $country, LocationRepository $locationRepository, MeasurementRepository $measurementRepository): Response
    {
        // Szukamy lokalizacji na podstawie miasta (i opcjonalnie kraju)
        $location = $locationRepository->findOneByCityAndCountry($city, $country);

        if (!$location) {
            throw $this->createNotFoundException('Location not found.');
        }

        // Pobieramy pomiary dla znalezionej lokalizacji
        $measurements = $measurementRepository->findByLocation($location);

        // Renderujemy widok i przekazujemy dane
        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}
