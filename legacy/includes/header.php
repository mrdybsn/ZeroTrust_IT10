<?php
// Usage: include this file after requireLogin() / requireAdmin()
// Pass $pageTitle before including.
$pageTitle = $pageTitle ?? 'ZERO TRUST';
$role      = $_SESSION['role'] ?? 'player';
$username  = $_SESSION['username'] ?? '';
$fullname  = $_SESSION['fullname'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?> — ZERO TRUST</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg:       #020c14;
    --panel:    rgba(4,15,26,0.97);
    --border:   #0ff;
    --accent:   #00ffe7;
    --accent2:  #ff2d6b;
    --accent3:  #ffe700;
    --glow:     0 0 8px #00ffe7, 0 0 20px #00ffe780;
    --text:     #c8f0ff;
    --muted:    #456070;
    --font-mono:'Share Tech Mono', monospace;
    --font-hud: 'Orbitron', monospace;
    --font-body:'Rajdhani', sans-serif;
    --sidebar:  240px;
}
body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--font-body);
    min-height: 100vh;
    display: flex;
}

/* Grid bg */
body::before {
    content: '';
    position: fixed; inset: 0; z-index: 0;
    background-image:
        linear-gradient(rgba(0,255,231,0.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,255,231,0.025) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
}

/* ---- SIDEBAR ---- */
.sidebar {
    width: var(--sidebar);
    min-height: 100vh;
    background: rgba(2,10,18,0.98);
    border-right: 1px solid rgba(0,255,231,0.15);
    display: flex; flex-direction: column;
    position: fixed; left: 0; top: 0; bottom: 0;
    z-index: 100;
    padding: 0;
}
.sidebar-logo {
    padding: 24px 20px 20px;
    border-bottom: 1px solid rgba(0,255,231,0.1);
    text-align: center;
}
.sidebar-logo .game-title {
    font-family: var(--font-hud);
    font-size: 18px;
    font-weight: 900;
    color: #fff;
    text-shadow: var(--glow);
    letter-spacing: 3px;
}
.sidebar-logo .game-sub {
    font-family: var(--font-mono);
    font-size: 9px;
    color: var(--muted);
    letter-spacing: 2px;
    margin-top: 4px;
}
.role-badge {
    display: inline-block;
    font-family: var(--font-mono);
    font-size: 9px;
    letter-spacing: 2px;
    padding: 3px 10px;
    margin-top: 8px;
    border: 1px solid;
    clip-path: polygon(6px 0%, 100% 0%, calc(100% - 6px) 100%, 0% 100%);
}
.role-badge.admin  { color: var(--accent3); border-color: var(--accent3); background: rgba(255,231,0,0.08); }
.role-badge.player { color: var(--accent);  border-color: var(--accent);  background: rgba(0,255,231,0.08); }

.sidebar-user {
    padding: 16px 20px;
    border-bottom: 1px solid rgba(0,255,231,0.08);
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--muted);
}
.sidebar-user strong {
    display: block;
    color: var(--text);
    font-size: 13px;
    margin-bottom: 2px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.sidebar-nav { flex: 1; padding: 16px 0; }
.nav-section {
    font-family: var(--font-mono);
    font-size: 9px;
    color: var(--muted);
    letter-spacing: 3px;
    padding: 0 20px 8px;
    margin-top: 16px;
}
.nav-item {
    display: flex; align-items: center; gap: 12px;
    padding: 11px 20px;
    color: var(--muted);
    text-decoration: none;
    font-family: var(--font-body);
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 1px;
    transition: all .25s;
    position: relative;
    border-left: 2px solid transparent;
}
.nav-item:hover, .nav-item.active {
    color: var(--accent);
    border-left-color: var(--accent);
    background: rgba(0,255,231,0.05);
}
.nav-item .nav-icon { font-size: 16px; width: 20px; text-align: center; }

.sidebar-footer {
    padding: 16px 20px;
    border-top: 1px solid rgba(0,255,231,0.08);
}
.btn-logout {
    display: flex; align-items: center; gap: 10px;
    width: 100%;
    padding: 10px 14px;
    background: transparent;
    border: 1px solid rgba(255,45,107,0.4);
    color: var(--accent2);
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 2px;
    cursor: pointer;
    text-decoration: none;
    transition: all .3s;
    clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));
}
.btn-logout:hover {
    background: rgba(255,45,107,0.12);
    border-color: var(--accent2);
    box-shadow: var(--glow-r);
}

/* ---- MAIN CONTENT ---- */
.main {
    margin-left: var(--sidebar);
    flex: 1;
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 1;
    min-height: 100vh;
}

/* Top bar */
.topbar {
    height: 56px;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 32px;
    border-bottom: 1px solid rgba(0,255,231,0.1);
    background: rgba(2,10,18,0.7);
    backdrop-filter: blur(8px);
    position: sticky; top: 0; z-index: 50;
}
.topbar-title {
    font-family: var(--font-hud);
    font-size: 13px;
    color: var(--accent);
    letter-spacing: 4px;
    text-transform: uppercase;
}
.topbar-right {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--muted);
    letter-spacing: 1px;
    display: flex; align-items: center; gap: 16px;
}
.status-dot {
    display: inline-block; width: 6px; height: 6px;
    border-radius: 50%; background: #0f0;
    box-shadow: 0 0 6px #0f0;
    animation: pulse 2s ease infinite;
}
@keyframes pulse { 50% { box-shadow: 0 0 14px #0f0; } }

/* ---- CONTENT WRAPPER ---- */
.content { padding: 32px; flex: 1; }

/* ---- CARDS / PANELS ---- */
.card {
    background: var(--panel);
    border: 1px solid rgba(0,255,231,0.2);
    padding: 24px 28px;
    position: relative;
    margin-bottom: 24px;
    clip-path: polygon(0 0, calc(100% - 16px) 0, 100% 16px, 100% 100%, 16px 100%, 0 calc(100% - 16px));
}
.card-title {
    font-family: var(--font-hud);
    font-size: 12px;
    color: var(--accent);
    letter-spacing: 3px;
    margin-bottom: 20px;
    display: flex; align-items: center; gap: 10px;
}
.card-title::after {
    content: ''; flex: 1; height: 1px;
    background: linear-gradient(90deg, rgba(0,255,231,0.3), transparent);
}

/* Stat cards row */
.stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 28px; }
.stat-card {
    background: rgba(4,15,26,0.9);
    border: 1px solid rgba(0,255,231,0.15);
    padding: 20px;
    text-align: center;
    clip-path: polygon(0 0, calc(100% - 12px) 0, 100% 12px, 100% 100%, 12px 100%, 0 calc(100% - 12px));
    transition: border-color .3s, box-shadow .3s;
}
.stat-card:hover { border-color: var(--accent); box-shadow: var(--glow); }
.stat-num {
    font-family: var(--font-hud);
    font-size: 32px;
    font-weight: 700;
    color: var(--accent);
    text-shadow: var(--glow);
    line-height: 1;
}
.stat-label {
    font-family: var(--font-mono);
    font-size: 9px;
    color: var(--muted);
    letter-spacing: 2px;
    margin-top: 6px;
}

/* Table */
.data-table { width: 100%; border-collapse: collapse; font-family: var(--font-body); font-size: 14px; }
.data-table thead tr {
    border-bottom: 1px solid rgba(0,255,231,0.3);
}
.data-table th {
    font-family: var(--font-mono);
    font-size: 9px;
    letter-spacing: 3px;
    color: var(--accent);
    padding: 10px 14px;
    text-align: left;
    text-transform: uppercase;
}
.data-table td {
    padding: 12px 14px;
    border-bottom: 1px solid rgba(0,255,231,0.06);
    color: var(--text);
    font-size: 13px;
}
.data-table tbody tr:hover { background: rgba(0,255,231,0.03); }
.data-table .mono { font-family: var(--font-mono); font-size: 12px; }

/* Badges */
.badge {
    display: inline-block;
    font-family: var(--font-mono);
    font-size: 9px;
    letter-spacing: 2px;
    padding: 3px 10px;
    border: 1px solid;
    clip-path: polygon(4px 0%, 100% 0%, calc(100% - 4px) 100%, 0% 100%);
}
.badge-admin  { color: var(--accent3); border-color: var(--accent3); background: rgba(255,231,0,0.07); }
.badge-player { color: var(--accent);  border-color: var(--accent);  background: rgba(0,255,231,0.06); }
.badge-active   { color: #0f0; border-color: #0f0; background: rgba(0,255,0,0.06); }
.badge-inactive { color: var(--accent2); border-color: var(--accent2); background: rgba(255,45,107,0.07); }

/* Buttons */
.btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px;
    font-family: var(--font-mono);
    font-size: 10px;
    letter-spacing: 2px;
    border: 1px solid;
    cursor: pointer;
    background: transparent;
    text-decoration: none;
    transition: all .3s;
    clip-path: polygon(6px 0%, 100% 0%, calc(100% - 6px) 100%, 0% 100%);
}
.btn-primary { color: var(--accent);  border-color: var(--accent); }
.btn-primary:hover { background: rgba(0,255,231,0.12); box-shadow: var(--glow); }
.btn-danger  { color: var(--accent2); border-color: var(--accent2); }
.btn-danger:hover  { background: rgba(255,45,107,0.12); }
.btn-warning { color: var(--accent3); border-color: var(--accent3); }
.btn-warning:hover { background: rgba(255,231,0,0.10); }
.btn-sm { padding: 5px 10px; font-size: 9px; }

/* Form controls */
.form-group { margin-bottom: 20px; }
.form-label {
    display: block;
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--accent);
    letter-spacing: 3px;
    margin-bottom: 7px;
}
.form-control {
    width: 100%;
    background: rgba(0,255,231,0.03);
    border: 1px solid var(--muted);
    color: var(--text);
    font-family: var(--font-mono);
    font-size: 13px;
    padding: 10px 14px;
    outline: none;
    transition: border-color .3s, box-shadow .3s;
}
.form-control:focus { border-color: var(--accent); box-shadow: var(--glow); }
select.form-control { cursor: pointer; }
select.form-control option { background: #040f1a; }

/* Alert flash */
.flash { font-family: var(--font-mono); font-size: 11px; padding: 10px 16px; margin-bottom: 20px; border-left: 3px solid; letter-spacing: 1px; }
.flash-success { color: var(--accent);  border-color: var(--accent);  background: rgba(0,255,231,0.07); }
.flash-error   { color: var(--accent2); border-color: var(--accent2); background: rgba(255,45,107,0.07); }

/* Modal */
.modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 999;
    background: rgba(0,0,0,0.8); backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
}
.modal-overlay.open { display: flex; }
.modal {
    background: #040f1a;
    border: 1px solid var(--accent);
    box-shadow: var(--glow);
    padding: 32px 36px;
    width: 460px; max-width: 95vw;
    clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 20px, 100% 100%, 20px 100%, 0 calc(100% - 20px));
    animation: slideIn .3s ease;
}
@keyframes slideIn { from { transform:translateY(-16px); opacity:0; } to { transform:none; opacity:1; } }
.modal-title {
    font-family: var(--font-hud);
    font-size: 13px;
    color: var(--accent);
    letter-spacing: 4px;
    margin-bottom: 24px;
    display: flex; align-items: center; gap: 10px;
}
.modal-title::after {
    content: ''; flex: 1; height: 1px;
    background: linear-gradient(90deg, rgba(0,255,231,0.3), transparent);
}
.modal-actions { display: flex; gap: 12px; margin-top: 24px; justify-content: flex-end; }

/* Scrollbar */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(0,255,231,0.3); border-radius: 3px; }

/* Responsive */
@media (max-width: 768px) {
    .sidebar { width: 60px; }
    .sidebar-logo .game-title,
    .sidebar-logo .game-sub,
    .sidebar-user, .role-badge,
    .nav-item span, .nav-section,
    .sidebar-footer .btn-logout span { display: none; }
    .nav-item { justify-content: center; padding: 14px; }
    .main { margin-left: 60px; }
    .topbar { padding: 0 16px; }
    .content { padding: 16px; }
}
</style>

<div class="sidebar">
    <div class="sidebar-logo">
        <div class="game-title">ZERO TRUST</div>
        <div class="game-sub">CYBERSECURITY GAME</div>
        <div class="role-badge <?= $role ?>"><?= strtoupper($role) ?></div>
    </div>
    <div class="sidebar-user">
        <strong><?= htmlspecialchars($fullname) ?></strong>
        @<?= htmlspecialchars($username) ?>
    </div>

    <nav class="sidebar-nav">
        <?php if ($role === 'admin'): ?>
        <div class="nav-section">// ADMIN CONTROL</div>
        <a href="/zero_trust/admin/dashboard.php"
           class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '') ?>">
            <span class="nav-icon">◈</span><span>Dashboard</span>
        </a>
        <a href="/zero_trust/admin/users.php"
           class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '') ?>">
            <span class="nav-icon">◉</span><span>User Management</span>
        </a>
        <a href="/zero_trust/admin/logs.php"
           class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'logs.php' ? 'active' : '') ?>">
            <span class="nav-icon">▣</span><span>Activity Logs</span>
        </a>
        <?php else: ?>
        <div class="nav-section">// PLAYER HQ</div>
        <a href="/zero_trust/player/dashboard.php"
           class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '') ?>">
            <span class="nav-icon">◈</span><span>Dashboard</span>
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="/zero_trust/logout.php" class="btn-logout">
            <span>⏻</span><span>LOGOUT</span>
        </a>
    </div>
</div>

<div class="main">
    <div class="topbar">
        <div class="topbar-title"><?= htmlspecialchars($pageTitle) ?></div>
        <div class="topbar-right">
            <span><span class="status-dot"></span> SECURE SESSION</span>
            <span id="clock"></span>
        </div>
    </div>
    <div class="content">
