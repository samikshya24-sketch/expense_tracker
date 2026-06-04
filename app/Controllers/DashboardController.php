<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Category;

class DashboardController
{
    public function index(): void
    {
        Auth::guard();

        $userId    = Auth::id();
        $monthYear = date('Y-m');
        $month     = (int)date('n');
        $year      = (int)date('Y');

        // Filter params from GET
        $filterCatId = (int)($_GET['cat']  ?? 0) ?: null;
        $filterFrom  = trim($_GET['from']  ?? '') ?: null;
        $filterTo    = trim($_GET['to']    ?? '') ?: null;
        $searchQuery = trim($_GET['search'] ?? '') ?: null;

        // Core budget + spending data
        $monthlyTotal  = Expense::getMonthlyTotal($userId, $month, $year);
        $budget        = Budget::getByUserMonth($userId, $monthYear);
        $remaining     = $budget - $monthlyTotal;
        $overBudget    = $remaining < 0;

        // Category breakdown with budget status calculated
        $categoriesRaw  = Expense::getCategoryBreakdown($userId, $month, $year);
        $allCategories  = Category::getAll();

        // Calculate per-category budget threshold (equal split of total budget)
        $activeCatCount = count(array_filter($categoriesRaw, fn($c) => $c['total'] > 0));
        $perCatBudget   = ($budget > 0 && $activeCatCount > 0)
                          ? round($budget / $activeCatCount, 2)
                          : 0;

        // Attach budget status to each category
        $categories = array_map(function($cat) use ($perCatBudget) {
            $cat['per_cat_budget'] = $perCatBudget;
            if ($perCatBudget <= 0 || $cat['total'] <= 0) {
                $cat['budget_status'] = 'none';       // no budget set or no spending
            } elseif ($cat['total'] >= $perCatBudget) {
                $cat['budget_status'] = 'over';       // at or over budget
            } elseif ($cat['total'] >= $perCatBudget * 0.8) {
                $cat['budget_status'] = 'warning';    // within 80–100% of budget
            } else {
                $cat['budget_status'] = 'ok';         // under 80%
            }
            return $cat;
        }, $categoriesRaw);

        // Expenses list (with optional filters)
        $expenses = Expense::getByUser($userId, $filterCatId, $filterFrom, $filterTo, $searchQuery);

        // Flash messages — get ALL types (success, error, warning)
        $flashSuccess = Auth::getFlash('success');
        $flashError   = Auth::getFlash('error');
        $flashWarning = Auth::getFlash('warning');

        require __DIR__ . '/../Views/dashboard/index.php';
    }
}
