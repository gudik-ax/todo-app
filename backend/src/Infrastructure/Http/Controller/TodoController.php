<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\UseCase\CompleteTodo;
use App\Application\UseCase\CreateTodo;
use App\Application\UseCase\DeleteTodo;
use App\Application\UseCase\ListTodos;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/todos')]
final readonly class TodoController
{
    public function __construct(
        private CreateTodo $createTodo,
        private CompleteTodo $completeTodo,
        private ListTodos $listTodos,
        private DeleteTodo $deleteTodo,
    ) {}

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $todos = $this->listTodos->execute();

        return new JsonResponse(
            array_map(fn($todo) => $todo->toArray(), $todos),
        );
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $title = $data['title'] ?? null;

        if (!is_string($title) || trim($title) === '') {
            return new JsonResponse(['error' => 'Title is required'], Response::HTTP_BAD_REQUEST);
        }

        $todo = $this->createTodo->execute(trim($title));

        return new JsonResponse($todo->toArray(), Response::HTTP_CREATED);
    }

    #[Route('/{id}/complete', methods: ['PATCH'])]
    public function complete(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return new JsonResponse(['error' => 'Invalid ID'], Response::HTTP_BAD_REQUEST);
        }

        $todo = $this->completeTodo->execute(Uuid::fromString($id));

        if ($todo === null) {
            return new JsonResponse(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($todo->toArray());
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return new JsonResponse(['error' => 'Invalid ID'], Response::HTTP_BAD_REQUEST);
        }

        $deleted = $this->deleteTodo->execute(Uuid::fromString($id));

        if (!$deleted) {
            return new JsonResponse(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
