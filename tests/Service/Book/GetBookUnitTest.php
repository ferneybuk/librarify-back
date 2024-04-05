<?php

namespace App\Tests\Service\Book;

use App\Entity\Book;
use App\Entity\User;
use App\Model\Exception\Book\BookNotFound;
use App\Repository\BookRepository;
use App\Service\Book\GetBook;
use App\Service\Utils\Security;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class GetBookUnitTest extends TestCase
{
    public function testBookNotFound(): void
    {
        $bookRepository = $this->createMock(BookRepository::class);
        $bookRepository->expects(self::once())
            ->method('find')
            ->with('b08184cb-6112-4897-92df-9fc53df1a3db')
            ->willReturn(null);

        $security = $this->createMock(Security::class);

        $getBook = new GetBook($bookRepository, $security);
        $this->expectException(BookNotFound::class);
        $getBook->__invoke('b08184cb-6112-4897-92df-9fc53df1a3db');
    }

    public function TestUserNotEqual(): void
    {
        $firstUser = $this->createMock(User::class);
        $secondUser = $this->createMock(User::class);

        $book = $this->createMock(Book::class);
        $book->expects(self::once())
            ->method('getUser')
            ->willReturn($firstUser);

        $bookRepository = $this->createMock(BookRepository::class);
        $bookRepository->expects(self::once())
            ->method('find')
            ->with('b08184cb-6112-4897-92df-9fc53df1a3db')
            ->willReturn($book);

        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('getCurrentUser')
            ->willReturn($secondUser);

        $getBook = new GetBook($bookRepository, $security);
        $this->expectException(AccessDeniedException::class);
        $getBook->__invoke('b08184cb-6112-4897-92df-9fc53df1a3db');
    }

    public function testSuccesGetBook(): void
    {
        $user = $this->createMock(User::class);

        $book = $this->createMock(Book::class);
        $book->expects(self::once())
            ->method('getUser')
            ->willReturn($user);

        $bookRepository = $this->createMock(BookRepository::class);
        $bookRepository->expects(self::once())
            ->method('find')
            ->with('b08184cb-6112-4897-92df-9fc53df1a3db')
            ->willReturn($book);

        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('getCurrentUser')
            ->willReturn($user);

        $getBook = new GetBook($bookRepository, $security);

        $returnedBook = $getBook->__invoke('b08184cb-6112-4897-92df-9fc53df1a3db');
        $this->assertsame($book, $returnedBook);
    }
}