-- Zero Trust: A Mobile-Based Interactive Cybersecurity Game
-- Database Setup File
-- IT 10 - Information Assurance and Security 1

CREATE DATABASE IF NOT EXISTS zero_trust_db;
USE zero_trust_db;

-- =============================================
-- USERS TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,         -- bcrypt hashed
    role ENUM('admin', 'player') NOT NULL DEFAULT 'player',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================================
-- LOGS TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    activity VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- SEED: Default Admin Account
-- Password: Admin@1234  (bcrypt hash)
-- =============================================
INSERT INTO users (fullname, username, password, role) VALUES
('System Administrator', 'admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =============================================
-- SEED: Sample Player Accounts
-- Password for all: Player@123
-- =============================================
INSERT INTO users (fullname, username, password, role) VALUES
('Mardy Besana',     'mardy',   '$2y$12$u4lp6mcFkA2B/vD1H.w5fOOkDJvFUZJfHqZpJtHQnP0D.FjfIqiXG', 'player'),
('John Caminoy',     'john',    '$2y$12$u4lp6mcFkA2B/vD1H.w5fOOkDJvFUZJfHqZpJtHQnP0D.FjfIqiXG', 'player'),
('Hezelie Diwa',     'hezelie', '$2y$12$u4lp6mcFkA2B/vD1H.w5fOOkDJvFUZJfHqZpJtHQnP0D.FjfIqiXG', 'player'),
('Franzine Eclar',   'franzine','$2y$12$u4lp6mcFkA2B/vD1H.w5fOOkDJvFUZJfHqZpJtHQnP0D.FjfIqiXG', 'player'),
('Gycel Ucag',       'gycel',   '$2y$12$u4lp6mcFkA2B/vD1H.w5fOOkDJvFUZJfHqZpJtHQnP0D.FjfIqiXG', 'player');

-- =============================================
-- SEED: Sample Activity Logs
-- =============================================
INSERT INTO logs (user_id, activity, ip_address) VALUES
(1, 'Admin account initialized', '127.0.0.1'),
(2, 'Player account created', '127.0.0.1');
