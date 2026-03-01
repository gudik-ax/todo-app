import type { Todo } from "./types";

const BASE_URL = import.meta.env.VITE_API_URL ?? "http://localhost:8000";

async function request<T>(path: string, options?: RequestInit): Promise<T> {
  const res = await fetch(`${BASE_URL}${path}`, {
    headers: { "Content-Type": "application/json" },
    ...options,
  });
  if (!res.ok) throw new Error(`API error: ${res.status}`);
  if (res.status === 204) return undefined as T;
  return res.json();
}

export const api = {
  getTodos: () => request<Todo[]>("/api/todos"),
  createTodo: (title: string) =>
    request<Todo>("/api/todos", {
      method: "POST",
      body: JSON.stringify({ title }),
    }),
  completeTodo: (id: number) =>
    request<Todo>(`/api/todos/${id}/complete`, { method: "PATCH" }),
  deleteTodo: (id: number) =>
    request<void>(`/api/todos/${id}`, { method: "DELETE" }),
};
