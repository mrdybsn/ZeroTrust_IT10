<?php
require_once '../includes/auth.php';
requireAdmin();

$conn = getDB();
$logs = $conn->query(
    "SELECT l.id, l.activity, l.timestamp, l.ip_address, u.username, u.role
     FROM logs l
     LEFT JOIN users u ON l.user_id = u.id
     ORDER BY l.timestamp DESC
     LIMIT 200"
);
$conn->close();

$pageTitle = 'ACTIVITY LOGS';
include '../includes/header.php';
?>

<div class="card">
    <div class="card-title">▣ SYSTEM ACTIVITY LOG — LAST 200 ENTRIES</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Timestamp</th>
                <th>User</th>
                <th>Role</th>
                <th>Activity</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
        <?php while($log = $logs->fetch_assoc()): ?>
        <tr>
            <td class="mono" style="font-size:11px;"><?= $log['id'] ?></td>
            <td class="mono" style="font-size:11px;"><?= e($log['timestamp']) ?></td>
            <td class="mono"><?= e($log['username'] ?? '— SYSTEM —') ?></td>
            <td>
                <?php if($log['role']): ?>
                <span class="badge badge-<?= $log['role'] ?>"><?= strtoupper($log['role']) ?></span>
                <?php else: ?>
                <span class="badge" style="color:var(--muted);border-color:var(--muted);">N/A</span>
                <?php endif; ?>
            </td>
            <td style="font-size:13px;"><?= e($log['activity']) ?></td>
            <td class="mono" style="font-size:11px;"><?= e($log['ip_address']) ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
