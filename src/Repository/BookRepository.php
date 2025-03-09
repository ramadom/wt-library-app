<?php

namespace WT\Library\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WT\Library\Entity\BookEntity;
use Psr\Log\LoggerInterface;

class BookRepository extends ServiceEntityRepository {

  #[\Override]
  public function __construct(ManagerRegistry $registry,
    private LoggerInterface $log) {
    parent::__construct($registry, BookEntity::class);
  }

  public function findBookByISBN(string $ISBN): ?BookEntity {

    $book = null;
    try {
        $query = $this->createQueryBuilder('b')
            ->andWhere('b.ISBN = :isbn')
            ->setParameter('isbn', $ISBN)
            ->setMaxResults(1) // Limit to single result in case of dublicates
            ->getQuery();
        $book = $query->getOneOrNullResult();

    } catch (\Exception $ex) {
        throw $ex;
    }
    return $book;
  }

}
