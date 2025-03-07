<?php

namespace WT\Library\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WT\Library\BookEntity;

class BookRepository extends ServiceEntityRepository {

  #[\Override]
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, BookEntity::class);
  }

}
