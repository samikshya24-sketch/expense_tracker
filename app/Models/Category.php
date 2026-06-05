<?php
namespace App\Models;

use App\Core\Database;

class Category
{
    /** Get all categories — optionally filter by name */
    public static function getAll(?string $search = null): array
    {
        $db     = Database::connect();
        $sql    = 'SELECT * FROM categories WHERE 1=1';
        $params = [];

        if ($search) {
            $sql     .= ' AND name LIKE ?';
            $params[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY name ASC';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Get single category by id */
    public static function getById(int $id): array|false
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Add a new category */
    public static function create(string $name, string $icon, string $color): void
    {
        $db = Database::connect();
        $db->prepare('INSERT INTO categories (name, icon, color) VALUES (?, ?, ?)')
           ->execute([$name, $icon, $color]);
    }

    /** Update an existing category */
    public static function update(int $id, string $name, string $icon, string $color): void
    {
        $db = Database::connect();
        $db->prepare('UPDATE categories SET name=?, icon=?, color=? WHERE id=?')
           ->execute([$name, $icon, $color, $id]);
    }

    /** Delete a category — only if no expenses use it */
    public static function delete(int $id): bool
    {
        $db   = Database::connect();
        // Check if any expenses reference this category
        $stmt = $db->prepare('SELECT COUNT(*) FROM expenses WHERE category_id = ?');
        $stmt->execute([$id]);
        if ((int)$stmt->fetchColumn() > 0) {
            return false; // cannot delete — in use
        }
        $db->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
        return true;
    }
}