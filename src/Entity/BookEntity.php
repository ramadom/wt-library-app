<?php

namespace WT\Library\Entity;

use Doctrine\ORM\Mapping as ORM;
use WT\Library\Repository\BookRepository;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: "book")]
class BookEntity implements \JsonSerializable {

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: "integer")]
  public $id;

  #[ORM\Column(type: "string", length: 200)]
  public $title;

  #[ORM\Column(type: "string", name: "isbn", length: 13)]  //TODO add unique constraint
  public $ISBN;

  #[ORM\Column(type: "date")]
  public $publicationDate;

  #[ORM\Column(type: "string", length: 50)]
  public $genre;

  #[ORM\Column(type: "integer")]
  public $copies;

  public function update(BookEntity $source) {
    $this->title = $source->title;
    $this->ISBN = $source->ISBN;
    $this->publicationDate = $source->publicationDate;
    $this->genre = $source->genre;
    $this->copies = $source->copies;
  }

  #[\Override]
  public function jsonSerialize(): mixed { //TODO use DTOs for API data and JSON serialization
    $json = [];
    $json['id'] = $this->id;
    $json['title'] = $this->title;
    $json['ISBN'] = $this->ISBN;
    $json['publicationDate'] = $this->publicationDate ? $this->publicationDate->format('Y-m-d') : '';
    $json['genre'] = $this->genre;
    $json['copies'] = $this->copies;
    return $json;
  }

  public function __toString(): string {
    return "BookEntity[id=" . $this->id
      . ", title=" . $this->title
      . ", ISBN=" . $this->ISBN
      . ", genre=" . $this->genre
      . ", copies=" . $this->copies
      . "]";
  }
}
