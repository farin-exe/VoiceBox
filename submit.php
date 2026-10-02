<?php
require_once 'includes/auth.php';
require_once 'config/db.php';
requireStudent();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $category_id = (int)($_POST['category_id'] ?? 0);
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($category_id <= 0 || $subject === '' || $description === '') {
        $error = 'Please complete all complaint fields.';
    } elseif (strlen($subject) > 200) {
        $error = 'Subject cannot exceed 200 characters.';
    } elseif (strlen($description) < 15) {
        $error = 'Please provide at least 15 characters in the description.';
    } elseif (strlen($description) > 5000) {
        $error = 'Description cannot exceed 5000 characters.';
    } else {
        $check = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
        $check->execute([$category_id]);

        if (!$check->fetch()) {
            $error = 'Please select a valid complaint category.';
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO complaints
                (student_id, category_id, subject, description)
                VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $_SESSION['user_id'],
                $category_id,
                $subject,
                $description
            ]);

            $success = 'Complaint submitted successfully.';
        }
    }
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

$pageTitle = 'Submit Complaint | VoiceBox';
require 'includes/header.php';
?>

<div class="page-heading">
    <div>
        <span class="eyebrow">STUDENT PORTAL</span>
        <h1>Submit a Complaint</h1>
        <p>Give enough detail so the administration can understand and review your concern.</p>
    </div>
</div>

<div class="form-layout">
    <section class="form-card">
        <?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert success"><?= h($success) ?></div><?php endif; ?>

        <form id="complaintForm" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">

            <div class="form-grid">
                <label>
                    Complaint Category
                    <select name="category_id" required>
                        <option value="">Choose a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int)$category['id'] ?>">
                                <?= h($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Subject
                    <input type="text" name="subject" maxlength="200"
                           placeholder="Short title of your complaint" required>
                </label>
            </div>

            <label>
                Description
                <textarea name="description" id="description" maxlength="5000" rows="10"
                          placeholder="Explain the issue clearly..." required></textarea>
                <small class="counter"><span id="descriptionCount">0</span> / 5000 characters</small>
            </label>

            <div class="form-actions">
                <a href="complaints.php" class="btn light">Cancel</a>
                <button class="btn primary" type="submit">Submit Complaint →</button>
            </div>
        </form>
    </section>

    <aside class="side-info">
        <div class="info-icon">i</div>
        <h3>Before you submit</h3>
        <ul>
            <li>Choose the most relevant category.</li>
            <li>Use a clear and specific subject.</li>
            <li>Explain what happened and where.</li>
            <li>Avoid submitting duplicate complaints.</li>
        </ul>
    </aside>
</div>

<?php require 'includes/footer.php'; ?>
