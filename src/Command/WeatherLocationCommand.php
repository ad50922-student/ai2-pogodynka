<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:location',
    description: 'Displays a measurement for selected location',
)]
class WeatherLocationCommand extends Command
{
    private WeatherUtil $weatherUtil;
    private LocationRepository $locationRepository;
    public function __construct(WeatherUtil $weatherUtil, LocationRepository $locationRepository)
    {
        $this->weatherUtil = $weatherUtil;
        $this->locationRepository = $locationRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('locationid', InputArgument::REQUIRED, 'ID of selected location')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $locationid = $input->getArgument('locationid');
        $location = $this->locationRepository->find($locationid);

        $measurements = $this->weatherUtil->getWeatherForLocation($location);

        if (empty($measurements)) {
            $io->warning('No measurements for selected location.');
        } else {
            foreach ($measurements as $measurement) {
                $measurementDate = $measurement->getDate();
                $io->section(sprintf('Forecast for %s on %s:', $location->getLocation(), $measurement->getDate()->format('Y-m-d')));
                $io->text([
                    'Temperature: ' . $measurement->getCelsius() . ' C',
                ]);
            }
        }

        return Command::SUCCESS;
    }
}