-- Zero Trust — Laravel Secure Login & User Management
-- IT 10 - Information Assurance and Security 1
-- Import this file in phpMyAdmin OR run: php artisan migrate --seed

CREATE DATABASE IF NOT EXISTS zero_trust_db;
USE zero_trust_db;

-- Run Laravel migrations for full schema (users, logs, sessions, cache, jobs).
-- This file documents the core tables used by the capstone module.

-- USERS (see database/migrations/0001_01_01_000000_create_users_table.php)
-- LOGS  (see database/migrations/2026_05_24_000001_create_logs_table.php)

-- Default credentials after seeding (php artisan db:seed):
-- Admin:  admin / Admin@1234
-- Player: mardy / Player@123  (also john, hezelie, franzine, gycel)
