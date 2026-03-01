# Todo App

A todo list application built with hexagonal architecture.

## Stack

- **Backend:** Symfony (PHP) — Hexagonal Architecture
- **Frontend:** React SPA (Vite + TypeScript)
- **Database:** PostgreSQL
- **API:** REST JSON

## Architecture

```
backend/
├── src/
│   ├── Domain/           # Entities, repository ports, value objects
│   ├── Application/      # Use cases (CreateTodo, CompleteTodo, ListTodos, DeleteTodo)
│   └── Infrastructure/   # Doctrine repos, Symfony controllers, config
frontend/
├── src/                  # React + TypeScript SPA
```

## Setup

### Backend
```bash
cd backend
composer install
# Configure DATABASE_URL in .env.local
php bin/console doctrine:migrations:migrate
symfony server:start
```

### Frontend
```bash
cd frontend
npm install
npm run dev
```
