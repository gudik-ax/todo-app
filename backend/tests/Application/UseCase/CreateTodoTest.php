<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase;

use App\Application\UseCase\CreateTodo;
use App\Domain\Entity\Todo;
use App\Domain\Port\TodoRepositoryPort;
use PHPUnit\Framework\TestCase;

final class CreateTodoTest extends TestCase
{
    public function testCreatesTodoAndSaves(): void
    {
        $repo = $this->createMock(TodoRepositoryPort::class);
        $repo->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Todo::class));

        $useCase = new CreateTodo($repo);
        $todo = $useCase->execute('Write tests');

        $this->assertSame('Write tests', $todo->getTitle());
        $this->assertFalse($todo->isCompleted());
    }
}
