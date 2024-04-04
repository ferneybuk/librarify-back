<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(
        BookRepository $bookRepository,
    ): Response
    {

        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
            'books' =>  $bookRepository->findAll(),
        ]);
    }
}
