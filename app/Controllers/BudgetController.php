<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\Budget;
use App\Models\User;

class BudgetController
{
   public function showPanel(): void
{
    Auth::guard();
    $userId    = Auth::id();
    $monthYear = date('Y-m');
    $budget    = Budget::getByUserMonth($userId, $monthYear);
    $user      = User::findById($userId);

    // Filter params
    $from = trim($_GET['from'] ?? '');
    $to   = trim($_GET['to']   ?? '');
    $sort = ($_GET['sort'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

    $allBudgets   = Budget::getAllByUserFiltered($userId, $from, $to, $sort);
    $flashSuccess = Auth::getFlash('success');
    $flashError   = Auth::getFlash('error');

    require __DIR__ . '/../Views/budget/panel.php';
}

    public function save(): void
    {
        Auth::guard();
        $amount    = (float)trim($_POST['amount']     ?? 0);
        $monthYear = trim($_POST['month_year']        ?? date('Y-m'));

        if ($amount <= 0) {
            Auth::setFlash('error', 'Please enter a valid budget amount greater than zero.');
            Auth::redirect('/user');
        }

        Budget::save(Auth::id(), $amount, $monthYear);
        Auth::setFlash('success', '✓ Budget for ' . $monthYear . ' saved successfully!');
        Auth::redirect('/user');
    }

    public function delete(): void
    {
        Auth::guard();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            Budget::deleteById($id, Auth::id());
            Auth::setFlash('success', '✓ Budget entry deleted.');
        }
        Auth::redirect('/user');
    }
}
