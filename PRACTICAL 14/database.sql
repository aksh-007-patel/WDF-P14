-- Practical 14: Admin Dashboard with RBAC, Dynamic Menu, Audit Log
-- Database: practical14_db
-- Import: via phpMyAdmin OR mysql -u root < database.sql

CREATE DATABASE IF NOT EXISTS practical14_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE practical14_db;
SET FOREIGN_KEY_CHECKS=0;

-- ================= USERS =================
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','staff','student') NOT NULL DEFAULT 'student',
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================= STUDENTS =================
CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  course VARCHAR(100) NOT NULL DEFAULT 'BCA',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ================= EVENTS =================
CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  event_date DATE NOT NULL,
  location VARCHAR(150) NOT NULL DEFAULT 'Main Hall',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================= REGISTRATIONS =================
CREATE TABLE IF NOT EXISTS registrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  event_id INT NOT NULL,
  registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  UNIQUE KEY uq_reg (student_id, event_id)
);

-- ================= AUDIT LOGS =================
CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(100) NOT NULL,
  details TEXT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_action (action),
  INDEX idx_created (created_at)
);

-- ================= DYNAMIC MENUS =================
CREATE TABLE IF NOT EXISTS menus (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(80) NOT NULL,
  url VARCHAR(255) NOT NULL,
  icon VARCHAR(80) NOT NULL DEFAULT 'bi-circle',
  allowed_roles VARCHAR(100) NOT NULL DEFAULT 'admin',
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
);

-- ============ SEED: MENUS (dynamic, role-based) ============
TRUNCATE TABLE menus;
INSERT INTO menus (label, url, icon, allowed_roles, sort_order) VALUES
('Dashboard',    '/PRACTICAL 14/admin/dashboard.php',     'bi-speedometer2', 'admin,staff',          1),
('Users',        '/PRACTICAL 14/admin/users.php',         'bi-people',       'admin',                2),
('Students',     '/PRACTICAL 14/admin/students.php',     'bi-mortarboard',  'admin,staff',          3),
('Events',       '/PRACTICAL 14/admin/events.php',       'bi-calendar-event','admin,staff',         4),
('Registrations','/PRACTICAL 14/admin/registrations.php','bi-card-checklist','admin,staff',         5),
('Audit Logs',   '/PRACTICAL 14/admin/audit_logs.php',   'bi-journal-text', 'admin',                6),
('My Profile',   '/PRACTICAL 14/admin/profile.php',      'bi-person-circle','admin,staff,student',  7);

-- ============ SEED: USERS (password_hash of xxx123) ============
-- admin@example.com / admin123 | staff@example.com / staff123 | student@example.com / student123
TRUNCATE TABLE audit_logs;
TRUNCATE TABLE registrations;
TRUNCATE TABLE students;
TRUNCATE TABLE events;
TRUNCATE TABLE users;

INSERT INTO users (name, email, password, role, status) VALUES
('Admin User',   'admin@example.com',   '$2y$10$wH8kQmXvQmXvQmXvQmXvOuJvJvJvJvJvJvJvJvJvJvJvJvJvJvJ.', 'admin',   'active'),
('Staff User',   'staff@example.com',   '$2y$10$wH8kQmXvQmXvQmXvQmXvOuJvJvJvJvJvJvJvJvJvJvJvJvJvJvJ.', 'staff',   'active'),
('Student User', 'student@example.com', '$2y$10$wH8kQmXvQmXvQmXvQmXvOuJvJvJvJvJvJvJvJvJvJvJvJvJvJvJ.', 'student', 'active');

-- NOTE: hashes above are placeholders. Run setup.php once to fix them automatically.
-- Alternatively use plain SQL below with known hashes for 'admin123','staff123','student123':
-- generated via PHP password_hash(). setup.php regenerates them on first run.

-- ============ SEED: STUDENTS ============
INSERT INTO students (name, email, course) VALUES
('Aarav Patel','aarav@example.com','BCA'),
('Diya Shah','diya@example.com','BCA'),
('Kabir Mehta','kabir@example.com','MSc IT'),
('Ananya Rao','ananya@example.com','BCA'),
('Vivaan Joshi','vivaan@example.com','BBA'),
('Ishita Nair','ishita@example.com','MSc IT');

-- ============ SEED: EVENTS ============
INSERT INTO events (title, event_date, location) VALUES
('Tech Fest 2026',      DATE_SUB(CURDATE(), INTERVAL 5 MONTH), 'Main Hall'),
('Hackathon',           DATE_SUB(CURDATE(), INTERVAL 4 MONTH), 'Lab Block'),
('Cultural Day',        DATE_SUB(CURDATE(), INTERVAL 3 MONTH), 'Auditorium'),
('Workshop on AI',      DATE_SUB(CURDATE(), INTERVAL 2 MONTH), 'Seminar Hall'),
('Sports Meet',         DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 'Ground'),
('Annual Day',          CURDATE(),                              'Auditorium');

-- ============ SEED: REGISTRATIONS (spread across months for chart) ============
INSERT INTO registrations (student_id, event_id, registered_at) VALUES
(1,1, DATE_SUB(NOW(), INTERVAL 5 MONTH)),
(2,1, DATE_SUB(NOW(), INTERVAL 5 MONTH)),
(3,1, DATE_SUB(NOW(), INTERVAL 5 MONTH)),
(1,2, DATE_SUB(NOW(), INTERVAL 4 MONTH)),
(4,2, DATE_SUB(NOW(), INTERVAL 4 MONTH)),
(2,3, DATE_SUB(NOW(), INTERVAL 3 MONTH)),
(5,3, DATE_SUB(NOW(), INTERVAL 3 MONTH)),
(6,3, DATE_SUB(NOW(), INTERVAL 3 MONTH)),
(3,4, DATE_SUB(NOW(), INTERVAL 2 MONTH)),
(4,4, DATE_SUB(NOW(), INTERVAL 2 MONTH)),
(1,5, DATE_SUB(NOW(), INTERVAL 1 MONTH)),
(2,5, DATE_SUB(NOW(), INTERVAL 1 MONTH)),
(3,5, DATE_SUB(NOW(), INTERVAL 1 MONTH)),
(5,6, NOW()),
(6,6, NOW()),
(1,6, NOW()),
(2,6, NOW());

SET FOREIGN_KEY_CHECKS=1;
