<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\UseCase\CompleteTodoUseCase;
use App\Domain\Entity\Todo;
use App\Domain\Exception\TodoNotFoundException;
use App\Domain\Port\TodoRepositoryPort;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CompleteTodoUseCaseTest extends TestCase
{
    private TodoRepositoryPort&MockObject $repository;
    private CompleteTodoUseCase $useCase;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TodoRepositoryPort::class);
        $this->useCase = new CompleteTodoUseCase($this->repository);
    }

    #[Test]
    public function it_completes_an_existing_incomplete_todo(): void
    {
        $todo = new Todo('Buy groceries');
        $this->assertFalse($todo->isCompleted());

        $this->repository
            ->method('findById')
            ->with(1)
            ->willReturn($todo);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (Todo $t): bool => $t->isCompleted() === true));

        $result = $this->useCase->execute(1);

        $this->assertTrue($result->isCompleted());
    }

    #[Test]
    public function it_throws_when_todo_not_found(): void
    {
        $this->repository
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        $this->repository->expects($this->never())->method('save');

        $this->expectException(TodoNotFoundException::class);

        $this->useCase->execute(999);
    }

    #[Test]
    public function it_toggles_already_completed_todo_back_to_incomplete(): void
    {
        $todo = new Todo('Buy groceries');
        $todo->toggleComplete(); // now completed
        $this->assertTrue($todo->isCompleted());

        $this->repository
            ->method('findById')
            ->with(1)
            ->willReturn($todo);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (Todo $t): bool => $t->isCompleted() === false));

        $result = $this->useCase->execute(1);

        $this->assertFalse($result->isCompleted());
    }
}
