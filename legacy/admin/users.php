<?php
require_once '../includes/auth.php';
requireAdmin();

$conn  = getDB();
$flash = '';
$flashType = 'success';

// ---- ADD USER ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $flash = 'Invalid CSRF token.'; $flashType = 'error';
    } else {
        $fullname = trim($_POST['fullname'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = in_array($_POST['role'] ?? '', ['admin','player']) ? $_POST['role'] : 'player';

        if (empty($fullname) || empty($username) || empty($password)) {
            $flash = 'All fields are required.'; $flashType = 'error';
        } elseif (strlen($password) < 8) {
            $flash = 'Password must be at least 8 characters.'; $flashType = 'error';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt   = $conn->prepare("INSERT INTO users (fullname, username, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $fullname, $username, $hashed, $role);
            if ($stmt->execute()) {
                $newId = $conn->insert_id;
                logActivity($_SESSION['user_id'], "Admin added user: $username (role: $role)");
                $flash = "User '$username' created successfully.";
            } else {
                $flash = 'Username already exists.'; $flashType = 'error';
            }
            $stmt->close();
        }
    }
}

// ---- EDIT USER ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $flash = 'Invalid CSRF token.'; $flashType = 'error';
    } else {
        $id       = (int)($_POST['id'] ?? 0);
        $fullname = trim($_POST['fullname'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $role     = in_array($_POST['role'] ?? '', ['admin','player']) ? $_POST['role'] : 'player';
        $status   = in_array($_POST['status'] ?? '', ['active','inactive']) ? $_POST['status'] : 'active';
        $password = $_POST['password'] ?? '';

        if ($id > 0 && !empty($fullname) && !empty($username)) {
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt   = $conn->prepare("UPDATE users SET fullname=?, username=?, password=?, role=?, status=? WHERE id=?");
                $stmt->bind_param('sssssi', $fullname, $username, $hashed, $role, $status, $id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET fullname=?, username=?, role=?, status=? WHERE id=?");
                $stmt->bind_param('ssssi', $fullname, $username, $role, $status, $id);
            }
            if ($stmt->execute()) {
                logActivity($_SESSION['user_id'], "Admin updated user ID: $id ($username)");
                $flash = "User '$username' updated.";
            } else {
                $flash = 'Update failed. Username may be taken.'; $flashType = 'error';
            }
            $stmt->close();
        }
    }
}

// ---- DELETE USER ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $flash = 'Invalid CSRF token.'; $flashType = 'error';
    } else {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === (int)$_SESSION['user_id']) {
            $flash = 'Cannot delete your own account.'; $flashType = 'error';
        } elseif ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            logActivity($_SESSION['user_id'], "Admin deleted user ID: $id");
            $flash = "User deleted.";
            $stmt->close();
        }
    }
}

// Fetch all users
$users = $conn->query("SELECT id, fullname, username, role, status, created_at FROM users ORDER BY id ASC");
$conn->close();

// Refresh CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$pageTitle = 'USER MANAGEMENT';
include '../includes/header.php';
?>

<?php if ($flash): ?>
<div class="flash flash-<?= $flashType ?>"><?= ($flashType==='success' ? '✔' : '⚠') ?> <?= e($flash) ?></div>
<?php endif; ?>

<div style="display:flex;justify-content:flex-end;margin-bottom:20px;">
    <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('open')">
        + ADD USER
    </button>
</div>

<div class="card">
    <div class="card-title">◉ REGISTERED AGENTS</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while($u = $users->fetch_assoc()): ?>
        <tr>
            <td class="mono"><?= $u['id'] ?></td>
            <td><?= e($u['fullname']) ?></td>
            <td class="mono"><?= e($u['username']) ?></td>
            <td><span class="badge badge-<?= $u['role'] ?>"><?= strtoupper($u['role']) ?></span></td>
            <td><span class="badge badge-<?= $u['status'] ?>"><?= strtoupper($u['status']) ?></span></td>
            <td class="mono" style="font-size:11px;"><?= e(date('Y-m-d', strtotime($u['created_at']))) ?></td>
            <td>
                <button class="btn btn-warning btn-sm"
                    onclick="openEdit(<?= htmlspecialchars(json_encode($u)) ?>)">EDIT</button>
                <?php if($u['id'] != $_SESSION['user_id']): ?>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                    <button type="submit" class="btn btn-danger btn-sm">DEL</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- ADD USER MODAL -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-title">+ REGISTER NEW AGENT</div>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <div class="form-group">
                <label class="form-label">// FULL NAME</label>
                <input type="text" name="fullname" class="form-control" placeholder="Full name" maxlength="100" required>
            </div>
            <div class="form-group">
                <label class="form-label">// USERNAME</label>
                <input type="text" name="username" class="form-control" placeholder="Username" maxlength="50" required>
            </div>
            <div class="form-group">
                <label class="form-label">// PASSWORD (min 8 chars)</label>
                <input type="password" name="password" class="form-control" placeholder="Password" minlength="8" required>
            </div>
            <div class="form-group">
                <label class="form-label">// ROLE</label>
                <select name="role" class="form-control">
                    <option value="player">PLAYER</option>
                    <option value="admin">ADMIN</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-danger" onclick="document.getElementById('addModal').classList.remove('open')">CANCEL</button>
                <button type="submit" class="btn btn-primary">▶ CREATE</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-title">◉ EDIT AGENT RECORD</div>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <div class="form-group">
                <label class="form-label">// FULL NAME</label>
                <input type="text" name="fullname" id="editFullname" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">// USERNAME</label>
                <input type="text" name="username" id="editUsername" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">// NEW PASSWORD (leave blank to keep)</label>
                <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
            </div>
            <div class="form-group">
                <label class="form-label">// ROLE</label>
                <select name="role" id="editRole" class="form-control">
                    <option value="player">PLAYER</option>
                    <option value="admin">ADMIN</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">// STATUS</label>
                <select name="status" id="editStatus" class="form-control">
                    <option value="active">ACTIVE</option>
                    <option value="inactive">INACTIVE</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-danger" onclick="document.getElementById('editModal').classList.remove('open')">CANCEL</button>
                <button type="submit" class="btn btn-primary">▶ SAVE</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(u) {
    document.getElementById('editId').value       = u.id;
    document.getElementById('editFullname').value  = u.fullname;
    document.getElementById('editUsername').value  = u.username;
    document.getElementById('editRole').value      = u.role;
    document.getElementById('editStatus').value    = u.status;
    document.getElementById('editModal').classList.add('open');
}
// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if(e.target === o) o.classList.remove('open'); });
});
</script>

<?php include '../includes/footer.php'; ?>
