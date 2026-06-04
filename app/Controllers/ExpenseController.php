<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\Expense;
use App\Models\Category;

/**
 * Controller responsible for expense CRUD actions.
 */
class ExpenseController
{
    /**
     * Show the add expense form.
     */
    public function showAdd(): void
    {
        Auth::guard();
        $categories = Category::getAll();
        require __DIR__ . '/../Views/expense/add.php';
    }

    /**
     * Handle form submission for adding a new expense.
     */
    public function add(): void
    {
        Auth::guard();
        $catId  = (int)($_POST['category_id'] ?? 0);
        $amount = (float)($_POST['amount']     ?? 0);
        $note   = trim($_POST['note']          ?? '');
        $date   = trim($_POST['spent_date']    ?? '');
        if (empty($date)) $date = date('Y-m-d');

        // Validate required fields before saving.
        if ($catId <= 0 || $amount <= 0) {
            Auth::setFlash('error', 'Please select a category and enter a valid amount.');
            Auth::redirect('/add');
        }

        Expense::add(Auth::id(), $catId, $amount, $note ?: null, $date);
        Auth::setFlash('success', '✓ Expense saved successfully!');
        Auth::redirect('/');
    }

    /**
     * Show the edit form for an existing expense.
     */
    public function showEdit(): void
    {
        Auth::guard();
        $id      = (int)($_GET['id'] ?? 0);
        $expense = Expense::getById($id, Auth::id());
        if (!$expense) { Auth::redirect('/'); }
        $categories = Category::getAll();
        require __DIR__ . '/../Views/expense/edit.php';
    }

    /**
     * Persist updates to an existing expense.
     */
    public function update(): void
    {
        Auth::guard();
        $id     = (int)($_POST['id']          ?? 0);
        $catId  = (int)($_POST['category_id'] ?? 0);
        $amount = (float)($_POST['amount']    ?? 0);
        $note   = trim($_POST['note']         ?? '');
        $date   = trim($_POST['spent_date']   ?? '');
        if (empty($date)) $date = date('Y-m-d');

        // Ensure the user is modifying a valid expense.
        if ($id <= 0 || $catId <= 0 || $amount <= 0) {
            Auth::setFlash('error', 'Invalid expense data. Please check all fields.');
            Auth::redirect('/');
        }

        Expense::update($id, Auth::id(), $catId, $amount, $note ?: null, $date);
        Auth::setFlash('success', '✓ Expense updated successfully!');
        Auth::redirect('/');
    }

    /**
     * Delete an expense owned by the current user.
     */
    public function delete(): void
    {
        Auth::guard();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            Expense::deleteById($id, Auth::id());
            Auth::setFlash('success', '✓ Expense deleted.');
        }
        Auth::redirect('/');
    }
}
