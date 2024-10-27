<?php

namespace App\Command;

use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:country',
    description: 'Displays a measurement for selected city in a selected country',
)]
class WeatherCountryCityCommand extends Command
{
    private WeatherUtil $weatherUtil;

    public function __construct(WeatherUtil $weatherUtil)
    {
        $this->weatherUtil = $weatherUtil;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('countryCode', InputArgument::REQUIRED, 'Code of selected country')
            ->addArgument('city', InputArgument::REQUIRED, 'Name of selected city');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $countryCode = $input->getArgument('countryCode');
        $city = $input->getArgument('city');
        $io->text([
            'Country Code: ' . $countryCode,
            'City: ' . $city,
        ]);
        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($countryCode, $city);
        if (empty($measurements)) {
            $io->warning('No measurements for selected location.');
        } else {
            foreach ($measurements as $measurement) {
                $measurementDate = $measurement->getDate();
                $io->section(sprintf('Forecast for %s in %s on %s:', $city, $countryCode, $measurement->getDate()->format('Y-m-d')));
                $io->text([
                    'Temperature: ' . $measurement->getCelsius() . ' C',
                ]);
            }
        }

        return Command::SUCCESS;
    }
}