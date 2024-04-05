<?php

namespace App\Service\Book;

use App\Entity\Comment;
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
        $book = $this->bookRepository->find($id);
        $comment = $this->commentRepository->find(
            $request->request->get('_comment_id')
        );
        $book->removeComment($comment);
        $comment->setContent(
            $request->request->get('_comment_content')
        );
        $book->addComment($comment);
        //        TODO: Add this in a transaction
        $this->commentRepository->save($comment);
        $this->bookRepository->save($book);
    }
}