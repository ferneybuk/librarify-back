<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Service\Book\GetBook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\{Delete, Get, Post, Put};

class BookController extends AbstractController
{
    public function __construct(private BookRepository $bookRepository)
    {

    }

    #[Get('/book', name: 'app_book')]
    public function index(): Response
    {

        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
            'books' =>  $this->bookRepository->findAll(),
        ]);
    }

    #[Get('/book/{id}', name: 'detail_book')]
    public function details(string $id, GetBook $getBook): Response
    {
        $book = $getBook->__invoke($id);

        return $this->render('book/details.html.twig', [
            'book' => $book,
        ]);
    }

}
