<?php

namespace App\Controller;

use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use App\Entity\Measurement;

class WeatherApiController extends AbstractController
{
    private WeatherUtil $weatherUtil;

    public function __construct(WeatherUtil $weatherUtil)
    {
        $this->weatherUtil = $weatherUtil;
    }

    #[Route('/api/v1/weather', name: 'app_weather_api')]
    public function index(
        #[MapQueryParameter] string $city = null, 
        #[MapQueryParameter] string $country = null,
        #[MapQueryParameter('format')] string $format = 'json', 
        #[MapQueryParameter('twig')] bool $twig = false,
    ): JsonResponse|Response {
        if (!$city || !$country) {
            return $this->json(['error' => 'Missing city or country parameter'], 400);
        }

        // Pobieranie prognozy pogody za pomocą WeatherUtil
        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($country, $city);

        // W przypadku, gdy nie znaleziono pomiarów
        if (empty($measurements)) {
            return $this->json(['error' => 'No measurements found'], 404);
        }

        // Formatowanie danych wynikowych
        $data = [
            'city' => $city,
            'country' => $country,
            'measurements' => array_map(fn(Measurement $m) => [
                'date' => $m->getDate()->format('Y-m-d'),
                'celsius' => $m->getCelsius(),
                'fahrenheit' => $m->getFahrenheit(), // Dodanie wartości w Fahrenheitach
            ], $measurements)
        ];

        // Obsługa formatu odpowiedzi
        if ($format === 'json') {
            return $this->json($data);
        } elseif ($format === 'csv') {
            if ($twig) {
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            } else {
                // Generowanie CSV bez użycia TWIG
                $csvData = [];
                $csvData[] = ['city', 'country', 'date', 'celsius', 'fahrenheit']; // Nagłówki CSV

                foreach ($measurements as $measurement) {
                    $csvData[] = [
                        $city,
                        $country,
                        $measurement->getDate()->format('Y-m-d'),
                        $measurement->getCelsius(),
                        $measurement->getFahrenheit(), // Dodanie wartości w Fahrenheitach
                    ];
                }

                $csvContent = '';
                foreach ($csvData as $row) {
                    $csvContent .= implode(',', $row) . "\n";
                }

                $response = new Response($csvContent);
                $response->headers->set('Content-Type', 'text/plain');
                $response->headers->set('Content-Disposition', 'inline; filename="weather.csv"');

                return $response;
            }
        }

        // Domyślnie zwracaj JSON
        return $this->json($data);
    }
}
