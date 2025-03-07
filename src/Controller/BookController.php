<?php

namespace WT\Library\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/book')]
class BookController extends AbstractController {

  #[Route('/hi')]
  public function hi(Request $request) {

    return new Response("Hello");
  }
}
