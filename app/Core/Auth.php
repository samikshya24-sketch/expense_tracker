<?php
namespace App\Core;

class Auth
{
    // Base path — must match the subfolder your app is in
    private static string $base = '/expense_tracker/public';

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** Redirect to login if not authenticated */
    public static function guard(): void
    {
        self::start();
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . self::$base . '/login');
            exit;
        }
    }

    public static function login(int $id, string $name): void
    {
        self::start();
        $_SESSION['user_id']   = $id;
        $_SESSION['user_name'] = $name;
    }

    public static function logout(): void
    {
        self::start();
        session_destroy();
    }

    public static function id(): int
    {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    public static function name(): string
    {
        return $_SESSION['user_name'] ?? '';
    }

    /**
     * Store a flash message in session under a type key.
     * Supported types: 'success', 'error', 'warning', 'info'
     */
    public static function setFlash(string $key, string $msg): void
    {
        self::start();
        $_SESSION['flash'][$key] = $msg;
    }

    /**
     * Read and immediately clear a flash message.
     * Returns empty string if no message set for that key.
     */
    public static function getFlash(string $key): string
    {
        self::start();
        $msg = $_SESSION['flash'][$key] ?? '';
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    /**
     * Get ALL flash messages at once and clear them.
     * Returns ['type' => 'message'] array.
     */
    public static function getAllFlash(): array
    {
        self::start();
        $all = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $all;
    }
    /** Store admin status in session after login */
public static function setAdmin(bool $isAdmin): void
{
    self::start();
    $_SESSION['is_admin'] = $isAdmin;
}

/** Check if logged-in user is admin */
public static function isAdmin(): bool
{
    self::start();
    return !empty($_SESSION['is_admin']);
}

/** Redirect with 403 if not admin */
public static function requireAdmin(): void
{
    self::guard();
    if (!self::isAdmin()) {
        http_response_code(403);
        die('<div style="text-align:center;padding:60px">
            <h1 style="color:#DE350B">403 - Access Denied</h1>
         <p>You do not have permission to view this page.</p>
            <a href="/expense_tracker/public/">Go Home</a>
        </div>');
    }
}


    /** Build a full URL with the base path and redirect */
    public static function redirect(string $path): void
    {
        header('Location: ' . self::$base . $path);
        exit;
    }
}
