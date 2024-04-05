<?php

namespace App\Service\Comment;

use App\Entity\Comment;
use App\Model\Exception\Comment\CommentNotFound;
use App\Repository\CommentRepository;
use Ramsey\Uuid\Uuid;
class GetComment
{
    public function __construct(
        private CommentRepository $commentRepository,
    )
    {
    }

    public function __invoke(string $id): ?Comment
    {
        $category = $this->commentRepository->find(Uuid::fromString($id));
        if (!$category) {
            CommentNotFound::throwException();
        }
        return $category;
    }
}