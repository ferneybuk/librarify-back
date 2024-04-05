<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Service\Book\AddComment;
use App\Service\Book\UpdateComment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\{Delete, Get, Post, Put};

class BookController extends AbstractController
{
    public function __construct(
        private BookRepository $bookRepository
    )
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
    public function details(string $id): Response
    {
        $book = $this->bookRepository->find($id);

        return $this->render('book/details.html.twig', [
            'book' => $book,
        ]);
    }

    #[Post('/book/{id}/comment', name: 'add_comment')]
    public function addComment(
        string $id,
        Request $request,
        Addcomment $addcomment,
    ): Response
    {
        $book = $this->bookRepository->find($id);
        $addcomment->__invoke($id,$request);
        return $this->render('book/details.html.twig', [
            'book' => $book,
        ]);
    }

    #[Put('/book/{id}/comment', name: 'update_comment')]
    public function update_comment(
        string $id,
        Request $request,
        UpdateComment $updateComment,
    ): Response
    {
        $book = $this->bookRepository->find($id);
        $updateComment->__invoke($id,$request);
        return $this->render('book/details.html.twig', [
            'book' => $book,
        ]);
    }
}
