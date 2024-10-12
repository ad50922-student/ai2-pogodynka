<?php
namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function findOneByCityAndCountry(string $city, ?string $country): ?Location
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.city = :city')
            ->setParameter('city', $city);

        if ($country) {
            $qb->andWhere('l.country = :country')
               ->setParameter('country', $country);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}
