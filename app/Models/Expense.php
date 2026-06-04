<?php
namespace App\Models;

use App\Core\Database;

/**
 * Expense model contains all database operations related to expenses.
 */
class Expense
{
    /**
     * Create a new expense record for a user.
     */
    public static function add(int $userId, int $catId, float $amount, ?string $note, string $date): void
    {
        $db = Database::connect();
        $db->prepare('INSERT INTO expenses (user_id,category_id,amount,note,spent_date) VALUES (?,?,?,?,?)')
           ->execute([$userId, $catId, $amount, empty($note) ? null : $note, $date]);
    }

    /**
     * Fetch a single expense belonging to the given user.
     */
    public static function getById(int $id, int $userId): array|false
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT e.*, c.name AS cat_name, c.icon, c.color FROM expenses e JOIN categories c ON e.category_id=c.id WHERE e.id=? AND e.user_id=?');
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    /**
     * Update an existing expense record.
     */
    public static function update(int $id, int $userId, int $catId, float $amount, ?string $note, string $date): void
    {
        $db = Database::connect();
        $db->prepare('UPDATE expenses SET category_id=?,amount=?,note=?,spent_date=? WHERE id=? AND user_id=?')
           ->execute([$catId, $amount, empty($note) ? null : $note, $date, $id, $userId]);
    }

    /**
     * Delete an expense if it belongs to the specified user.
     */
    public static function deleteById(int $id, int $userId): void
    {
        $db = Database::connect();
        $db->prepare('DELETE FROM expenses WHERE id=? AND user_id=?')->execute([$id, $userId]);
    }

    /**
     * List expenses for a user with optional category, date range, and search filters.
     */
    public static function getByUser(int $userId, ?int $filterCatId=null, ?string $filterFrom=null, ?string $filterTo=null, ?string $searchQuery=null): array
    {
        $db     = Database::connect();
        $sql    = 'SELECT e.*, c.name AS cat_name, c.icon, c.color FROM expenses e JOIN categories c ON e.category_id=c.id WHERE e.user_id=?';
        $params = [$userId];
        if ($filterCatId) { $sql .= ' AND e.category_id=?'; $params[] = $filterCatId; }
        if ($filterFrom)  { $sql .= ' AND e.spent_date>=?'; $params[] = $filterFrom; }
        if ($filterTo)    { $sql .= ' AND e.spent_date<=?'; $params[] = $filterTo; }
        if ($searchQuery) {
            $sql .= ' AND (e.note LIKE ? OR c.name LIKE ?)';
            $params[] = '%' . $searchQuery . '%';
            $params[] = '%' . $searchQuery . '%';
        }

        // Most recent expenses first by date and creation timestamp.
        $sql .= ' ORDER BY e.spent_date DESC, e.created_at DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getMonthlyTotal(int $userId, int $m, int $y): float
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT COALESCE(SUM(amount),0) FROM expenses WHERE user_id=? AND MONTH(spent_date)=? AND YEAR(spent_date)=?');
        $stmt->execute([$userId, $m, $y]);
        return (float)$stmt->fetchColumn();
    }

    public static function getCategoryBreakdown(int $userId, int $m, int $y): array
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT c.name,c.icon,c.color,COALESCE(SUM(e.amount),0) AS total FROM categories c LEFT JOIN expenses e ON e.category_id=c.id AND e.user_id=? AND MONTH(e.spent_date)=? AND YEAR(e.spent_date)=? GROUP BY c.id ORDER BY total DESC');
        $stmt->execute([$userId, $m, $y]);
        return $stmt->fetchAll();
    }
}
