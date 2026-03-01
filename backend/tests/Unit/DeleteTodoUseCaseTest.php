<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\UseCase\DeleteTodoUseCase;
use App\Domain\Entity\Todo;
use App\Domain\Exception\TodoNotFoundException;
use App\Domain\Port\TodoRepositoryPort;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteTodoUseCaseTest extends TestCase
{
    private TodoRepositoryPort&MockObject $repository;
    private DeleteTodoUseCase $useCase;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TodoRepositoryPort::class);
        $this->useCase = new DeleteTodoUseCase($this->repository);
    }

    #[Test]
    public function it_deletes_an_existing_todo(): void
    {
        $todo = new Todo('Buy groceries');

        $this->repository
            ->method('findById')
            ->with(1)
            ->willReturn($todo);

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with($todo);

        $this->useCase->execute(1);
    }

    #[Test]
    public function it_throws_when_todo_not_found(): void
    {
        $this->repository
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        $this->repository->expects($this->never())->method('delete');

        $this->expectException(TodoNotFoundException::class);

        $this->useCase->execute(999);
    }
}
