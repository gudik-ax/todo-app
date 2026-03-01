<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Attributes\Test;

final class TodoApiTest extends WebTestCase
{
    #[Test]
    public function list_todos_returns_empty_array(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/todos');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    #[Test]
    public function create_todo_with_valid_title(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/todos', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Buy groceries']));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertSame('Buy groceries', $data['title']);
        $this->assertFalse($data['completed']);
        $this->assertArrayHasKey('createdAt', $data);
    }

    #[Test]
    public function create_todo_with_empty_title_returns_400(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/todos', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => '']));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function create_todo_with_missing_title_returns_400(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/todos', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function list_todos_returns_created_todos(): void
    {
        $client = static::createClient();

        // Create two todos
        $client->request('POST', '/api/todos', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'First todo']));
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $client->request('POST', '/api/todos', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Second todo']));
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // List all
        $client->request('GET', '/api/todos');
        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(2, $data);

        $titles = array_column($data, 'title');
        $this->assertContains('First todo', $titles);
        $this->assertContains('Second todo', $titles);
    }

    #[Test]
    public function complete_existing_todo(): void
    {
        $client = static::createClient();

        // Create a todo
        $client->request('POST', '/api/todos', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Complete me']));

        $created = json_decode($client->getResponse()->getContent(), true);
        $id = $created['id'];

        // Complete it
        $client->request('PATCH', "/api/todos/{$id}/complete");

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['completed']);
    }

    #[Test]
    public function complete_non_existent_todo_returns_404(): void
    {
        $client = static::createClient();
        $client->request('PATCH', '/api/todos/99999/complete');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function toggle_already_completed_todo(): void
    {
        $client = static::createClient();

        // Create and complete
        $client->request('POST', '/api/todos', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Toggle me']));
        $created = json_decode($client->getResponse()->getContent(), true);
        $id = $created['id'];

        $client->request('PATCH', "/api/todos/{$id}/complete");
        $firstToggle = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($firstToggle['completed']);

        // Toggle back
        $client->request('PATCH', "/api/todos/{$id}/complete");
        $secondToggle = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($secondToggle['completed']);
    }

    #[Test]
    public function delete_existing_todo(): void
    {
        $client = static::createClient();

        // Create a todo
        $client->request('POST', '/api/todos', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Delete me']));
        $created = json_decode($client->getResponse()->getContent(), true);
        $id = $created['id'];

        // Delete it
        $client->request('DELETE', "/api/todos/{$id}");
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        // Verify it's gone
        $client->request('GET', '/api/todos');
        $data = json_decode($client->getResponse()->getContent(), true);
        $ids = array_column($data, 'id');
        $this->assertNotContains($id, $ids);
    }

    #[Test]
    public function delete_non_existent_todo_returns_404(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/todos/99999');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
