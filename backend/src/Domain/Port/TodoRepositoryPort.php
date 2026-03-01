<?php

declare(strict_types=1);

namespace App\Domain\Port;

use App\Domain\Entity\Todo;
use Symfony\Component\Uid\Uuid;

interface TodoRepositoryPort
{
    public function save(Todo $todo): void;

    public function findById(Uuid $id): ?Todo;

    /** @return Todo[] */
    public function findAll(): array;

    public function delete(Todo $todo): void;
}
