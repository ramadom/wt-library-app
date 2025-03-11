<?php

namespace WT\Library\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WT\Library\Repository\BookRepository;
use WT\Library\Entity\BookEntity;
use WT\Library\Lib\RequestException;

class BookService {

  public function __construct(
    private BookRepository $br,
    private EntityManagerInterface $em,
    private LoggerInterface $log) {
  }

  public function findBookGenres(): array { //TODO add Genre entity to manage book genres

    $genres = [
      ['id' => "adventure", 'name' => "Adventure"],
      ['id' => "computers", 'name' => "Computer science"],
      ['id' => "classic", 'name' => "Classic"],
      ['id' => "scifi", 'name' => "Science fiction"]
    ];

    return $genres;
  }

  public function findBooks(): array {

    $books = null;
    try {
      $books = $this->br->findAll(); //TODO add pagination

    } catch (RequestException $ex) {
      throw $ex;
    } catch (\Exception $ex) {
      throw new \Exception("Error finding book", 0, $ex);
    }
    return $books;
  }

  public function findBook(int $id): ?BookEntity {

    $book = null;
    try {
      $book = $this->checkBookExists($id);

    } catch (RequestException $ex) {
      throw $ex;
    } catch (\Exception $ex) {
      throw new \Exception("Error finding book", 0, $ex);
    }
    return $book;
  }

  public function saveBook(BookEntity $book, ?int $id = null): BookEntity {

    try {
      $book->id = $id;
      $this->checkISBNAlreadyUsed($book->ISBN, $book); //TODO split into two validation methods for new and updated book

      if ($id === null) { // Persist new book

        //TODO check if ISBN already used
        $this->em->persist($book);

      } else { // Update existing book

        $updatedBook = $this->checkBookExists($id);
        //TODO check if ISBN is used on another book
        $updatedBook->update($book);

      }
      $this->em->flush();

    } catch (RequestException $ex) {
      throw $ex;
    } catch (\Exception $ex) {
      throw new \Exception("Error saving book", 0, $ex);
    }
    return $book;
  }

  public function removeBook(int $id): int {

    try {
      $book = $this->checkBookExists($id);
      $this->em->remove($book);
      $this->em->flush();
    } catch (RequestException $ex) {
      throw $ex;
    } catch (\Exception $ex) {
      throw new \Exception("Error deleting book", 0, $ex);
    }
    return $id;

  }

  private function checkBookExists(int $id): BookEntity {

    $book = $this->br->find($id);
    if ($book == null) {
      throw new RequestException("Book $id not found");
    }
    return $book;
  }

  /**
   * Check for duplicate ISBN to gracefully handle UNIQUE error
   *
   */
  private function checkISBNAlreadyUsed(string $ISBN, BookEntity $book) {
    $existingBook = $this->br->findBookByISBN($ISBN);

    if ($book->id === null) { // New book, but ISBN already used
        if ($existingBook !== null) {
            throw new RequestException("Book with ISBN number $ISBN already exists");
        }
    } else { // Updated book, but ISBN already used in another book
        if ($existingBook !== null && $existingBook->id !== $book->id) {
            throw new RequestException("Book with ISBN number $ISBN already exists");
        }
    }
  }

}
