<?php

namespace App\Service\Book;

use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;

class UpdateComment
{
    public function __construct(
        private readonly GetBook $getBook,
        private readonly BookRepository $bookRepository,
        private readonly CommentRepository $commentRepository,
    ) {
    }

    public function __invoke(string $id, Request $request): void
    {
        $content = json_decode($request->getContent(), true);
        $book = ($this->getBook)($id);
        $comment = $this->commentRepository->find($content['id']);
        $book->removeComment($comment);
        $comment->setContent($content['content']);
        $book->addComment($comment);
        $this->bookRepository->save($book);
    }
}