<?php

namespace App\Form\Model;

use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\User;
use Ramsey\Uuid\UuidInterface;
class CommentDto
{
    public function __construct(
        public ?UuidInterface $id = null,
        public ?string $content = null,
        public ?User $user = null,
        public ?Book $book = null,
    ) {
    }

    public static function createFromComment(Comment $comment): self
    {
        $dto = new self(
            $comment->getId(),
            $comment->getContent(),
            $comment->getUser(),
            $comment->getBook()
        );
        return $dto;
    }
}