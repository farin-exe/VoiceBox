<?php
require_once 'includes/auth.php';
require_once 'config/db.php';
requireAdmin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $id = (int)($_POST['complaint_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $note = trim($_POST['admin_note'] ?? '');

        $allowed = ['Pending', 'In Progress', 'Resolved', 'Rejected'];

        if (!in_array($status, $allowed, true)) {
            $error = 'Invalid status.';
        } elseif (strlen($note) > 2000) {
            $error = 'Admin note is too long.';
        } else {
            $stmt = $pdo->prepare(
                "UPDATE complaints SET status=?, admin_note=? WHERE id=?"
            );
            $stmt->execute([$status, $note, $id]);
            $message = 'Complaint status updated successfully.';
        }
    }

    if ($action === 'delete_complaint') {
        $id = (int)($_POST['complaint_id'] ?? 0);

        $stmt = $pdo->prepare("DELETE FROM complaints WHERE id=?");
        $stmt->execute([$id]);
        $message = 'Complaint record deleted.';
    }

    if ($action === 'add_category') {
        $name = trim($_POST['category_name'] ?? '');

        if ($name === '') {
            $error = 'Category name is required.';
        } elseif (strlen($name) > 100) {
            $error = 'Category name is too long.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                $stmt->execute([$name]);
                $message = 'Category added successfully.';
            } catch (PDOException $e) {
                $error = 'That category already exists.';
            }
        }
    }

    if ($action === 'delete_category') {
        $id = (int)($_POST['category_id'] ?? 0);

        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
            $stmt->execute([$id]);
            $message = 'Category deleted successfully.';
        } catch (PDOException $e) {
            $error = 'This category cannot be deleted because a complaint uses it.';
        }
    }
}

$stats = $pdo->query(
    "SELECT
        COUNT(*) AS total,
        COALESCE(SUM(status='Pending'),0) AS pending,
        COALESCE(SUM(status='In Progress'),0) AS progress,
        COALESCE(SUM(status='Resolved'),0) AS resolved
     FROM complaints"
)->fetch();

$complaints = $pdo->query(
    "SELECT c.*, u.name AS student_name, u.student_id AS student_code,
            cat.name AS category
     FROM complaints c
     INNER JOIN users u ON u.id=c.student_id
     INNER JOIN categories cat ON cat.id=c.category_id
     ORDER BY c.created_at DESC"
)->fetchAll();

$categories = $pdo->query(
    "SELECT id, name FROM categories ORDER BY name"
)->fetchAll();

$pageTitle = 'Admin Panel | VoiceBox';
require 'includes/header.php';
?>

<div class="page-heading">
    <div>
        <span class="eyebrow">ADMINISTRATION</span>
        <h1>Complaint Control Center</h1>
        <p>Review complaints, update statuses, manage categories, and remove invalid records.</p>
    </div>
</div>

<?php if ($message): ?><div class="alert success"><?= h($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>

<div class="stats-row admin-stats">
    <div class="metric"><span>Total Complaints</span><strong><?= (int)$stats['total'] ?></strong><small>All records</small></div>
    <div class="metric"><span>Pending</span><strong><?= (int)$stats['pending'] ?></strong><small>Needs review</small></div>
    <div class="metric"><span>In Progress</span><strong><?= (int)$stats['progress'] ?></strong><small>Being handled</small></div>
    <div class="metric"><span>Resolved</span><strong><?= (int)$stats['resolved'] ?></strong><small>Completed</small></div>
</div>

<section class="admin-section">
    <div class="section-title compact">
        <span>COMPLAINT MANAGEMENT</span>
        <h2>All Student Complaints</h2>
    </div>

    <div class="table-card">
        <div class="table-top">
            <div><h3>Complaint Records</h3><p>Update status and leave an optional administrative note.</p></div>
            <input class="table-search" id="adminSearch" type="search" placeholder="Search records...">
        </div>

        <div class="table-wrap">
            <table id="adminTable">
                <thead>
                <tr>
                    <th>Student</th>
                    <th>Complaint</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($complaints as $c): ?>
                    <tr>
                        <td>
                            <strong><?= h($c['student_name']) ?></strong>
                            <div class="muted"><?= h($c['student_code']) ?></div>
                        </td>
                        <td>
                            <strong><?= h($c['subject']) ?></strong>
                            <div class="muted"><?= h(mb_strimwidth($c['description'], 0, 80, '...')) ?></div>
                        </td>
                        <td><?= h($c['category']) ?></td>
                        <td>
                            <span class="status status-<?= strtolower(str_replace(' ', '-', $c['status'])) ?>">
                                <?= h($c['status']) ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="admin-form">
                                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="complaint_id" value="<?= (int)$c['id'] ?>">

                                <select name="status" required>
                                    <?php foreach (['Pending','In Progress','Resolved','Rejected'] as $s): ?>
                                        <option value="<?= h($s) ?>" <?= $c['status'] === $s ? 'selected' : '' ?>>
                                            <?= h($s) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <input type="text" name="admin_note"
                                       value="<?= h($c['admin_note']) ?>"
                                       maxlength="2000"
                                       placeholder="Admin note (optional)">

                                <button class="btn small primary" type="submit">Update</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" data-confirm="Delete this complaint record permanently?">
                                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                                <input type="hidden" name="action" value="delete_complaint">
                                <input type="hidden" name="complaint_id" value="<?= (int)$c['id'] ?>">
                                <button class="text-btn danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (!$complaints): ?>
                    <tr><td colspan="6" class="empty-table">No complaint records available.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="admin-section">
    <div class="section-title compact">
        <span>CATEGORY MANAGEMENT</span>
        <h2>Complaint Categories</h2>
    </div>

    <div class="category-manager">
        <form method="POST" class="category-add">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <input type="hidden" name="action" value="add_category">
            <input type="text" name="category_name" maxlength="100"
                   placeholder="Enter a new category" required>
            <button class="btn primary" type="submit">+ Add Category</button>
        </form>

        <div class="category-list">
            <?php foreach ($categories as $cat): ?>
                <div class="category-chip">
                    <span><?= h($cat['name']) ?></span>
                    <form method="POST" data-confirm="Delete this category?">
                        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                        <input type="hidden" name="action" value="delete_category">
                        <input type="hidden" name="category_id" value="<?= (int)$cat['id'] ?>">
                        <button class="chip-delete" type="submit">×</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
