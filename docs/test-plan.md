# Todo App — Test Plan

## 1. Overview

This document describes the test strategy for the Todo App, a full-stack application
built with a Symfony (PHP 8.2+) backend using hexagonal architecture and a React
(TypeScript) frontend. The backend exposes a REST JSON API consumed by the React SPA.

**Application under test:** Todo list CRUD — create, list, complete (toggle), and delete todos.

## 2. Scope

### In Scope
- Domain logic (entities, value objects)
- Application use cases (CreateTodo, CompleteTodo, ListTodos, DeleteTodo)
- REST API endpoints (4 routes)
- React UI components (AddTodo, TodoItem, App/TodoList)
- End-to-end user flows

### Out of Scope
- Database migration testing
- Infrastructure provisioning / CI pipeline
- Performance / load testing
- Security penetration testing

## 3. Test Strategy

| Layer          | Tool            | Type        | Location                          |
|----------------|-----------------|-------------|-----------------------------------|
| Domain / UseCases | PHPUnit 10+  | Unit        | `backend/tests/Unit/`             |
| API Endpoints  | Symfony WebTestCase | Integration | `backend/tests/Integration/`  |
| React Components | Vitest + Testing Library | Component | `frontend/tests/`        |
| Full Stack     | Playwright      | E2E         | `e2e/tests/`                      |

### Test Pyramid
```
        /  E2E  \          ← 1 smoke test (Playwright)
       /----------\
      / Component  \       ← 2 test files (Vitest)
     /--------------\
    /  Integration   \     ← 1 test file, ~10 cases (Symfony)
   /------------------\
  /    Unit Tests      \   ← 4 test files, ~15 cases (PHPUnit)
 /______________________\
```

## 4. Test Cases

### 4.1 Unit Tests — Use Cases

#### 4.1.1 CreateTodoUseCase

| # | Test Case | Input | Expected Outcome |
|---|-----------|-------|------------------|
| U-1 | Create with valid title | `"Buy groceries"` | Todo created with title, `completed=false`, id assigned |
| U-2 | Create with empty title | `""` | Throws `InvalidArgumentException` |
| U-3 | Create with whitespace-only title | `"   "` | Throws `InvalidArgumentException` |
| U-4 | Title is trimmed | `"  Buy milk  "` | Todo created with title `"Buy milk"` |

#### 4.1.2 CompleteTodoUseCase

| # | Test Case | Input | Expected Outcome |
|---|-----------|-------|------------------|
| U-5 | Complete existing incomplete todo | Valid todo ID | `completed` toggled to `true` |
| U-6 | Complete non-existent todo | Non-existent ID | Throws `TodoNotFoundException` |
| U-7 | Toggle already-completed todo | Already-completed todo ID | `completed` toggled back to `false` |

#### 4.1.3 ListTodosUseCase

| # | Test Case | Input | Expected Outcome |
|---|-----------|-------|------------------|
| U-8  | List when no todos exist | — | Returns empty array |
| U-9  | List multiple todos | — | Returns all todos |
| U-10 | Todos ordered by creation (newest first) | — | Most recent todo first |

#### 4.1.4 DeleteTodoUseCase

| # | Test Case | Input | Expected Outcome |
|---|-----------|-------|------------------|
| U-11 | Delete existing todo | Valid todo ID | Todo removed from repository |
| U-12 | Delete non-existent todo | Non-existent ID | Throws `TodoNotFoundException` |

### 4.2 Integration Tests — API Endpoints

#### GET /api/todos

| # | Test Case | Expected |
|---|-----------|----------|
| I-1 | List todos (empty) | `200 OK`, empty JSON array `[]` |
| I-2 | List todos (with data) | `200 OK`, JSON array with todo objects |

#### POST /api/todos

| # | Test Case | Body | Expected |
|---|-----------|------|----------|
| I-3 | Create with valid title | `{"title":"Buy groceries"}` | `201 Created`, JSON todo with id |
| I-4 | Create with empty title | `{"title":""}` | `400 Bad Request`, error message |
| I-5 | Create with missing title | `{}` | `400 Bad Request`, error message |

#### PATCH /api/todos/{id}/complete

| # | Test Case | Expected |
|---|-----------|----------|
| I-6 | Complete existing todo | `200 OK`, JSON todo with `completed: true` |
| I-7 | Complete non-existent todo | `404 Not Found` |
| I-8 | Toggle already-completed todo | `200 OK`, JSON todo with `completed: false` |

#### DELETE /api/todos/{id}

| # | Test Case | Expected |
|---|-----------|----------|
| I-9  | Delete existing todo | `204 No Content` |
| I-10 | Delete non-existent todo | `404 Not Found` |
| I-11 | Confirm deleted todo is gone | `GET /api/todos` does not include deleted item |

### 4.3 Component Tests — React Frontend

#### TodoList (App component)

| # | Test Case | Expected |
|---|-----------|----------|
| C-1 | Renders empty state | Shows "No todos yet" message |
| C-2 | Renders loading state | Shows "Loading..." message |
| C-3 | Renders list of todos | Each todo title visible |
| C-4 | Completing a todo calls API | `onComplete` / API called with correct ID |
| C-5 | Deleting a todo removes it | Todo removed from DOM after delete |
| C-6 | Displays error on fetch failure | Shows error message |

#### AddTodo

| # | Test Case | Expected |
|---|-----------|----------|
| C-7 | Adds todo with valid title | Calls `onAdd` with trimmed title, clears input |
| C-8 | Blocks submit with empty title | Submit button disabled, `onAdd` not called |
| C-9 | Blocks submit with whitespace title | Submit button disabled |
| C-10 | Disables input while submitting | Input and button disabled during submission |

### 4.4 E2E Tests — Playwright

| # | Test Case | Steps | Expected |
|---|-----------|-------|----------|
| E-1 | Full CRUD flow | 1. Load app<br>2. Add todo "E2E Test Todo"<br>3. Verify it appears<br>4. Click complete<br>5. Verify completed style<br>6. Click delete<br>7. Verify removed | App reflects each state change |
| E-2 | Empty state | Load app with no todos | "No todos yet" message visible |

## 5. Edge Cases

| Category | Scenario | Expected Behavior |
|----------|----------|-------------------|
| Empty title | User submits empty string | 400 error / validation blocks submit |
| Whitespace title | User submits `"   "` | 400 error / validation blocks submit |
| Non-existent ID | Complete/delete with invalid ID | 404 Not Found |
| Already completed | Toggle completed todo | Reverts to incomplete |
| Empty list | No todos in database | Returns `[]`, UI shows empty state |
| Concurrent delete | Delete already-deleted todo | 404 Not Found (idempotent-safe) |
| Long title | Title with 500+ characters | Accepted (no max length enforced at API level) |
| Special characters | Title with `<script>`, emojis, unicode | Stored and returned correctly |

## 6. Acceptance Criteria

1. All unit tests pass with mocked repositories (no database required)
2. Integration tests pass against a test database (SQLite or PostgreSQL)
3. Frontend component tests pass with mocked API calls
4. E2E smoke test passes against running frontend + backend
5. Test coverage targets:
   - Unit tests: 100% of use case logic
   - Integration tests: all 4 endpoints, both happy and error paths
   - Component tests: all user-facing components
6. No test relies on external services or network access (except E2E)
7. Tests are deterministic and can run in CI without flakiness

## 7. Test Execution

### Running Tests

```bash
# Backend unit + integration tests
cd backend && php bin/phpunit

# Frontend component tests
cd frontend && npx vitest run

# E2E tests (requires running app)
cd e2e && npx playwright test
```

### CI Integration

Tests should run in this order in CI:
1. Backend unit tests (fastest, no dependencies)
2. Frontend component tests (fast, no dependencies)
3. Backend integration tests (needs test database)
4. E2E tests (needs full stack running)
