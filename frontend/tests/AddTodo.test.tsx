import { describe, it, expect, vi, beforeEach } from "vitest";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { AddTodo } from "../src/components/AddTodo";

describe("AddTodo", () => {
  let onAdd: ReturnType<typeof vi.fn>;

  beforeEach(() => {
    onAdd = vi.fn().mockResolvedValue(undefined);
  });

  it("calls onAdd with trimmed title on submit", async () => {
    render(<AddTodo onAdd={onAdd} />);
    const user = userEvent.setup();

    const input = screen.getByLabelText("New todo title");
    const button = screen.getByRole("button", { name: /add/i });

    await user.type(input, "  Buy groceries  ");
    await user.click(button);

    expect(onAdd).toHaveBeenCalledWith("Buy groceries");
  });

  it("clears input after successful submission", async () => {
    render(<AddTodo onAdd={onAdd} />);
    const user = userEvent.setup();

    const input = screen.getByLabelText("New todo title");

    await user.type(input, "Buy groceries");
    await user.click(screen.getByRole("button", { name: /add/i }));

    expect(input).toHaveValue("");
  });

  it("disables submit button when title is empty", () => {
    render(<AddTodo onAdd={onAdd} />);

    const button = screen.getByRole("button", { name: /add/i });
    expect(button).toBeDisabled();
  });

  it("disables submit button when title is only whitespace", async () => {
    render(<AddTodo onAdd={onAdd} />);
    const user = userEvent.setup();

    const input = screen.getByLabelText("New todo title");
    await user.type(input, "   ");

    const button = screen.getByRole("button", { name: /add/i });
    expect(button).toBeDisabled();
  });

  it("does not call onAdd when submitting empty form", async () => {
    render(<AddTodo onAdd={onAdd} />);
    const user = userEvent.setup();

    // Try to submit by pressing Enter on empty input
    const input = screen.getByLabelText("New todo title");
    await user.type(input, "{enter}");

    expect(onAdd).not.toHaveBeenCalled();
  });

  it("disables input and button while submitting", async () => {
    // Create a promise we can control to keep the submission pending
    let resolveSubmit!: () => void;
    const pendingAdd = vi.fn(
      () => new Promise<void>((resolve) => { resolveSubmit = resolve; })
    );

    render(<AddTodo onAdd={pendingAdd} />);
    const user = userEvent.setup();

    const input = screen.getByLabelText("New todo title");
    await user.type(input, "Buy groceries");

    const button = screen.getByRole("button", { name: /add/i });
    await user.click(button);

    // While submitting, both should be disabled
    expect(input).toBeDisabled();
    expect(button).toBeDisabled();

    // Resolve the pending submission
    resolveSubmit();
  });
});
