import { useEffect, useState } from "react";
import type { Todo } from "./types";
import { api } from "./api";
import { TodoItem } from "./components/TodoItem";
import { AddTodo } from "./components/AddTodo";
import "./App.css";

export default function App() {
  const [todos, setTodos] = useState<Todo[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchTodos = async () => {
    try {
      setError(null);
      const data = await api.getTodos();
      setTodos(data);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Failed to load todos");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchTodos();
  }, []);

  const handleAdd = async (title: string) => {
    const todo = await api.createTodo(title);
    setTodos((prev) => [todo, ...prev]);
  };

  const handleComplete = async (id: number) => {
    const updated = await api.completeTodo(id);
    setTodos((prev) => prev.map((t) => (t.id === id ? updated : t)));
  };

  const handleDelete = async (id: number) => {
    await api.deleteTodo(id);
    setTodos((prev) => prev.filter((t) => t.id !== id));
  };

  return (
    <div className="app">
      <h1>📝 Todos</h1>
      <AddTodo onAdd={handleAdd} />
      {loading && <p className="status">Loading...</p>}
      {error && <p className="status error">{error}</p>}
      {!loading && !error && todos.length === 0 && (
        <p className="status">No todos yet. Add one above!</p>
      )}
      <ul className="todo-list">
        {todos.map((todo) => (
          <TodoItem
            key={todo.id}
            todo={todo}
            onComplete={handleComplete}
            onDelete={handleDelete}
          />
        ))}
      </ul>
    </div>
  );
}
