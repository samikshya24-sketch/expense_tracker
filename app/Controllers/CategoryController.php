<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\Category;

class CategoryController
{
    /** List all categories with optional search filter */
    public function index(): void
    {
        Auth::guard();
        $search       = trim($_GET['search'] ?? '');
        $categories   = Category::getAll($search ?: null);
        $flashSuccess = Auth::getFlash('success');
        $flashError   = Auth::getFlash('error');
        require __DIR__ . '/../Views/category/index.php';
    }

    /** Show add form */
    public function showAdd(): void
    {
        Auth::guard();
        $error = '';
        require __DIR__ . '/../Views/category/add.php';
    }

    /** Handle add form POST */
    public function add(): void
    {
        Auth::guard();
        $name  = strip_tags(trim($_POST['name']  ?? ''));
        $icon  = strip_tags(trim($_POST['icon']  ?? ''));
        $color = trim($_POST['color'] ?? '#6554C0');
        $error = '';

        if (empty($name)) {
            $error = 'Category name is required.';
            require __DIR__ . '/../Views/category/add.php';
            return;
        }

        if (empty($icon)) {
            $icon = '📦';
        }

        // Validate hex color
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $color = '#6554C0';
        }

        Category::create($name, $icon, $color);
        Auth::setFlash('success', '✓ Category "' . $name . '" added successfully.');
        Auth::redirect('/categories');
    }

    /** Show edit form */
    public function showEdit(): void
    {
        Auth::guard();
        $id       = (int)($_GET['id'] ?? 0);
        $category = Category::getById($id);
        if (!$category) { Auth::redirect('/categories'); }
        $error = '';
        require __DIR__ . '/../Views/category/edit.php';
    }

    /** Handle edit form POST */
    public function update(): void
    {
        Auth::guard();
        $id    = (int)($_POST['id']    ?? 0);
        $name  = strip_tags(trim($_POST['name']  ?? ''));
        $icon  = strip_tags(trim($_POST['icon']  ?? ''));
        $color = trim($_POST['color'] ?? '#6554C0');

        if (empty($name)) {
            $error    = 'Category name is required.';
            $category = Category::getById($id);
            require __DIR__ . '/../Views/category/edit.php';
            return;
        }

        if (empty($icon)) $icon = '📦';

        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $color = '#6554C0';
        }

        Category::update($id, $name, $icon, $color);
        Auth::setFlash('success', '✓ Category updated successfully.');
        Auth::redirect('/categories');
    }

    /** Delete a category */
    public function delete(): void
    {
        Auth::guard();
        $id      = (int)($_GET['id'] ?? 0);
        $deleted = Category::delete($id);

        if ($deleted) {
            Auth::setFlash('success', '✓ Category deleted.');
        } else {
            Auth::setFlash('error', 'Cannot delete — this category has expenses linked to it.');
        }

        Auth::redirect('/categories');
    }
}