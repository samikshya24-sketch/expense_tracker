<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\User;

class ProfileController
{
    // ── VIEW PROFILE ──────────────────────────────────────────

    /** Show logged-in user's profile page */
    public function show(): void
    {
        Auth::guard();
        $user         = User::findById(Auth::id());
        $flashSuccess = Auth::getFlash('success');
        $flashError   = Auth::getFlash('error');
        require __DIR__ . '/../Views/profile/show.php';
    }

    // ── EDIT PROFILE (name + email) ───────────────────────────

    /** Show edit profile form */
    public function showEdit(): void
    {
        Auth::guard();
        $user  = User::findById(Auth::id());
        $error = '';
        require __DIR__ . '/../Views/profile/edit.php';
    }

    /** Handle edit profile POST */
    public function update(): void
    {
        Auth::guard();
        $id    = Auth::id();
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');
        $error = '';

        // Validation
        if (empty($name) || empty($email)) {
            $error = 'Name and email are required.';
        } elseif (strlen($name) < 2) {
            $error = 'Name must be at least 2 characters.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (User::emailTakenByOther($email, $id)) {
            $error = 'That email address is already used by another account.';
        }

        if ($error) {
            $user = User::findById($id);
            require __DIR__ . '/../Views/profile/edit.php';
            return;
        }

        User::updateProfile($id, $name, $email);
        User::updateSessionName($name); // keep session in sync

        Auth::setFlash('success', '✓ Profile updated successfully.');
        Auth::redirect('/profile');
    }

    // ── CHANGE PASSWORD ───────────────────────────────────────

    /** Show change password form */
    public function showChangePassword(): void
    {
        Auth::guard();
        $error = '';
        require __DIR__ . '/../Views/profile/change_password.php';
    }

    /** Handle change password POST */
    public function changePassword(): void
    {
        Auth::guard();
        $id          = Auth::id();
        $current     = $_POST['current_password'] ?? '';
        $new         = $_POST['new_password']     ?? '';
        $confirm     = $_POST['confirm_password'] ?? '';
        $error       = '';

        if (empty($current) || empty($new) || empty($confirm)) {
            $error = 'All password fields are required.';
        } elseif (!User::verifyPassword($id, $current)) {
            $error = 'Your current password is incorrect.';
        } elseif (strlen($new) < 6) {
            $error = 'New password must be at least 6 characters.';
        } elseif ($new !== $confirm) {
            $error = 'New passwords do not match.';
        } elseif ($current === $new) {
            $error = 'New password must be different from your current password.';
        }

        if ($error) {
            require __DIR__ . '/../Views/profile/change_password.php';
            return;
        }

        User::updatePassword($id, $new);
        Auth::setFlash('success', '✓ Password changed successfully.');
        Auth::redirect('/profile');
    }

    // ── DELETE ACCOUNT ────────────────────────────────────────

    /** Show delete account confirmation page */
    public function showDelete(): void
    {
        Auth::guard();
        $error = '';
        require __DIR__ . '/../Views/profile/delete.php';
    }

    /** Handle delete account POST — requires password confirmation */
    public function delete(): void
    {
        Auth::guard();
        $id       = Auth::id();
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm']  ?? '';
        $error    = '';

        if (empty($password)) {
            $error = 'Please enter your password to confirm deletion.';
        } elseif ($confirm !== 'DELETE') {
            $error = 'Please type DELETE in the confirmation box.';
        } elseif (!User::verifyPassword($id, $password)) {
            $error = 'Password is incorrect.';
        }

        if ($error) {
            require __DIR__ . '/../Views/profile/delete.php';
            return;
        }

        // Destroy session first, then delete (CASCADE removes expenses + budgets)
        Auth::logout();
        User::delete($id);

        Auth::setFlash('success', 'Your account has been deleted.');
        Auth::redirect('/login');
    }

    // ── USER LIST / SEARCH / FILTER ───────────────────────────

    /**
     * List all users with search + filter.
     * This is a self-service directory — every logged-in user can see
     * the list (name, email, status, join date) but cannot edit others.
     */
    public function list(): void
    {
       Auth::requireAdmin(); 

    $search = trim($_GET['search'] ?? '');


        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';
        $sort   = $_GET['sort']   ?? 'created_at';
        $dir    = $_GET['dir']    ?? 'DESC';

        // Sanitise status
        if (!in_array($status, ['active', 'inactive', ''])) $status = '';

        $users        = User::list(
            $search ?: null,
            $status ?: null,
            $sort,
            $dir
        );
        $totalUsers   = User::count();
        $activeUsers  = User::count('active');
        $flashSuccess = Auth::getFlash('success');

        require __DIR__ . '/../Views/profile/list.php';
    }

  
/** Show edit form for a specific user */
public function adminShowEdit(): void
{
    Auth::requireAdmin();
    $id   = (int)($_GET['id'] ?? 0);
    $user = User::findById($id);
    if (!$user) { Auth::redirect('/users'); }
    $error = '';
    require __DIR__ . '/../Views/profile/edit_user.php';
}

/** Handle edit form POST */
public function adminUpdate(): void
{
    Auth::requireAdmin();
    $id       = (int)($_POST['id']       ?? 0);
    $name     = strip_tags(trim($_POST['name']     ?? ''));
    $email    = strip_tags(trim($_POST['email']    ?? ''));
    $isActive = (int)($_POST['is_active'] ?? 1);
    $isAdmin  = (int)($_POST['is_admin']  ?? 0);

    if ($id <= 0 || empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid data. Please check all fields.';
        $user  = User::findById($id);
        require __DIR__ . '/../Views/profile/edit_user.php';
        return;
    }

    if (User::emailTakenByOther($email, $id)) {
        $error = 'That email is already used by another account.';
        $user  = User::findById($id);
        require __DIR__ . '/../Views/profile/edit_user.php';
        return;
    }

    User::updateProfile($id, $name, $email);
    User::updateStatus($id, $isActive, $isAdmin);
    Auth::setFlash('success', '✓ User updated successfully.');
    Auth::redirect('/users');
}

/** Activate a user */
public function adminActivate(): void
{
    Auth::requireAdmin();
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        User::activate($id);
        Auth::setFlash('success', '✓ User activated.');
    }
    Auth::redirect('/users');
}

/** Deactivate a user — cannot deactivate yourself */
public function adminDeactivate(): void
{
    Auth::requireAdmin();
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && $id !== Auth::id()) {
        User::deactivate($id);
        Auth::setFlash('success', '✓ User deactivated.');
    }
    Auth::redirect('/users');
}

    /** Deactivate own account — keeps data, just blocks login */
    public function deactivate(): void
    {
        Auth::guard();
        $id       = Auth::id();
        $password = $_POST['password'] ?? '';
        $error    = '';

        if (empty($password)) {
            $error = 'Please enter your password to deactivate.';
        } elseif (!User::verifyPassword($id, $password)) {
            $error = 'Password is incorrect.';
        }

        if ($error) {
            require __DIR__ . '/../Views/profile/delete.php';
            return;
        }

        User::deactivate($id);
        Auth::logout();
        Auth::setFlash('success', 'Your account has been deactivated. Contact support to reactivate.');
        Auth::redirect('/login');
    }

}
