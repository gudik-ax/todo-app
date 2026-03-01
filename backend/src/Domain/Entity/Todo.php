<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\Uid\Uuid;

final class Todo
{
    private Uuid $id;
    private string $title;
    private bool $completed;
    private \DateTimeImmutable $createdAt;

    public function __construct(string $title)
    {
        $this->id = Uuid::v7();
        $this->title = $title;
        $this->completed = false;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function complete(): void
    {
        $this->completed = true;
    }

    public function toArray(): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'completed' => $this->completed,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
