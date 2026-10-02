<?php
require_once 'includes/auth.php';
require_once 'config/db.php';
requireStudent();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $action = $_POST['action'] ?? '';
    $complaint_id = (int)($_POST['complaint_id'] ?? 0);

    $stmt = $pdo->prepare(
        "SELECT id, status FROM complaints
         WHERE id = ? AND student_id = ?"
    );
    $stmt->execute([$complaint_id, $_SESSION['user_id']]);
    $complaint = $stmt->fetch();

    if (!$complaint) {
        $error = 'Complaint not found.';
    } elseif ($action === 'withdraw') {
        if ($complaint['status'] !== 'Pending') {
            $error = 'Only pending complaints can be withdrawn.';
        } else {
            $stmt = $pdo->prepare(
                "UPDATE complaints SET status='Withdrawn'
                 WHERE id=? AND student_id=?"
            );
            $stmt->execute([$complaint_id, $_SESSION['user_id']]);
            $message = 'Complaint withdrawn successfully.';
        }
    } elseif ($action === 'delete') {
        if (!in_array($complaint['status'], ['Withdrawn', 'Rejected'], true)) {
            $error = 'Only withdrawn or rejected complaints can be deleted.';
        } else {
            $stmt = $pdo->prepare(
                "DELETE FROM complaints WHERE id=? AND student_id=?"
            );
            $stmt->execute([$complaint_id, $_SESSION['user_id']]);
            $message = 'Complaint deleted successfully.';
        }
    }
}

$stmt = $pdo->prepare(
    "SELECT c.*, cat.name AS category
     FROM complaints c
     INNER JOIN categories cat ON cat.id = c.category_id
     WHERE c.student_id = ?
     ORDER BY c.created_at DESC"
);
$stmt->execute([$_SESSION['user_id']]);
$complaints = $stmt->fetchAll();

$total = count($complaints);
$pending = count(array_filter($complaints, fn($c) => $c['status'] === 'Pending'));
$progress = count(array_filter($complaints, fn($c) => $c['status'] === 'In Progress'));
$resolved = count(array_filter($complaints, fn($c) => $c['status'] === 'Resolved'));

$pageTitle = 'My Complaints | VoiceBox';
require 'includes/header.php';
?>

<div class="page-heading">
    <div>
        <span class="eyebrow">STUDENT DASHBOARD</span>
        <h1>My Complaints</h1>
        <p>Monitor your submitted complaints and their current status.</p>
    </div>
    <a href="submit.php" class="btn primary">+ New Complaint</a>
</div>

<?php if ($message): ?><div class="alert success"><?= h($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>

<div class="stats-row">
    <div class="metric"><span>Total</span><strong><?= $total ?></strong><small>Submitted</small></div>
    <div class="metric"><span>Pending</span><strong><?= $pending ?></strong><small>Awaiting review</small></div>
    <div class="metric"><span>In Progress</span><strong><?= $progress ?></strong><small>Being handled</small></div>
    <div class="metric"><span>Resolved</span><strong><?= $resolved ?></strong><small>Completed</small></div>
</div>

<div class="table-card">
    <?php if (!$complaints): ?>
        <div class="empty-state">
            <div class="empty-icon">V</div>
            <h2>No complaints yet</h2>
            <p>Your submitted complaints will appear here.</p>
            <a href="submit.php" class="btn primary">Submit Your First Complaint</a>
        </div>
    <?php else: ?>
        <div class="table-top">
            <div><h3>Complaint History</h3><p>All complaints submitted from your account.</p></div>
            <input class="table-search" id="complaintSearch" type="search" placeholder="Search complaints...">
        </div>

        <div class="table-wrap">
            <table id="complaintsTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Complaint</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Updated</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($complaints as $c): ?>
                    <tr>
                        <td>#<?= (int)$c['id'] ?></td>
                        <td>
                            <strong><?= h($c['subject']) ?></strong>
                            <div class="muted"><?= h(mb_strimwidth($c['description'], 0, 65, '...')) ?></div>
                            <?php if ($c['admin_note']): ?>
                                <div class="admin-note"><b>Admin:</b> <?= h($c['admin_note']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= h($c['category']) ?></td>
                        <td>
                            <span class="status status-<?= strtolower(str_replace(' ', '-', $c['status'])) ?>">
                                <?= h($c['status']) ?>
                            </span>
                        </td>
                        <td><?= h(date('d M Y', strtotime($c['created_at']))) ?></td>
                        <td><?= h(date('d M Y', strtotime($c['updated_at']))) ?></td>
                        <td>
                            <?php if ($c['status'] === 'Pending'): ?>
                                <form method="POST" data-confirm="Withdraw this pending complaint?">
                                    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                                    <input type="hidden" name="action" value="withdraw">
                                    <input type="hidden" name="complaint_id" value="<?= (int)$c['id'] ?>">
                                    <button class="text-btn warning" type="submit">Withdraw</button>
                                </form>
                            <?php elseif (in_array($c['status'], ['Withdrawn', 'Rejected'], true)): ?>
                                <form method="POST" data-confirm="Delete this complaint permanently?">
                                    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="complaint_id" value="<?= (int)$c['id'] ?>">
                                    <button class="text-btn danger" type="submit">Delete</button>
                                </form>
                            <?php else: ?>
                                <span class="muted">No action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
