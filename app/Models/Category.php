<?php
namespace App\Models;

use App\Core\Database;

class Category
{
    /** Return all seeded categories — used by Add Expense panel tile grid */
    public static function getAll(): array
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT * FROM categories ORDER BY id');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
