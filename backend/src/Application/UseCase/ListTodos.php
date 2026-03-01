<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Todo;
use App\Domain\Port\TodoRepositoryPort;

final readonly class ListTodos
{
    public function __construct(
        private TodoRepositoryPort $repository,
    ) {}

    /** @return Todo[] */
    public function execute(): array
    {
        return $this->repository->findAll();
    }
}
