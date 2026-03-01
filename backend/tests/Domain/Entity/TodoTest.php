<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Todo;
use PHPUnit\Framework\TestCase;

final class TodoTest extends TestCase
{
    public function testCreateTodo(): void
    {
        $todo = new Todo('Buy milk');

        $this->assertSame('Buy milk', $todo->getTitle());
        $this->assertFalse($todo->isCompleted());
        $this->assertNotNull($todo->getId());
        $this->assertNotNull($todo->getCreatedAt());
    }

    public function testCompleteTodo(): void
    {
        $todo = new Todo('Buy milk');
        $todo->complete();

        $this->assertTrue($todo->isCompleted());
    }

    public function testToArray(): void
    {
        $todo = new Todo('Buy milk');
        $array = $todo->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertSame('Buy milk', $array['title']);
        $this->assertFalse($array['completed']);
        $this->assertArrayHasKey('createdAt', $array);
    }
}
