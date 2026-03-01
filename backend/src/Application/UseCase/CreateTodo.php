<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Todo;
use App\Domain\Port\TodoRepositoryPort;

final readonly class CreateTodo
{
    public function __construct(
        private TodoRepositoryPort $repository,
    ) {}

    public function execute(string $title): Todo
    {
        $todo = new Todo($title);
        $this->repository->save($todo);

        return $todo;
    }
}
