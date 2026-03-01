import { describe, it, expect, vi, beforeEach } from "vitest";
import { render, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import App from "../src/App";
import { api } from "../src/api";
import type { Todo } from "../src/types";

vi.mock("../src/api", () => ({
  api: {
    getTodos: vi.fn(),
    createTodo: vi.fn(),
    completeTodo: vi.fn(),
    deleteTodo: vi.fn(),
  },
}));

const mockTodos: Todo[] = [
  { id: 1, title: "Buy groceries", completed: false, createdAt: "2026-01-01T00:00:00Z" },
  { id: 2, title: "Walk the dog", completed: true, createdAt: "2026-01-02T00:00:00Z" },
];

beforeEach(() => {
  vi.resetAllMocks();
});

describe("TodoList (App)", () => {
  it("renders loading state initially", () => {
    vi.mocked(api.getTodos).mockReturnValue(new Promise(() => {})); // never resolves
    render(<App />);
    expect(screen.getByText("Loading...")).toBeInTheDocument();
  });

  it("renders empty state when no todos exist", async () => {
    vi.mocked(api.getTodos).mockResolvedValue([]);
    render(<App />);

    await waitFor(() => {
      expect(screen.getByText(/no todos yet/i)).toBeInTheDocument();
    });
  });

  it("renders a list of todos", async () => {
    vi.mocked(api.getTodos).mockResolvedValue(mockTodos);
    render(<App />);

    await waitFor(() => {
      expect(screen.getByText("Buy groceries")).toBeInTheDocument();
      expect(screen.getByText("Walk the dog")).toBeInTheDocument();
    });
  });

  it("calls completeTodo API when toggle button is clicked", async () => {
    vi.mocked(api.getTodos).mockResolvedValue(mockTodos);
    vi.mocked(api.completeTodo).mockResolvedValue({
      ...mockTodos[0],
      completed: true,
    });

    render(<App />);
    const user = userEvent.setup();

    await waitFor(() => {
      expect(screen.getByText("Buy groceries")).toBeInTheDocument();
    });

    const completeButton = screen.getByLabelText("Mark complete");
    await user.click(completeButton);

    expect(api.completeTodo).toHaveBeenCalledWith(1);
  });

  it("removes a todo from the list after deletion", async () => {
    vi.mocked(api.getTodos).mockResolvedValue(mockTodos);
    vi.mocked(api.deleteTodo).mockResolvedValue(undefined);

    render(<App />);
    const user = userEvent.setup();

    await waitFor(() => {
      expect(screen.getByText("Buy groceries")).toBeInTheDocument();
    });

    const deleteButton = screen.getByLabelText('Delete "Buy groceries"');
    await user.click(deleteButton);

    await waitFor(() => {
      expect(screen.queryByText("Buy groceries")).not.toBeInTheDocument();
    });
    expect(api.deleteTodo).toHaveBeenCalledWith(1);
  });

  it("displays an error message when fetching todos fails", async () => {
    vi.mocked(api.getTodos).mockRejectedValue(new Error("API error: 500"));
    render(<App />);

    await waitFor(() => {
      expect(screen.getByText("API error: 500")).toBeInTheDocument();
    });
  });
});
