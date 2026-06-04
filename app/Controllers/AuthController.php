<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\User;

class AuthController
{
    public function showRegister(): void
    {
        Auth::start();
        if (Auth::id()) { Auth::redirect('/'); }
        $error = '';
        require __DIR__ . '/../Views/auth/register.php';
    }

    public function register(): void
    {
        Auth::start();
        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $error    = '';

        if (empty($name) || empty($email) || empty($password)) {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif (User::findByEmail($email)) {
            $error = 'An account with that email already exists.';
        } else {
            User::create($name, $email, $password);
            Auth::setFlash('success', 'Your account has been registered. Please login to your account.');
            Auth::redirect('/login');
        }

        require __DIR__ . '/../Views/auth/register.php';
    }

    public function showLogin(): void
    {
        Auth::start();
        if (Auth::id()) { Auth::redirect('/'); }
        $error = '';
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        Auth::start();
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $error    = '';

        $user = User::findByEmail($email);

        if (!$user) {
            $error = 'Invalid email or password.';
        } elseif (!password_verify($password, $user['password'])) {
            $error = 'Invalid email or password.';
        } elseif ((int)$user['is_active'] === 0) {
            // Deactivated account — block login with a clear message
            $error = 'This account has been deactivated. Please contact support.';
        } else {
            Auth::login($user['id'], $user['name']);
            Auth::login($user['id'], $user['name']);
            Auth::setAdmin((bool)$user['is_admin']); // <- admin
            Auth::redirect('/');

            Auth::redirect('/');
        }

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function logout(): void
    {
        Auth::logout();
        Auth::redirect('/login');
    }
}
