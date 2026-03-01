import type { Todo } from "../types";

interface Props {
  todo: Todo;
  onComplete: (id: number) => void;
  onDelete: (id: number) => void;
}

export function TodoItem({ todo, onComplete, onDelete }: Props) {
  return (
    <li className={`todo-item ${todo.completed ? "completed" : ""}`}>
      <button
        className="toggle"
        onClick={() => onComplete(todo.id)}
        aria-label={todo.completed ? "Mark incomplete" : "Mark complete"}
      >
        {todo.completed ? "✅" : "⬜"}
      </button>
      <span className="title">{todo.title}</span>
      <button
        className="delete"
        onClick={() => onDelete(todo.id)}
        aria-label={`Delete "${todo.title}"`}
      >
        🗑️
      </button>
    </li>
  );
}
