<?php
// src/Repository/SubscriptionRepository.php
namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 *
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    // Méthode customisée trouver les souscriptions par date de début
    public function findByBeginDate(\DateTime $beginDate): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.beginDate = :beginDate')
            ->setParameter('beginDate', $beginDate)
            ->getQuery()
            ->getResult();
    }

    // Méthode customisée pour trouver les souscriptions actives
    public function findActiveSubscriptions(): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('s')
            ->andWhere('s.beginDate <= :now')
            ->andWhere('s.endDate >= :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }
}
