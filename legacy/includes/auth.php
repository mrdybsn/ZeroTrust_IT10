<?php
// ============================================================
// Zero Trust — Auth & Session Helpers
// Security: hashed passwords, prepared statements, logging
// ============================================================

require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------------------------------------
// Log an activity
// -----------------------------------------------------------
function logActivity($user_id, $activity) {
    $conn = getDB();
    $ip   = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $stmt = $conn->prepare(
        "INSERT INTO logs (user_id, activity, ip_address) VALUES (?, ?, ?)"
    );
    $stmt->bind_param('iss', $user_id, $activity, $ip);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

// -----------------------------------------------------------
// Attempt login — uses prepared statement (SQL Injection safe)
// -----------------------------------------------------------
function attemptLogin($username, $password) {
    $conn = getDB();

    // Only query by username; password verified via bcrypt
    $stmt = $conn->prepare(
        "SELECT id, fullname, username, password, role, status FROM users WHERE username = ? LIMIT 1"
    );
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if (!$user) {
        return ['success' => false, 'message' => 'Invalid username or password.'];
    }
    if ($user['status'] === 'inactive') {
        return ['success' => false, 'message' => 'Account is deactivated. Contact admin.'];
    }
    if (!password_verify($password, $user['password'])) {
        logActivity(null, "Failed login attempt for username: $username");
        return ['success' => false, 'message' => 'Invalid username or password.'];
    }

    // Regenerate session to prevent fixation
    session_regenerate_id(true);

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['fullname']  = $user['fullname'];
    $_SESSION['role']      = $user['role'];

    logActivity($user['id'], "User '{$user['username']}' logged in successfully.");
    return ['success' => true, 'role' => $user['role']];
}

// -----------------------------------------------------------
// Logout
// -----------------------------------------------------------
function logout() {
    if (!empty($_SESSION['user_id'])) {
        logActivity($_SESSION['user_id'], "User '{$_SESSION['username']}' logged out.");
    }
    session_unset();
    session_destroy();
}

// -----------------------------------------------------------
// Guard: require login
// -----------------------------------------------------------
function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        header('Location: /zero_trust/index.php?msg=session_expired');
        exit;
    }
}

// -----------------------------------------------------------
// Guard: require admin role
// -----------------------------------------------------------
function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: /zero_trust/player/dashboard.php?msg=unauthorized');
        exit;
    }
}

// -----------------------------------------------------------
// Sanitize output
// -----------------------------------------------------------
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
