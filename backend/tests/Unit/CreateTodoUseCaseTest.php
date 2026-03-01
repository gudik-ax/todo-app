<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\UseCase\CreateTodoUseCase;
use App\Domain\Entity\Todo;
use App\Domain\Repository\TodoRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateTodoUseCaseTest extends TestCase
{
    private TodoRepositoryInterface&MockObject $repository;
    private CreateTodoUseCase $useCase;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TodoRepositoryInterface::class);
        $this->useCase = new CreateTodoUseCase($this->repository);
    }

    #[Test]
    public function it_creates_a_todo_with_valid_title(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Todo $todo): bool {
                return $todo->getTitle() === 'Buy groceries'
                    && $todo->isCompleted() === false;
            }));

        $todo = $this->useCase->execute('Buy groceries');

        $this->assertSame('Buy groceries', $todo->getTitle());
        $this->assertFalse($todo->isCompleted());
        $this->assertNotNull($todo->getCreatedAt());
    }

    #[Test]
    public function it_trims_the_title(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Todo $todo): bool {
                return $todo->getTitle() === 'Buy milk';
            }));

        $todo = $this->useCase->execute('  Buy milk  ');

        $this->assertSame('Buy milk', $todo->getTitle());
    }

    #[Test]
    public function it_rejects_empty_title(): void
    {
        $this->repository->expects($this->never())->method('save');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('title');

        $this->useCase->execute('');
    }

    #[Test]
    public function it_rejects_whitespace_only_title(): void
    {
        $this->repository->expects($this->never())->method('save');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('title');

        $this->useCase->execute('   ');
    }
}
