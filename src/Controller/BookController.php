<?php

namespace WT\Library\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use WT\Library\Service\BookService;
use WT\Library\Entity\BookEntity;
use WT\Library\Lib\RequestException;

#[Route('/api/book')]
class BookController extends AbstractController {

  public function __construct(
    private BookService $bs,
    private LoggerInterface $log) {
  }

  #[Route('/genre')]
  public function getBookGenres(Request $request): JsonResponse {

    $res = null;
    $status = JsonResponse::HTTP_OK;
    try {
      $genres = $this->bs->findBookGenres();
      $res = new LibraryResponse($genres);

    } catch (\Exception $ex) {
      $this->log->error("ERROR: $ex");
      $res = new LibraryResponse(null, "Error getting book genres: " . $ex->getMessage());
      $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
    }
    return new JsonResponse($res, $status);
  }

  #[Route('/', methods: ['GET'])]
  public function getBooks(): JsonResponse {

    $res = null;
    $status = JsonResponse::HTTP_OK;
    try {
      $books = $this->bs->findBooks();
      $res = new LibraryResponse($books);

    } catch (\Exception $ex) {
      $this->log->error("ERROR: $ex"); //TODO fix logging
      $res = new LibraryResponse(null, "Error getting books: " . $ex->getMessage());
      $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
    }
    return new JsonResponse($res, $status);
  }

  #[Route('/{id}', methods: ['GET'])]
  public function getBook(Request $request): JsonResponse {

    $res = null;
    $status = JsonResponse::HTTP_OK;
    try {
      $id = (int) $request->get('id'); //TODO validate value
      $book = $this->bs->findBook($id);
      $res = new LibraryResponse($book);

    } catch (RequestException $ex) {
      $res = new LibraryResponse(null, "Bad request: " . $ex->getMessage());
      $status = JsonResponse::HTTP_BAD_REQUEST;
    } catch (\Exception $ex) {
      $this->log->error("ERROR: $ex");
      $res = new LibraryResponse(null, "Error getting book: " . $ex->getMessage());
      $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
    }
    return new JsonResponse($res, $status);
  }

  #[Route('/', methods: ['POST'])]
  public function postBook(Request $request): JsonResponse {

    $res = null;
    $json = null;
    $status = JsonResponse::HTTP_OK;
    try {
      $json = $request->getContent();
      $data = json_decode($json, true);
      $book = $this->validateBook($data);
      $book = $this->bs->saveBook($book);
      $res = new LibraryResponse($book, "Book created successfully");

    } catch (RequestException $ex) {
      $res = new LibraryResponse(null, "Bad request: " . $ex->getMessage());
      $status = JsonResponse::HTTP_BAD_REQUEST;
    } catch (\Exception $ex) {
      $this->log->error("ERROR: $ex");  //TODO fix logging
      $res = new LibraryResponse(null, "Error creating book: " . $ex->getMessage());
      $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
    }
    return new JsonResponse($res, $status);
  }

  #[Route('/{id}', methods: ['PUT'])]
  public function putBook(Request $request): JsonResponse {

    $res = null;
    $json = null;
    $status = JsonResponse::HTTP_OK;
    try {
      $id = (int) $request->get('id');
      $json = $request->getContent();
      $data = json_decode($json, true);
      $book = $this->validateBook($data);
      $book = $this->bs->saveBook($book, $id);
      $res = new LibraryResponse($book, "Book updated successfully");

    } catch (RequestException $ex) {
      $res = new LibraryResponse(null, "Bad request: " . $ex->getMessage());
      $status = JsonResponse::HTTP_BAD_REQUEST;
    } catch (\Exception $ex) {
      $this->log->error("ERROR: $ex");
      $res = new LibraryResponse(null, "Error updating book " . $ex->getMessage());
      $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
    }
    return new JsonResponse($res, JsonResponse::HTTP_OK);
  }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteBook(Request $request): JsonResponse {

      $res = null;
      $status = JsonResponse::HTTP_OK;
      try {
        $id = (int) $request->get('id');
        $this->bs->removeBook($id);
        $res = new LibraryResponse(null, "Book removed successfully");

      } catch (RequestException $ex) {
        $res = new LibraryResponse(null, "Bad request: " . $ex->getMessage());
        $status = JsonResponse::HTTP_BAD_REQUEST;
      } catch (\Exception $ex) {
        $this->log->error("ERROR: $ex");
        $res = new LibraryResponse(null, "Error deleting book " . $ex->getMessage());
        $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
      }
      return new JsonResponse($res, $status);
    }

  //TOOD move to validation to util class
  //TODO Return list of errors instead of throwing exceptions
  private function validateBook(array $data): BookEntity {

    $book = new BookEntity();
    // Validate title field
    if (isset($data['title'])) {
      $pTitle = $data['title'];
      if (!empty($pTitle)) {
        //TODO validate title length
        $book->title = $pTitle;
      } else {
        throw new RequestException("Field 'title' not valid");
      }
    } else {
      throw new RequestException("Field 'title' not found");
    }
    // Validate author field
    if (isset($data['author'])) {
      $pAuthor = $data['author'];
      if (!empty($pAuthor)) {
        //TODO validate author length
        $book->author = $pAuthor;
      } else {
        throw new RequestException("Field 'author' not valid");
      }
    } else {
      throw new RequestException("Field 'author' not found");
    }
    // Validate ISBN field
    if (isset($data['ISBN'])) {
      $pISBN = $data['ISBN'];
      if (!empty($pISBN)) {
        //TODO validate ISBN length
        $book->ISBN = $pISBN;
      } else {
        throw new RequestException("Field 'ISBN' not valid");
      }
    } else {
      throw new RequestException("Field 'ISBN' not found");
    }
    // Validate publication date field
    if (isset($data['publicationDate'])) { //TODO Move date validation method to Util class
      $pPublicationDate = $data['publicationDate'];
      if (!empty($pPublicationDate)) {
        $publicationDate = \DateTime::createFromFormat('Y-m-d', $data['publicationDate']);
        if ($publicationDate === false) {
          throw new RequestException("Bad format for 'publicationDate'");
        }
        $book->publicationDate = $publicationDate;
      } else {
        throw new RequestException("Field 'publicationDate' not valid");
      }
    } else {
      throw new RequestException("Field 'publicationDate' not found");
    }
    // Validate genre field
    if (isset($data['genre'])) {
      $pGenre = $data['genre'];
      if (!empty($pGenre)) {
        //TODO validate genre length
        $book->genre = $pGenre;
      } else {
        throw new RequestException("Field 'genre' not valid");
      }
    } else {
      throw new RequestException("Field 'genre' not found");
    }
    // Validate copies value field
    if (isset($data['copies'])) {
      $pCopies = $data['copies'];
      if (is_int($pCopies)) {
        $book->copies = $data['copies'];
      } else {
        throw new RequestException("Field 'copies' not a number");
      }
    } else {
      throw new RequestException("Field 'copies' not found");
    }
    return $book;
  }

}
