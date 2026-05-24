<?php
require_once '../includes/auth.php';
requireAdmin();

$conn = getDB();

// Stats
$total_users   = $conn->query("SELECT COUNT(*) FROM users WHERE role='player'")->fetch_row()[0];
$total_admins  = $conn->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetch_row()[0];
$active_users  = $conn->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetch_row()[0];
$total_logs    = $conn->query("SELECT COUNT(*) FROM logs")->fetch_row()[0];

// Recent logs
$recent_logs = $conn->query(
    "SELECT l.activity, l.timestamp, l.ip_address, u.username
     FROM logs l LEFT JOIN users u ON l.user_id = u.id
     ORDER BY l.timestamp DESC LIMIT 8"
);
$conn->close();

$pageTitle = 'ADMIN DASHBOARD';
include '../includes/header.php';
?>

<div class="stat-row">
    <div class="stat-card">
        <div class="stat-num"><?= $total_users ?></div>
        <div class="stat-label">PLAYERS</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $total_admins ?></div>
        <div class="stat-label">ADMINS</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $active_users ?></div>
        <div class="stat-label">ACTIVE ACCOUNTS</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $total_logs ?></div>
        <div class="stat-label">LOG ENTRIES</div>
    </div>
</div>

<div class="card">
    <div class="card-title">▣ RECENT ACTIVITY FEED</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>User</th>
                <th>Activity</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
        <?php while($log = $recent_logs->fetch_assoc()): ?>
        <tr>
            <td class="mono"><?= e($log['timestamp']) ?></td>
            <td class="mono"><?= e($log['username'] ?? 'SYSTEM') ?></td>
            <td><?= e($log['activity']) ?></td>
            <td class="mono"><?= e($log['ip_address']) ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div style="display:flex;gap:16px;flex-wrap:wrap;">
    <a href="/zero_trust/admin/users.php" class="btn btn-primary">◉ MANAGE USERS</a>
    <a href="/zero_trust/admin/logs.php" class="btn btn-warning">▣ VIEW ALL LOGS</a>
</div>

<?php include '../includes/footer.php'; ?>
