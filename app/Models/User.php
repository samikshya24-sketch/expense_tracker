<?php
namespace App\Models;

use App\Core\Database;

class User
{
    // ── READ ──────────────────────────────────────────────────

    /** Find a user by email — used during login */
    public static function findByEmail(string $email): array|false
    {
        $db   = Database::connect();
        $stmt = $db->prepare(
            'SELECT * FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /** Find user by ID — returns id, name, email, is_active, created_at (no password) */
    public static function findById(int $id): array|false
    {
        $db   = Database::connect();
        $stmt = $db->prepare(
            'SELECT id, name, email, is_active, created_at FROM users WHERE id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * List all users with optional search + status filter.
     * Used by the profile/admin list view.
     *
     * @param string|null $search  partial match on name or email
     * @param string|null $status  'active' | 'inactive' | null = all
     * @param string      $sort    column to sort by: name|email|created_at
     * @param string      $dir     ASC | DESC
     */
    public static function list(
        ?string $search = null,
        ?string $status = null,
        string  $sort   = 'created_at',
        string  $dir    = 'DESC'
    ): array {
        $db     = Database::connect();
        $allowed_sorts = ['name', 'email', 'created_at'];
        $sort   = in_array($sort, $allowed_sorts) ? $sort : 'created_at';
        $dir    = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $sql    = 'SELECT id, name, email, is_active, created_at FROM users WHERE 1=1';
        $params = [];

        if ($search) {
            $sql      .= ' AND (name LIKE ? OR email LIKE ?)';
            $like      = '%' . $search . '%';
            $params[]  = $like;
            $params[]  = $like;
        }

        if ($status === 'active') {
            $sql .= ' AND is_active = 1';
        } elseif ($status === 'inactive') {
            $sql .= ' AND is_active = 0';
        }

        $sql .= " ORDER BY $sort $dir";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Count total users — optionally filtered by status.
     */
    public static function count(?string $status = null): int
    {
        $db     = Database::connect();
        $sql    = 'SELECT COUNT(*) FROM users WHERE 1=1';
        $params = [];

        if ($status === 'active')   { $sql .= ' AND is_active = 1'; }
        if ($status === 'inactive') { $sql .= ' AND is_active = 0'; }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    // ── CREATE ────────────────────────────────────────────────

    /**
     * Create a new user account.
     * Password is bcrypt-hashed here — never stored plain.
     */
    public static function create(string $name, string $email, string $password): int
    {
        $db   = Database::connect();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare(
            'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $email, $hash]);
        return (int)$db->lastInsertId();
    }

    // ── UPDATE ────────────────────────────────────────────────

    /**
     * Update name and/or email for a user.
     * Email uniqueness is checked in the controller before calling this.
     */
    public static function updateProfile(int $id, string $name, string $email): void
    {
        $db = Database::connect();
        $db->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?')
           ->execute([$name, $email, $id]);
    }

    /**
     * Update password.
     * New password is bcrypt-hashed here.
     */
    public static function updatePassword(int $id, string $newPassword): void
    {
        $db   = Database::connect();
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $db->prepare('UPDATE users SET password = ? WHERE id = ?')
           ->execute([$hash, $id]);
    }

    /**
     * Refresh the session name after a profile name change.
     */
    public static function updateSessionName(string $name): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            $_SESSION['user_name'] = $name;
        }
    }

    // ── ACTIVATE / DEACTIVATE ─────────────────────────────────

    /**
     * Soft-deactivate a user (is_active = 0).
     * Ownership check: a user can only deactivate their own account.
     */
    public static function deactivate(int $id): void
    {
        $db = Database::connect();
        $db->prepare('UPDATE users SET is_active = 0 WHERE id = ?')
           ->execute([$id]);
    }

    /**
     * Re-activate a user account.
     */
    public static function activate(int $id): void
    {
        $db = Database::connect();
        $db->prepare('UPDATE users SET is_active = 1 WHERE id = ?')
           ->execute([$id]);
    }

    // ── DELETE ────────────────────────────────────────────────

    /**
     * Hard-delete a user and all their data (CASCADE handled by DB FK).
     * Only callable when the user confirms account deletion.
     */
    public static function delete(int $id): void
    {
        $db = Database::connect();
        $db->prepare('DELETE FROM users WHERE id = ?')
           ->execute([$id]);
    }

    public static function updateStatus(int $id, int $isActive, int $isAdmin): void
{
    $db = Database::connect();
    $db->prepare('UPDATE users SET is_active = ?, is_admin = ? WHERE id = ?')
       ->execute([$isActive, $isAdmin, $id]);
}

   
    // ── SECURITY HELPERS ──────────────────────────────────────

    /**
     * Check whether a given email is already taken by a DIFFERENT user.
     * Used during profile edit to detect conflicts.
     */
    public static function emailTakenByOther(string $email, int $currentUserId): bool
    {
        $db   = Database::connect();
        $stmt = $db->prepare(
            'SELECT COUNT(*) FROM users WHERE email = ? AND id != ?'
        );
        $stmt->execute([$email, $currentUserId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Verify the user's current password — used before allowing
     * a password change or account deletion.
     */
    public static function verifyPassword(int $id, string $password): bool
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $hash = $stmt->fetchColumn();
        return $hash && password_verify($password, $hash);
    }
}
