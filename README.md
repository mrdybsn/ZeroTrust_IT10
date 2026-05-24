# ZERO TRUST — Secure Login & User Management (Laravel)

**IT 10 - Information Assurance and Security 1** | Filamer Christian University

Laravel 12 implementation of the secure Login and User Management modules for the Zero Trust capstone project.

---

## File structure

```
zero_trust/
├── app/
│   ├── Http/Controllers/Auth/     # Login, logout, 2FA, password reset
│   ├── Http/Controllers/Admin/    # Dashboard, users CRUD, logs
│   ├── Http/Controllers/Player/   # Player dashboard
│   ├── Http/Middleware/           # EnsureAdmin (authorization)
│   ├── Models/                    # User, ActivityLog
│   └── Services/                  # ActivityLogger, Recaptcha, TwoFactor
├── database/migrations/           # users, logs, sessions
├── database/seeders/              # Default admin + players
├── resources/views/               # Blade templates (cyberpunk UI)
├── public/css/zero-trust.css
├── legacy/                        # Original plain PHP version (reference)
├── database.sql
└── index.php                      # XAMPP entry → public/
```

---

## Setup (XAMPP)

1. Ensure **Apache** and **MySQL** are running in XAMPP.
2. Create database (or let migrations create tables):
   ```sql
   CREATE DATABASE zero_trust_db;
   ```
3. Copy `.env.example` to `.env` if needed and set:
   ```
   DB_DATABASE=zero_trust_db
   DB_USERNAME=root
   DB_PASSWORD=
   APP_URL=http://localhost/zero_trust/public
   ```
4. From project folder:
   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```
5. Open: **http://localhost/zero_trust/public**  
   Or: **http://localhost/zero_trust/** (uses root `index.php`)

---

## Default credentials

| Role   | Username | Password    |
|--------|----------|-------------|
| Admin  | admin    | Admin@1234  |
| Player | mardy    | Player@123  |
| Player | john, hezelie, franzine, gycel | Player@123 |

---

## Security features (required)

| Requirement | Implementation |
|-------------|----------------|
| Hashed passwords | Laravel `bcrypt` via `password` cast on User model |
| SQL injection prevention | Eloquent ORM + query builder (parameterized) |
| Input validation | Form Request validation on all POST routes |
| Session management | Laravel sessions + `regenerate()` on login |
| Role authorization | `EnsureAdmin` middleware |
| Activity logging | `ActivityLogger` → `logs` table with IP |
| Invalid login handling | Generic error message, failed attempt logged |

**SQL injection test:** Enter `' OR '1'='1` as username — login will **not** bypass.

---

## CIA & AAA

- **Confidentiality:** Passwords hashed; secrets hidden from JSON
- **Integrity:** Validation rules; CSRF on all forms
- **Availability:** Graceful errors; rate limiting does not crash app
- **Authentication:** Username + password + Google reCAPTCHA v2
- **Authorization:** Admin-only routes for user CRUD and logs
- **Accounting:** Login, logout, CRUD, and failed attempts logged

---

## Bonus features

| Feature | Status |
|---------|--------|
| Google reCAPTCHA v2 | Login form (“I’m not a robot”) |
| Login attempt limiting | RateLimiter (5 per IP+username) |
| Account lockout | 5 failed attempts → 15 min lock |
| Password strength meter | Login + add user forms |
| Admin activity dashboard | Stat cards + 7-day charts |
| Two-factor authentication | Optional per user (6-digit code) |
| Password reset via email | `/forgot-password` (configure `MAIL_*` in `.env`) |
| HTTPS | Use XAMPP SSL or deploy behind HTTPS in production |

**reCAPTCHA:** Keys are in `.env` (`RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`). For demos on localhost, the included Google **test keys** work out of the box. For production, register your domain at [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin) and replace the keys.

**2FA demo:** Enable 2FA on a user in Admin → Edit User. With `APP_DEBUG=true`, the code is shown on the 2FA screen.

**Password reset via email:** See [Password reset setup](#password-reset-via-email-bonus) below.

---

## Demonstration checklist

- [ ] Valid login (admin → admin dashboard, player → player HQ)
- [ ] Invalid login (wrong password)
- [ ] SQL injection test `' OR '1'='1`
- [ ] Admin: add / edit / delete / view users
- [ ] Player: cannot access `/admin/*`
- [ ] Passwords hashed in DB (`users.password` starts with `$2y$`)
- [ ] Activity logs visible at Admin → Activity Logs
- [ ] reCAPTCHA, lockout, 2FA (bonus)

---

## Password reset via email (bonus)

### How it works

1. User opens **Forgot password?** on the login page → `/forgot-password`
2. Enters their registered email (e.g. `admin@zerotrust.local`)
3. System sends a **Reset Password** email with a secure link (expires in 60 minutes)
4. User sets a new password on `/reset-password/{token}`
5. Action is recorded in **Activity Logs** (AAA accounting)

### Local testing (XAMPP, no SMTP)

In `.env`:

```env
MAIL_MAILER=log
APP_DEBUG=true
APP_URL=http://localhost/zero_trust/public
```

1. Go to `/forgot-password`
2. Enter `admin@zerotrust.local` or `{username}@zerotrust.local`
3. Submit — a **reset link appears on screen** (debug mode) and the full email is in `storage/logs/laravel.log`

### Real email (Gmail SMTP example)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@gmail.com
MAIL_FROM_NAME="Zero Trust"
```

Use a [Gmail App Password](https://support.google.com/accounts/answer/185833) (not your normal password).

### Demo account emails

| Username | Email |
|----------|-------|
| admin | admin@zerotrust.local |
| mardy | mardy@zerotrust.local |
| john | john@zerotrust.local |

All players use `{username}@zerotrust.local` from the database seeder.

---

## Submission

1. Source code (this repository)
2. `database.sql` + migrations
3. Screenshots documentation (add your own `docs/` folder)
4. Live demo using checklist above

---

*Zero Trust Capstone Group — Filamer Christian University | 2026*
