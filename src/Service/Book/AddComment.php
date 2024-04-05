<?php

namespace App\Service\Book;

use App\Entity\Comment;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use App\Service\Utils\Security;
use Symfony\Component\HttpFoundation\Request;

class AddComment
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly CommentRepository $commentRepository,
        private Security $security
    ) {
    }

    public function __invoke(string $id, Request $request): void
    {
        $book = $this->bookRepository->find($id);
        $comment = Comment::create(
            $request->request->get('_content'),
            $this->security->getCurrentUser(),
            $book
        );
        $book->addComment($comment);
//        TODO: Add this in a transaction
        $this->commentRepository->save($comment);
        $this->bookRepository->save($book);
    }
}