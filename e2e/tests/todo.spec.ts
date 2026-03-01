import { test, expect } from "@playwright/test";

test.describe("Todo App E2E", () => {
  test.beforeEach(async ({ page }) => {
    await page.goto("/");
  });

  test("shows empty state when no todos exist", async ({ page }) => {
    await expect(page.getByText(/no todos yet/i)).toBeVisible();
  });

  test("full CRUD flow: add, complete, delete", async ({ page }) => {
    const todoTitle = `E2E Test Todo ${Date.now()}`;

    // --- Add a todo ---
    const input = page.getByPlaceholder("What needs to be done?");
    const addButton = page.getByRole("button", { name: /add/i });

    await input.fill(todoTitle);
    await addButton.click();

    // Verify the todo appears in the list
    const todoItem = page.getByText(todoTitle);
    await expect(todoItem).toBeVisible();

    // Input should be cleared after adding
    await expect(input).toHaveValue("");

    // --- Complete the todo ---
    const completeButton = page.getByLabel("Mark complete");
    await completeButton.click();

    // Verify the todo is now marked as completed (has completed class)
    const listItem = page.locator(".todo-item.completed").filter({ hasText: todoTitle });
    await expect(listItem).toBeVisible();

    // --- Delete the todo ---
    const deleteButton = page.getByLabel(`Delete "${todoTitle}"`);
    await deleteButton.click();

    // Verify the todo is removed from the list
    await expect(page.getByText(todoTitle)).not.toBeVisible();
  });

  test("add button is disabled with empty input", async ({ page }) => {
    const addButton = page.getByRole("button", { name: /add/i });
    await expect(addButton).toBeDisabled();
  });

  test("can add multiple todos", async ({ page }) => {
    const input = page.getByPlaceholder("What needs to be done?");
    const addButton = page.getByRole("button", { name: /add/i });

    await input.fill("First todo");
    await addButton.click();
    await expect(page.getByText("First todo")).toBeVisible();

    await input.fill("Second todo");
    await addButton.click();
    await expect(page.getByText("Second todo")).toBeVisible();

    // Both should be visible
    await expect(page.getByText("First todo")).toBeVisible();
    await expect(page.getByText("Second todo")).toBeVisible();
  });

  test("toggling a completed todo marks it incomplete", async ({ page }) => {
    const input = page.getByPlaceholder("What needs to be done?");
    const addButton = page.getByRole("button", { name: /add/i });

    const todoTitle = `Toggle Test ${Date.now()}`;
    await input.fill(todoTitle);
    await addButton.click();

    // Complete
    await page.getByLabel("Mark complete").click();
    await expect(page.locator(".todo-item.completed").filter({ hasText: todoTitle })).toBeVisible();

    // Uncomplete
    await page.getByLabel("Mark incomplete").click();
    await expect(
      page.locator(".todo-item:not(.completed)").filter({ hasText: todoTitle })
    ).toBeVisible();
  });
});
