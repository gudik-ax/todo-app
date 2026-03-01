<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Port\TodoRepositoryPort;
use Symfony\Component\Uid\Uuid;

final readonly class DeleteTodo
{
    public function __construct(
        private TodoRepositoryPort $repository,
    ) {}

    public function execute(Uuid $id): bool
    {
        $todo = $this->repository->findById($id);

        if ($todo === null) {
            return false;
        }

        $this->repository->delete($todo);

        return true;
    }
}
