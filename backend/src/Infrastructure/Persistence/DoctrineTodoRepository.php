<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Todo;
use App\Domain\Port\TodoRepositoryPort;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class DoctrineTodoRepository implements TodoRepositoryPort
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function save(Todo $todo): void
    {
        $this->em->persist($todo);
        $this->em->flush();
    }

    public function findById(Uuid $id): ?Todo
    {
        return $this->em->find(Todo::class, $id);
    }

    /** @return Todo[] */
    public function findAll(): array
    {
        return $this->em->getRepository(Todo::class)->findBy([], ['createdAt' => 'DESC']);
    }

    public function delete(Todo $todo): void
    {
        $this->em->remove($todo);
        $this->em->flush();
    }
}
