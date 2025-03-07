<?php

namespace WT\Library\Entity;

use Doctrine\ORM\Mapping as ORM;
use WT\Library\Repository\BookRepository;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: "book")]
class BookEntity {

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: "integer")]
  public $id;

  #[ORM\Column(type: "string", length: 200)]
  public $title;

  #[ORM\Column(type: "string", length: 13)]
  public $ISBN;

  #[ORM\Column(type: "date")]
  public $publicationDate;

  #[ORM\Column(type: "string", length: 50)]
  public $genre;

  #[ORM\Column(type: "integer")]
  public $copies;

}
