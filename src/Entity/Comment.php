<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Comment
{
    private DateTimeInterface $createdAt;

    public function __construct(
        private UuidInterface $id,
        private string $content,
        private User $user,
        private ?Book $book = null,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public static function create(
        string $content,
        User $user,
        ?Book $book,
    ): self {
        $comment = new self(
            Uuid::uuid4(),
            $content,
            $user,
            $book
        );
        return $comment;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;
        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function __toString()
    {
        return $this->content ?? 'Comment';
    }
}
