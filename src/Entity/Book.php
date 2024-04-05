<?php

namespace App\Entity;

use App\Entity\Book\Score;
use App\Event\Book\BookCreatedEvent;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DomainException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Contracts\EventDispatcher\Event;

class Book
{
    private array $domainEvents = [];
    private DateTimeInterface $createdAt;
    private ?Collection $comments;

    /**
     * @param Collection|Author[]|null $authors
     * @param Collection|Category[] |null $categories
     */
    public function __construct(
        private UuidInterface $id,
        private string $title,
        private User $user,
        private ?string $image = null,
        private ?string $description = null,
        private Score $score = new Score(),
        private ?DateTimeInterface $readAt = null,
        private ?Collection $authors = new ArrayCollection(),
        private ?Collection $categories = new ArrayCollection(),
    ) {
        $this->createdAt = new DateTimeImmutable();
        $this->comments = new ArrayCollection();
    }

    /**
     * @param array|Author[] $authors
     * @param array|Category[] $categories
     * @return self
     */
    public static function create(
        string $title,
        User $user,
        ?string $image,
        ?string $description,
        ?Score $score,
        ?DateTimeInterface $readAt,
        array $authors,
        array $categories
    ): self {
        $book = new self(
            Uuid::uuid4(),
            $title,
            $user,
            $image,
            $description,
            $score ?? new Score(),
            $readAt,
            new ArrayCollection($authors),
            new ArrayCollection($categories)
        );
        $book->addDomainEvent(new BookCreatedEvent($book->getId()));
        return $book;
    }

    public function addDomainEvent(Event $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function pullDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array
    {
        return array_values($this->categories->toArray());
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function updateCategories(Category ...$newCategories)
    {
        /** @var ArrayCollection<Category> */
        $originalCategories = new ArrayCollection();
        foreach ($this->categories as $category) {
            $originalCategories->add($category);
        }

        // Remove categories
        foreach ($originalCategories as $originalCategory) {
            if (!\in_array($originalCategory, $newCategories, true)) {
                $this->removeCategory($originalCategory);
            }
        }

        // Add categories
        foreach ($newCategories as $newCategory) {
            if (!$originalCategories->contains($newCategory)) {
                $this->addCategory($newCategory);
            }
        }
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    /**
     * @return Category[]
     */
    public function getComments(): array
    {
        return array_values($this->comments->toArray());
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
        }

        return $this;
    }

    public function updateComments(Comment ...$newComments)
    {
        /** @var ArrayCollection<Comment> */
        $originalComments = new ArrayCollection();
        foreach ($this->comments as $comment) {
            $originalComments->add($comment);
        }

        // Remove comments
        foreach ($originalComments as $originalComment) {
            if (!\in_array($originalComment, $newComments, true)) {
                $this->removeCategory($originalComment);
            }
        }

        // Add comments
        foreach ($newComments as $newComment) {
            if (!$originalComments->contains($newComment)) {
                $this->addCategory($newComment);
            }
        }
    }

    public function removeAuthor(Author $author): self
    {
        if ($this->authors->contains($author)) {
            $this->authors->removeElement($author);
        }

        return $this;
    }

    public function updateAuthors(Author ...$authors)
    {
        /** @var Author[]|ArrayCollection */
        $originalAuthors = new ArrayCollection();
        foreach ($this->authors as $author) {
            $originalAuthors->add($author);
        }

        // Remove authors
        foreach ($originalAuthors as $originalAuthor) {
            if (!\in_array($originalAuthor, $authors)) {
                $this->removeAuthor($originalAuthor);
            }
        }

        // Add authors
        foreach ($authors as $newAuthor) {
            if (!$originalAuthors->contains($newAuthor)) {
                $this->addAuthor($newAuthor);
            }
        }
    }

    /**
     * @param array|Author[] $authors
     * @param array|Category[] $categories
     * @return void
     */
    public function update(
        string $title,
        ?string $image,
        ?string $description,
        ?Score $score,
        ?DateTimeInterface $readAt,
        array $authors,
        array $categories
    ) {
        $this->title = $title;
        if ($image !== null) {
            $this->image = $image;
        }
        $this->description = $description;
        $this->score = $score;
        $this->readAt = $readAt;
        $this->updateCategories(...$categories);
        $this->updateAuthors(...$authors);
    }

    public function patch(array $data): self
    {
        if (\array_key_exists('score', $data)) {
            $this->score = Score::create($data['score']);
        }
        if (\array_key_exists('title', $data)) {
            $title = $data['title'];
            if ($title === null) {
                throw new DomainException('Title cannot be null');
            }
            $this->title = $title;
        }
        return $this;
    }

    public function setScore(Score $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getScore(): Score
    {
        return $this->score;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public  function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function isRead(): ?bool
    {
        return $this->readAt === null ? false : true;
    }

    public function markAsRead(DateTimeInterface $readAt): self
    {
        $this->readAt = $readAt;
        return $this;
    }

    public function setReadAt(DateTimeInterface $readAt): self
    {
        $this->readAt = $readAt;
        return $this;
    }

    public function getReadAt(): ?DateTimeInterface
    {
        return $this->readAt;
    }

    public function getReadAtAsString(): ?string
    {
        if ($this->readAt === null) {
            return null;
        }
        return $this->readAt->format('Y-m-d');
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function __toString()
    {
        return $this->title ?? 'Libro';
    }
}
