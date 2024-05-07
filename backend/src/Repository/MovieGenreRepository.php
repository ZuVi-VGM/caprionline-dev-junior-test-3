<?php

namespace App\Repository;

use App\Entity\MovieGenre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MovieGenre>
 *
 * @method MovieGenre|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovieGenre|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovieGenre[]    findAll()
 * @method MovieGenre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieGenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MovieGenre::class);
    }

    //    /**
    //     * @return MovieGenre[] Returns an array of MovieGenre objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MovieGenre
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // Consente di ottenere la lista di film associati ad un determinato genere,
    // ordinando i risultati sulla base dei parametri $orderBy ed $order

    public function findByGenreIdJoinedToMovie(int $genreId, array $orderBy = ['c.id' => 'ASC']): ?array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select('p, c')
            ->from('App\Entity\MovieGenre', 'p')
            ->innerJoin('p.movie', 'c')
            ->where('p.genre = :id')
            ->setParameter('id', $genreId);

        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $query->getQuery()->getResult();
    }
}
