<?php

namespace WT\Library\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use WT\Library\Service\BookService;

class LibraryController extends AbstractController {

  public function __construct(
    private BookService $bs,
    private LoggerInterface $log) {
  }

  #[Route('/', name: 'home')]
  public function home(): Response {

    $books = [];
    try {
      $books = $this->bs->findBooks();
    } catch (\Exception $ex) {
      $this->log->error("ERROR: $ex");
    }
    return $this->render('home.html.twig', ['books' => $books]);
  }

  #[Route('/admin', name: 'admin')]
  public function books(): Response {

    return $this->render('admin.html.twig');
  }

  #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
  public function login(Request $request): Response {

    return $this->render('login.html.twig');
  }

  #[Route('/logout', name: 'app_logout')]
  public function logout(): void {

  }

}
