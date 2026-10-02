<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

if (isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin') {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter your admin email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$admin['id'];
            $_SESSION['name'] = $admin['name'];
            $_SESSION['role'] = 'admin';
            header('Location: admin.php');
            exit;
        }

        $error = 'Invalid administrator email or password.';
    }
}

$pageTitle = 'Admin Login | VoiceBox';
require 'includes/header.php';
?>

<div class="admin-auth-page">
    <section class="admin-auth-card">
        <div class="admin-icon">
        <img src="assets/images/logo.jpg" alt="VoiceBox" class="admin-logo">
        <div class="admin-badge">Administrator Access</div>
        <h1>VoiceBox Admin Login</h1>
        <p class="admin-auth-subtitle">Sign in to review, manage, and update student complaints.</p>

        <?php if ($error): ?>
            <div class="alert error"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="admin-login-form">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">

            <label>Administrator Email
                <input type="email" name="email" placeholder="admin@voicebox.com" autocomplete="username" required>
            </label>

            <label>Password
                <span class="password-field">
                    <input type="password" name="password" placeholder="Enter administrator password" autocomplete="current-password" required>
                    <button type="button" class="password-toggle" aria-label="Show password" aria-pressed="false">
                        <svg class="eye-icon eye-open" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2.1 12s3.3-6 9.9-6 9.9 6 9.9 6-3.3 6-9.9 6-9.9-6-9.9-6Z"></path>
                            <circle cx="12" cy="12" r="2.8"></circle>
                        </svg>
                        <svg class="eye-icon eye-closed" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m3 3 18 18"></path>
                            <path d="M10.6 6.2A10.7 10.7 0 0 1 12 6c6.6 0 9.9 6 9.9 6a17 17 0 0 1-3.1 3.8"></path>
                            <path d="M6.2 6.2C3.6 8 2.1 12 2.1 12s3.3 6 9.9 6c1.5 0 2.8-.3 4-.8"></path>
                            <path d="M9.9 9.9a2.8 2.8 0 0 0 4.2 4.2"></path>
                        </svg>
                    </button>
                </span>
            </label>

            <button class="btn primary full" type="submit">Sign In as Administrator</button>
        </form>

        <div class="admin-auth-footer">
            <span>Student?</span>
            <a href="auth.php">Go to Student Login / Registration</a>
        </div>
    </section>
</div>

<?php require 'includes/footer.php'; ?>
