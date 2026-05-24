<?php
// ============================================================
// Zero Trust — Login Page
// ============================================================
require_once 'includes/auth.php';

// Redirect if already logged in
if (!empty($_SESSION['role'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'player/dashboard.php'));
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Username and password are required.';
        } else {
            $result = attemptLogin($username, $password);
            if ($result['success']) {
                header('Location: ' . ($result['role'] === 'admin' ? 'admin/dashboard.php' : 'player/dashboard.php'));
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$msg = $_GET['msg'] ?? '';
if ($msg === 'session_expired') $success = 'Session expired. Please log in again.';
if ($msg === 'logged_out')      $success = 'You have been logged out successfully.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZERO TRUST — ACCESS TERMINAL</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --bg:        #020c14;
    --panel:     #040f1a;
    --border:    #0ff;
    --accent:    #00ffe7;
    --accent2:   #ff2d6b;
    --glow:      0 0 8px #00ffe7, 0 0 20px #00ffe780;
    --glow-r:    0 0 8px #ff2d6b, 0 0 20px #ff2d6b80;
    --text:      #c8f0ff;
    --muted:     #456070;
    --font-mono: 'Share Tech Mono', monospace;
    --font-hud:  'Orbitron', monospace;
    --font-body: 'Rajdhani', sans-serif;
}

body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--font-body);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

/* ---- ANIMATED GRID BACKGROUND ---- */
.grid-bg {
    position: fixed; inset: 0; z-index: 0;
    background-image:
        linear-gradient(rgba(0,255,231,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,255,231,0.04) 1px, transparent 1px);
    background-size: 40px 40px;
    animation: gridScroll 20s linear infinite;
}
@keyframes gridScroll { to { background-position: 0 40px; } }

.scanline {
    position: fixed; inset: 0; z-index: 1;
    background: repeating-linear-gradient(
        to bottom,
        transparent 0,
        transparent 3px,
        rgba(0,0,0,0.15) 3px,
        rgba(0,0,0,0.15) 4px
    );
    pointer-events: none;
}

/* Floating particles */
.particles { position: fixed; inset: 0; z-index: 0; overflow: hidden; }
.particle {
    position: absolute;
    width: 2px; height: 2px;
    background: var(--accent);
    border-radius: 50%;
    animation: float linear infinite;
    opacity: 0;
}
@keyframes float {
    0%   { transform: translateY(100vh) translateX(0); opacity: 0; }
    10%  { opacity: 1; }
    90%  { opacity: 0.6; }
    100% { transform: translateY(-10vh) translateX(var(--dx, 30px)); opacity: 0; }
}

/* ---- CORNER DECORATIONS ---- */
.corner {
    position: fixed; width: 60px; height: 60px;
    border-color: var(--accent); border-style: solid;
    opacity: 0.5;
}
.corner-tl { top: 20px; left: 20px; border-width: 2px 0 0 2px; }
.corner-tr { top: 20px; right: 20px; border-width: 2px 2px 0 0; }
.corner-bl { bottom: 20px; left: 20px; border-width: 0 0 2px 2px; }
.corner-br { bottom: 20px; right: 20px; border-width: 0 2px 2px 0; }

/* ---- MAIN CONTAINER ---- */
.container {
    position: relative; z-index: 10;
    display: flex; flex-direction: column; align-items: center;
    gap: 0;
    animation: fadeIn .8s ease forwards;
}
@keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

/* ---- LOGO / TITLE AREA ---- */
.logo-area {
    text-align: center; margin-bottom: 8px;
}
.logo-subtitle {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--accent);
    letter-spacing: 6px;
    text-transform: uppercase;
    opacity: 0.7;
    margin-bottom: 6px;
}
.logo-title {
    font-family: var(--font-hud);
    font-size: clamp(36px, 6vw, 64px);
    font-weight: 900;
    color: #fff;
    text-shadow: var(--glow), 0 0 40px #00ffe7;
    letter-spacing: 8px;
    line-height: 1;
    animation: flicker 6s infinite;
}
@keyframes flicker {
    0%,95%,100% { opacity:1; }
    96% { opacity:.6; }
    97% { opacity:1; }
    98% { opacity:.4; }
    99% { opacity:1; }
}
.logo-tagline {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--accent2);
    letter-spacing: 3px;
    margin-top: 4px;
    animation: blink 1.5s step-end infinite;
}
@keyframes blink { 50% { opacity:0; } }

/* ---- LOGIN PANEL ---- */
.panel {
    background: linear-gradient(135deg, rgba(4,15,26,0.97) 0%, rgba(2,18,30,0.97) 100%);
    border: 1px solid var(--accent);
    box-shadow: var(--glow), inset 0 0 40px rgba(0,255,231,0.04);
    width: 420px;
    padding: 36px 40px 32px;
    position: relative;
    clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 20px, 100% 100%, 20px 100%, 0 calc(100% - 20px));
}

/* Panel top bar */
.panel::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, transparent, var(--accent), var(--accent2), var(--accent), transparent);
    animation: scanBar 3s ease-in-out infinite;
}
@keyframes scanBar {
    0%,100% { opacity:.4; }
    50% { opacity:1; }
}

.panel-header {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--muted);
    letter-spacing: 3px;
    margin-bottom: 28px;
    display: flex; align-items: center; gap: 10px;
}
.panel-header::before, .panel-header::after {
    content: ''; flex: 1; height: 1px;
    background: linear-gradient(90deg, transparent, var(--muted));
}
.panel-header::after { background: linear-gradient(90deg, var(--muted), transparent); }

/* ---- ALERTS ---- */
.alert {
    font-family: var(--font-mono);
    font-size: 12px;
    padding: 10px 14px;
    margin-bottom: 20px;
    border-left: 3px solid;
    letter-spacing: 1px;
    animation: slideIn .3s ease;
}
@keyframes slideIn { from { transform:translateX(-10px); opacity:0; } to { transform:none; opacity:1; } }
.alert-error   { color: var(--accent2); border-color: var(--accent2); background: rgba(255,45,107,0.08); }
.alert-success { color: var(--accent);  border-color: var(--accent);  background: rgba(0,255,231,0.06); }
.alert-icon    { margin-right: 6px; }

/* ---- FORM ---- */
.field { margin-bottom: 22px; }

.field-label {
    display: block;
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--accent);
    letter-spacing: 3px;
    margin-bottom: 8px;
    text-transform: uppercase;
}

.field-wrap {
    position: relative;
    display: flex; align-items: center;
}
.field-icon {
    position: absolute; left: 14px;
    color: var(--muted); font-size: 14px;
    pointer-events: none;
    transition: color .3s;
}
.field-wrap:focus-within .field-icon { color: var(--accent); }

input[type="text"], input[type="password"] {
    width: 100%;
    background: rgba(0,255,231,0.03);
    border: 1px solid var(--muted);
    color: var(--text);
    font-family: var(--font-mono);
    font-size: 14px;
    padding: 12px 14px 12px 40px;
    outline: none;
    transition: border-color .3s, box-shadow .3s;
    clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));
}
input:focus {
    border-color: var(--accent);
    box-shadow: var(--glow);
    background: rgba(0,255,231,0.06);
}
input::placeholder { color: var(--muted); }

.toggle-pw {
    position: absolute; right: 12px;
    background: none; border: none;
    color: var(--muted); cursor: pointer; font-size: 16px;
    transition: color .3s;
}
.toggle-pw:hover { color: var(--accent); }

/* ---- SUBMIT BUTTON ---- */
.btn-login {
    width: 100%;
    background: transparent;
    border: 2px solid var(--accent);
    color: var(--accent);
    font-family: var(--font-hud);
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 5px;
    padding: 14px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: color .3s, box-shadow .3s;
    clip-path: polygon(0 0, calc(100% - 12px) 0, 100% 12px, 100% 100%, 12px 100%, 0 calc(100% - 12px));
    margin-top: 8px;
    text-transform: uppercase;
}
.btn-login::before {
    content: '';
    position: absolute; inset: 0;
    background: var(--accent);
    transform: translateX(-101%);
    transition: transform .4s cubic-bezier(.4,0,.2,1);
    z-index: -1;
}
.btn-login:hover {
    color: var(--bg);
    box-shadow: var(--glow);
}
.btn-login:hover::before { transform: translateX(0); }
.btn-login:active { transform: scale(.98); }

/* Loading state */
.btn-login.loading {
    pointer-events: none;
    opacity: .7;
}

/* ---- STATUS BAR ---- */
.status-bar {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 24px;
    padding-top: 16px;
    border-top: 1px solid rgba(0,255,231,0.1);
    font-family: var(--font-mono);
    font-size: 9px;
    color: var(--muted);
    letter-spacing: 2px;
}
.status-dot {
    display: inline-block; width: 6px; height: 6px;
    border-radius: 50%; background: #0f0;
    box-shadow: 0 0 6px #0f0;
    animation: pulse 2s ease infinite;
    margin-right: 5px;
}
@keyframes pulse { 50% { box-shadow: 0 0 14px #0f0; } }

/* ---- VERSION TAG ---- */
.version-tag {
    font-family: var(--font-mono);
    font-size: 9px;
    color: rgba(0,255,231,0.25);
    margin-top: 16px;
    letter-spacing: 2px;
    text-align: center;
}

/* ---- RESPONSIVE ---- */
@media (max-width: 480px) {
    .panel { width: 95vw; padding: 28px 24px; }
    .logo-title { font-size: 40px; }
}
</style>
</head>
<body>

<div class="grid-bg"></div>
<div class="scanline"></div>
<div class="particles" id="particles"></div>
<div class="corner corner-tl"></div>
<div class="corner corner-tr"></div>
<div class="corner corner-bl"></div>
<div class="corner corner-br"></div>

<div class="container">

    <!-- Logo -->
    <div class="logo-area">
        <div class="logo-subtitle">FCU — IT 10 Capstone Project</div>
        <div class="logo-title">ZERO TRUST</div>
        <div class="logo-tagline">▶ AUTHENTICATE TO BEGIN ◀</div>
    </div>

    <!-- Login Panel -->
    <div class="panel">
        <div class="panel-header">ACCESS TERMINAL</div>

        <?php if ($error): ?>
        <div class="alert alert-error">
            <span class="alert-icon">⚠</span> <?= e($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success">
            <span class="alert-icon">✔</span> <?= e($success) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" id="loginForm" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

            <div class="field">
                <label class="field-label" for="username">// AGENT ID</label>
                <div class="field-wrap">
                    <span class="field-icon">◈</span>
                    <input type="text" id="username" name="username"
                           placeholder="Enter your username"
                           maxlength="50"
                           value="<?= e($_POST['username'] ?? '') ?>"
                           required autofocus>
                </div>
            </div>

            <div class="field">
                <label class="field-label" for="password">// ACCESS KEY</label>
                <div class="field-wrap">
                    <span class="field-icon">◉</span>
                    <input type="password" id="password" name="password"
                           placeholder="Enter your password"
                           maxlength="128"
                           required>
                    <button type="button" class="toggle-pw" id="togglePw" title="Toggle visibility">👁</button>
                </div>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                ▶ INITIATE ACCESS
            </button>
        </form>

        <div class="status-bar">
            <span><span class="status-dot"></span>SYSTEM ONLINE</span>
            <span>ENCRYPTION: AES-256</span>
            <span>AUTH: BCRYPT</span>
        </div>
    </div>

    <div class="version-tag">ZERO TRUST v1.0 &nbsp;|&nbsp; SQL INJECTION PROTECTED &nbsp;|&nbsp; PREPARED STATEMENTS</div>
</div>

<script>
// Particle generator
(function(){
    const container = document.getElementById('particles');
    for(let i = 0; i < 40; i++){
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.cssText = `
            left:${Math.random()*100}%;
            animation-duration:${6 + Math.random()*12}s;
            animation-delay:${Math.random()*10}s;
            --dx:${(Math.random()-0.5)*80}px;
            width:${Math.random() > 0.7 ? 3 : 2}px;
            height:${Math.random() > 0.7 ? 3 : 2}px;
            background:${Math.random() > 0.5 ? '#00ffe7' : '#ff2d6b'};
        `;
        container.appendChild(p);
    }
})();

// Toggle password visibility
document.getElementById('togglePw').addEventListener('click', function(){
    const pw = document.getElementById('password');
    pw.type = pw.type === 'password' ? 'text' : 'password';
    this.textContent = pw.type === 'password' ? '👁' : '🙈';
});

// Button loading state
document.getElementById('loginForm').addEventListener('submit', function(){
    const btn = document.getElementById('loginBtn');
    btn.classList.add('loading');
    btn.textContent = '► AUTHENTICATING...';
});
</script>

</body>
</html>
