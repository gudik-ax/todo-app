import { useState } from "react";

interface Props {
  onAdd: (title: string) => Promise<void>;
}

export function AddTodo({ onAdd }: Props) {
  const [title, setTitle] = useState("");
  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    const trimmed = title.trim();
    if (!trimmed) return;
    setSubmitting(true);
    try {
      await onAdd(trimmed);
      setTitle("");
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <form className="add-todo" onSubmit={handleSubmit}>
      <input
        type="text"
        value={title}
        onChange={(e) => setTitle(e.target.value)}
        placeholder="What needs to be done?"
        disabled={submitting}
        aria-label="New todo title"
      />
      <button type="submit" disabled={submitting || !title.trim()}>
        Add
      </button>
    </form>
  );
}
