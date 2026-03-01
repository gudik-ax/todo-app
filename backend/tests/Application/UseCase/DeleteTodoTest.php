<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase;

use App\Application\UseCase\DeleteTodo;
use App\Domain\Entity\Todo;
use App\Domain\Port\TodoRepositoryPort;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class DeleteTodoTest extends TestCase
{
    public function testDeletesExistingTodo(): void
    {
        $todo = new Todo('Test');
        $id = $todo->getId();

        $repo = $this->createMock(TodoRepositoryPort::class);
        $repo->method('findById')->with($id)->willReturn($todo);
        $repo->expects($this->once())->method('delete')->with($todo);

        $useCase = new DeleteTodo($repo);
        $this->assertTrue($useCase->execute($id));
    }

    public function testReturnsFalseForMissingTodo(): void
    {
        $repo = $this->createMock(TodoRepositoryPort::class);
        $repo->method('findById')->willReturn(null);

        $useCase = new DeleteTodo($repo);
        $this->assertFalse($useCase->execute(Uuid::v7()));
    }
}
