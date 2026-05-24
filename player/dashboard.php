<?php
require_once '../includes/auth.php';
requireLogin();

// Redirect admins to admin dashboard
if ($_SESSION['role'] === 'admin') {
    header('Location: /zero_trust/admin/dashboard.php');
    exit;
}

$conn = getDB();

// Get player's own login history
$stmt = $conn->prepare(
    "SELECT activity, timestamp, ip_address FROM logs
     WHERE user_id = ? ORDER BY timestamp DESC LIMIT 10"
);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$myLogs = $stmt->get_result();
$stmt->close();
$conn->close();

$msg = $_GET['msg'] ?? '';

$pageTitle = 'PLAYER DASHBOARD';
include '../includes/header.php';
?>

<?php if ($msg === 'unauthorized'): ?>
<div class="flash flash-error">⚠ UNAUTHORIZED — Admin access required.</div>
<?php endif; ?>

<!-- Welcome -->
<div class="card" style="border-color: rgba(0,255,231,0.4);">
    <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
        <div style="font-size:48px;filter:drop-shadow(var(--glow));">🎮</div>
        <div>
            <div style="font-family:var(--font-hud);font-size:20px;color:#fff;margin-bottom:6px;letter-spacing:3px;">
                WELCOME BACK, <?= strtoupper(e($_SESSION['fullname'])) ?>
            </div>
            <div style="font-family:var(--font-mono);font-size:11px;color:var(--accent);letter-spacing:2px;">
                AGENT ID: <?= e($_SESSION['username']) ?> &nbsp;|&nbsp;
                CLEARANCE: <span style="color:var(--accent);">PLAYER</span>
            </div>
        </div>
    </div>
</div>

<!-- Game placeholder cards -->
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-num" style="color:var(--accent3);">—</div>
        <div class="stat-label">MISSIONS CLEARED</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:var(--accent2);">—</div>
        <div class="stat-label">THREATS DEFEATED</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">—</div>
        <div class="stat-label">CURRENT LEVEL</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#0f0;">—</div>
        <div class="stat-label">SCORE</div>
    </div>
</div>

<!-- Game placeholder -->
<div class="card" style="text-align:center;padding:48px 32px;">
    <div style="font-family:var(--font-hud);font-size:15px;color:var(--accent);letter-spacing:5px;margin-bottom:12px;">
        ▶ GAME MODULE
    </div>
    <div style="font-family:var(--font-mono);font-size:12px;color:var(--muted);letter-spacing:2px;line-height:1.8;">
        ZERO TRUST — CYBERSECURITY ROLE PLAYING GAME<br>
        <span style="color:var(--accent2);">[ GAME CONTENT COMING SOON ]</span><br><br>
        This system currently demonstrates the secure<br>
        Login &amp; User Management module for IT 10.
    </div>
</div>

<!-- My recent activity -->
<div class="card">
    <div class="card-title">▣ MY ACTIVITY LOG</div>
    <table class="data-table">
        <thead>
            <tr><th>Timestamp</th><th>Activity</th><th>IP</th></tr>
        </thead>
        <tbody>
        <?php while($log = $myLogs->fetch_assoc()): ?>
        <tr>
            <td class="mono" style="font-size:11px;"><?= e($log['timestamp']) ?></td>
            <td style="font-size:13px;"><?= e($log['activity']) ?></td>
            <td class="mono" style="font-size:11px;"><?= e($log['ip_address']) ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
