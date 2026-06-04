<?php
namespace App\Models;

use App\Core\Database;

class Budget
{
    public static function getByUserMonth(int $userId, string $monthYear): float
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT amount FROM budgets WHERE user_id=? AND month_year=?');
        $stmt->execute([$userId, $monthYear]);
        return (float)($stmt->fetchColumn() ?: 0);
    }

    /** Get full budget row (id + amount) for a given user/month */
    public static function getRowByUserMonth(int $userId, string $monthYear): array|false
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT * FROM budgets WHERE user_id=? AND month_year=?');
        $stmt->execute([$userId, $monthYear]);
        return $stmt->fetch();
    }

    /** Get all budget rows for a user, for history listing */
    public static function getAllByUser(int $userId): array
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT * FROM budgets WHERE user_id=? ORDER BY month_year DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get per-category budgets for a given month.
     * Returns assoc array keyed by category_id => budget_amount.
     * Uses the overall monthly budget divided equally across active categories
     * as a simple per-category limit (can be extended to a cat_budgets table later).
     */
    public static function getCategoryBudgets(int $userId, string $monthYear, int $categoryCount): array
    {
        $total = self::getByUserMonth($userId, $monthYear);
        if ($total <= 0 || $categoryCount <= 0) return [];
        $perCat = $total / $categoryCount;
        return ['per_category' => round($perCat, 2), 'total' => $total];
    }

    public static function save(int $userId, float $amount, string $monthYear): void
    {
        $db = Database::connect();
        $db->prepare('INSERT INTO budgets (user_id,amount,month_year) VALUES (?,?,?) ON DUPLICATE KEY UPDATE amount=VALUES(amount)')
           ->execute([$userId, $amount, $monthYear]);
    }

    /** Delete a specific budget row by id — ownership verified */
    public static function deleteById(int $id, int $userId): void
    {
        $db = Database::connect();
        $db->prepare('DELETE FROM budgets WHERE id=? AND user_id=?')->execute([$id, $userId]);
    }

    /** Get budgets with optional month range filter and sort direction */
public static function getAllByUserFiltered(
    int $userId,
    string $from = '',
    string $to   = '',
    string $sort = 'DESC'
): array {
    $db     = Database::connect();
    $sql    = 'SELECT * FROM budgets WHERE user_id = ?';
    $params = [$userId];

    if ($from) {
        $sql     .= ' AND month_year >= ?';
        $params[] = $from;
    }
    if ($to) {
        $sql     .= ' AND month_year <= ?';
        $params[] = $to;
    }

    // Whitelist sort to prevent injection
    $sort  = strtoupper($sort) === 'ASC' ? 'ASC' : 'DESC';
    $sql  .= " ORDER BY month_year $sort";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
}
