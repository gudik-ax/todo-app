<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Todo;
use App\Domain\Port\TodoRepositoryPort;
use Symfony\Component\Uid\Uuid;

final readonly class CompleteTodo
{
    public function __construct(
        private TodoRepositoryPort $repository,
    ) {}

    public function execute(Uuid $id): ?Todo
    {
        $todo = $this->repository->findById($id);

        if ($todo === null) {
            return null;
        }

        $todo->toggleComplete();
        $this->repository->save($todo);

        return $todo;
    }
}
