<?php
// ── Load all core files ───────────────────────────────────────
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Auth.php';
require_once __DIR__ . '/../app/Core/Router.php';

// ── Load all models ───────────────────────────────────────────
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Budget.php';
require_once __DIR__ . '/../app/Models/Expense.php';
require_once __DIR__ . '/../app/Models/Category.php';
require_once __DIR__ . '/../app/Controllers/CategoryController.php';

// ── Load all controllers ──────────────────────────────────────
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/ProfileController.php';
require_once __DIR__ . '/../app/Controllers/BudgetController.php';
require_once __DIR__ . '/../app/Controllers/ExpenseController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';

use App\Core\Router;

$router = new Router();

// ── Auth ──────────────────────────────────────────────────────
$router->get('/login',    'AuthController@showLogin');
$router->post('/login',   'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register','AuthController@register');
$router->get('/logout',   'AuthController@logout');

// ── Dashboard ─────────────────────────────────────────────────
$router->get('/',          'DashboardController@index');
$router->get('/dashboard', 'DashboardController@index');

// ── Profile ───────────────────────────────────────────────────
$router->get('/profile',                  'ProfileController@show');
$router->get('/profile/edit',             'ProfileController@showEdit');
$router->post('/profile/update',          'ProfileController@update');
$router->get('/profile/change-password',  'ProfileController@showChangePassword');
$router->post('/profile/change-password', 'ProfileController@changePassword');
$router->get('/profile/delete',           'ProfileController@showDelete');
$router->post('/profile/delete',          'ProfileController@delete');
$router->post('/profile/deactivate',      'ProfileController@deactivate');
$router->get('/users',                    'ProfileController@list');

// ── Expenses ──────────────────────────────────────────────────
$router->get('/add',              'ExpenseController@showAdd');
$router->post('/expense/save',    'ExpenseController@add');
$router->get('/expense/edit',     'ExpenseController@showEdit');
$router->post('/expense/update',  'ExpenseController@update');
$router->get('/expense/delete',   'ExpenseController@delete');

// ── Budget / User panel ───────────────────────────────────────
$router->get('/user',          'BudgetController@showPanel');
$router->post('/budget/save',  'BudgetController@save');
$router->get('/budget/delete', 'BudgetController@delete');

// ── Categories ────────────────────────────────────────────────
$router->get('/categories',        'CategoryController@index');
$router->get('/categories/add',    'CategoryController@showAdd');
$router->post('/categories/add',   'CategoryController@add');
$router->get('/categories/edit',   'CategoryController@showEdit');
$router->post('/categories/update','CategoryController@update');
$router->get('/categories/delete', 'CategoryController@delete');

// Admin user management
$router->get('/admin/user/edit',       'ProfileController@adminShowEdit');
$router->post('/admin/user/update',    'ProfileController@adminUpdate');
$router->get('/admin/user/activate',   'ProfileController@adminActivate');
$router->get('/admin/user/deactivate', 'ProfileController@adminDeactivate');

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);


