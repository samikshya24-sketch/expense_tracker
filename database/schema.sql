-- ============================================================
--  Expense Tracker — Full Schema + Seed Data
--  Run this in phpMyAdmin > SQL tab
-- ============================================================

CREATE DATABASE IF NOT EXISTS expense_tracker
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE expense_tracker;

-- ── USERS (Member 1) ──────────────────────────────────────────
CREATE TABLE users (
  id         INT           NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100)  NOT NULL,
  email      VARCHAR(150)  NOT NULL UNIQUE,
  password   VARCHAR(255)  NOT NULL,          -- bcrypt hash
  is_active  TINYINT(1)    NOT NULL DEFAULT 1,
  created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

-- ── CATEGORIES (Member 3 — seeded, not user-created) ──────────
CREATE TABLE categories (
  id    INT          NOT NULL AUTO_INCREMENT,
  name  VARCHAR(50)  NOT NULL,
  icon  VARCHAR(10)  NOT NULL,
  color VARCHAR(7)   NOT NULL DEFAULT '#6554C0',
  PRIMARY KEY (id)
);

INSERT INTO categories (name, icon, color) VALUES
  ('Food',          '🍔', '#FF8B00'),
  ('Transport',     '🚌', '#0052CC'),
  ('Entertainment', '🎬', '#6554C0'),
  ('Shopping',      '🛍️', '#FF5630'),
  ('Health',        '💊', '#36B37E'),
  ('Education',     '📚', '#00B8D9'),
  ('Utilities',     '💡', '#403294'),
  ('Other',         '📦', '#8B949E');

-- ── EXPENSES (Member 3) ───────────────────────────────────────
CREATE TABLE expenses (
  id          INT           NOT NULL AUTO_INCREMENT,
  user_id     INT           NOT NULL,
  category_id INT           NOT NULL,
  amount      DECIMAL(10,2) NOT NULL,
  note        VARCHAR(255)  NULL DEFAULT NULL,
  spent_date  DATE          NOT NULL,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- ── BUDGETS (Member 2) ────────────────────────────────────────
-- UNIQUE KEY ensures one budget per user per month
CREATE TABLE budgets (
  id         INT           NOT NULL AUTO_INCREMENT,
  user_id    INT           NOT NULL,
  amount     DECIMAL(10,2) NOT NULL,
  month_year VARCHAR(7)    NOT NULL,           -- Format: YYYY-MM e.g. 2026-05
  created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_budget (user_id, month_year),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
