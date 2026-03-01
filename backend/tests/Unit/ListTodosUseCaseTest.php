<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\UseCase\ListTodosUseCase;
use App\Domain\Entity\Todo;
use App\Domain\Port\TodoRepositoryPort;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ListTodosUseCaseTest extends TestCase
{
    private TodoRepositoryPort&MockObject $repository;
    private ListTodosUseCase $useCase;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TodoRepositoryPort::class);
        $this->useCase = new ListTodosUseCase($this->repository);
    }

    #[Test]
    public function it_returns_empty_array_when_no_todos_exist(): void
    {
        $this->repository
            ->method('findAll')
            ->willReturn([]);

        $result = $this->useCase->execute();

        $this->assertSame([], $result);
    }

    #[Test]
    public function it_returns_all_todos(): void
    {
        $todo1 = new Todo('Buy groceries');
        $todo2 = new Todo('Walk the dog');
        $todo3 = new Todo('Read a book');

        $this->repository
            ->method('findAll')
            ->willReturn([$todo1, $todo2, $todo3]);

        $result = $this->useCase->execute();

        $this->assertCount(3, $result);
        $this->assertSame('Buy groceries', $result[0]->getTitle());
        $this->assertSame('Walk the dog', $result[1]->getTitle());
        $this->assertSame('Read a book', $result[2]->getTitle());
    }

    #[Test]
    public function it_returns_todos_ordered_by_creation_newest_first(): void
    {
        $older = new Todo('Older todo');
        $newer = new Todo('Newer todo');

        // Repository is expected to return newest first
        $this->repository
            ->method('findAll')
            ->willReturn([$newer, $older]);

        $result = $this->useCase->execute();

        $this->assertCount(2, $result);
        $this->assertSame('Newer todo', $result[0]->getTitle());
        $this->assertSame('Older todo', $result[1]->getTitle());
    }
}
