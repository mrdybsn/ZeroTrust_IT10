# ZERO TRUST — Secure Login & User Management Module
### IT 10 - Information Assurance and Security 1 | Filamer Christian University

---

## 📁 FILE STRUCTURE

```
zero_trust/
├── index.php                  ← Login page (game UI)
├── logout.php                 ← Logout handler
├── database.sql               ← MySQL database setup
├── includes/
│   ├── db.php                 ← Database connection (PDO-free MySQLi)
│   ├── auth.php               ← Login, logout, session guards, logging
│   ├── header.php             ← Shared game HUD (sidebar + topbar)
│   └── footer.php             ← Shared footer + JS
├── admin/
│   ├── dashboard.php          ← Admin overview (stats + recent logs)
│   ├── users.php              ← Full CRUD: add/edit/delete/view users
│   └── logs.php               ← Activity log viewer (200 entries)
└── player/
    └── dashboard.php          ← Player HQ (welcome + own log)
```

---

## ⚙️ SETUP INSTRUCTIONS (XAMPP)

1. Copy the `zero_trust/` folder to `C:/xampp/htdocs/`
2. Open phpMyAdmin → run `database.sql` to create the DB and seed data
3. Edit `includes/db.php` and set your MySQL username/password if needed
4. Visit: `http://localhost/zero_trust/`

---

## 🔑 DEFAULT CREDENTIALS

| Role   | Username | Password    |
|--------|----------|-------------|
| Admin  | admin    | Admin@1234  |
| Player | mardy    | Player@123  |
| Player | john     | Player@123  |
| Player | hezelie  | Player@123  |
| Player | franzine | Player@123  |
| Player | gycel    | Player@123  |

> **IMPORTANT:** Change all passwords after setup.

---

## 🛡️ SECURITY FEATURES IMPLEMENTED

### Against SQL Injection
- All database queries use **MySQLi Prepared Statements** with `bind_param()`
- No raw string concatenation used in any SQL query
- Example test: entering `' OR '1'='1` in the username field will NOT bypass login

### Password Security
- Passwords hashed with **bcrypt (PASSWORD_BCRYPT)** at cost factor 12
- `password_hash()` and `password_verify()` used — no plain-text passwords stored
- Minimum 8-character password enforced on registration

### Session Security
- `session_regenerate_id(true)` called on successful login (prevents session fixation)
- Role-based guards: `requireLogin()` and `requireAdmin()` functions
- Sessions destroyed completely on logout

### CSRF Protection
- CSRF token generated with `bin2hex(random_bytes(32))` on each form
- Token validated server-side before processing any POST request

### Input Validation
- All output escaped with `htmlspecialchars()` via `e()` helper (prevents XSS)
- Role and status fields validated against whitelists
- Username/password length limits enforced

### CIA Triad Application
- **Confidentiality**: Passwords hashed, no sensitive data exposed in responses
- **Integrity**: Input validation, prepared statements, CSRF protection
- **Availability**: Invalid input handled gracefully; system stays operational

### AAA Framework
- **Authentication**: bcrypt password verification on every login
- **Authorization**: Role-based access control (Admin vs Player)
- **Accounting**: All login/logout/CRUD actions recorded in `logs` table with timestamp + IP

---

## 🎯 DEMONSTRATION CHECKLIST (for IT 10 Demo)

- [x] Valid login → redirects to correct dashboard by role
- [x] Invalid login → error message, no bypass
- [x] SQL Injection test (`' OR '1'='1`) → rejected
- [x] Admin access → User Management CRUD + Logs
- [x] Player access → restricted to own dashboard only
- [x] Password hashing → check DB, passwords show as bcrypt hash
- [x] Activity logs → every action is recorded

---

## 📊 GRADING CRITERIA COMPLIANCE

| Criteria                | Implementation                                      |
|------------------------|-----------------------------------------------------|
| Functionality (25%)    | Login, logout, session, CRUD, role redirect         |
| Security (35%)         | Prepared stmts, bcrypt, CSRF, session regen, XSS    |
| UI/UX (10%)            | Cyberpunk/game-themed HUD interface                 |
| Database Design (10%)  | Normalized users + logs tables, FK constraint       |
| Documentation (10%)    | This README + inline code comments                  |
| Demonstration (10%)    | All demo items checkable above                      |

---

*Zero Trust Capstone Group — Filamer Christian University, Inc. | 2026*
