<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase;

use App\Application\UseCase\CompleteTodo;
use App\Domain\Entity\Todo;
use App\Domain\Port\TodoRepositoryPort;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CompleteTodoTest extends TestCase
{
    public function testTogglesToComplete(): void
    {
        $todo = new Todo('Test');
        $id = $todo->getId();

        $repo = $this->createMock(TodoRepositoryPort::class);
        $repo->method('findById')->with($id)->willReturn($todo);
        $repo->expects($this->once())->method('save');

        $useCase = new CompleteTodo($repo);
        $result = $useCase->execute($id);

        $this->assertNotNull($result);
        $this->assertTrue($result->isCompleted());
    }

    public function testToggleBackToIncomplete(): void
    {
        $todo = new Todo('Test');
        $todo->toggleComplete(); // now completed
        $id = $todo->getId();

        $repo = $this->createMock(TodoRepositoryPort::class);
        $repo->method('findById')->with($id)->willReturn($todo);
        $repo->expects($this->once())->method('save');

        $useCase = new CompleteTodo($repo);
        $result = $useCase->execute($id);

        $this->assertNotNull($result);
        $this->assertFalse($result->isCompleted());
    }

    public function testReturnsNullForMissingTodo(): void
    {
        $repo = $this->createMock(TodoRepositoryPort::class);
        $repo->method('findById')->willReturn(null);

        $useCase = new CompleteTodo($repo);
        $result = $useCase->execute(Uuid::v7());

        $this->assertNull($result);
    }
}
