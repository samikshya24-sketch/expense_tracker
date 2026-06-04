# Expense Tracker — XAMPP Setup Guide

## Quick Setup (5 Steps)

### 1. Copy files
Extract and place the `expense_tracker` folder in:
```
C:\xampp\htdocs\expense_tracker\
```

### 2. Start XAMPP
Open XAMPP Control Panel → Start **Apache** and **MySQL**

### 3. Create database
- Go to: http://localhost/phpmyadmin
- Click **New** → name it `expense_tracker` → collation: `utf8mb4_unicode_ci` → Create
- Select the database → click **SQL** tab
- Open `database/schema.sql`, copy all contents, paste and click **Go**

### 4. Enable mod_rewrite
- In XAMPP Control Panel → Apache → Config → httpd.conf
- Find: `#LoadModule rewrite_module` → remove the `#`
- Find: `AllowOverride None` (inside the htdocs Directory block) → change to `AllowOverride All`
- Restart Apache

### 5. Open the app
```
http://localhost/expense_tracker/public/
```

---

## Folder Structure
```
expense_tracker/
├── app/
│   ├── Core/           Database.php, Router.php, Auth.php
│   ├── Models/         User, Budget, Expense, Category
│   ├── Controllers/    Auth, Budget, Expense, Dashboard
│   └── Views/          auth/, dashboard/, expense/, budget/, layouts/
├── database/
│   └── schema.sql      ← Run this in phpMyAdmin first
├── public/
│   ├── index.php       ← Front controller
│   ├── .htaccess       ← URL rewriting
│   └── assets/         css/style.css, js/app.js
└── README.md
```

## If you still see 404
The `Router.php` has `basePath = '/expense_tracker/public'` which matches the default XAMPP subfolder path.
If your folder is named differently, edit that line in `app/Core/Router.php`.
"# expense_tracker" 
"# expense_tracker" 
