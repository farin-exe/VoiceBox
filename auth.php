<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

if (isLoggedIn()) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin.php' : 'complaints.php'));
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        $student_id = trim($_POST['student_id'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($name === '' || $student_id === '' || $email === '' || $password === '' || $confirm_password === '') {
            $error = 'Please fill in every registration field.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm_password) {
            $error = 'Password and confirm password do not match.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO users (name, email, student_id, password, role)
                     VALUES (?, ?, ?, ?, 'student')"
                );
                $stmt->execute([
                    $name,
                    $email,
                    $student_id,
                    password_hash($password, PASSWORD_DEFAULT)
                ]);
                $success = 'Registration successful. Please log in.';
            } catch (PDOException $e) {
                $error = ($e->getCode() === '23000')
                    ? 'That email or student ID is already registered.'
                    : 'Registration could not be completed.';
            }
        }
    }

    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            header('Location: ' . ($user['role'] === 'admin' ? 'admin.php' : 'complaints.php'));
            exit;
        }

        $error = 'Incorrect email or password.';
    }
}

$pageTitle = 'Login & Registration | VoiceBox';
require 'includes/header.php';
?>

<div class="auth-page">
    <section class="auth-showcase">
        <div class="badge"><span class="pulse"></span> Secure Student Portal</div>
        <h1>Turn concerns into <span>action.</span></h1>
        <p>Sign in to track your complaints or create a new student account.</p>

        <div class="auth-points">
            <div><b>✓</b><span>Session-based authentication</span></div>
            <div><b>✓</b><span>Personal complaint tracking</span></div>
            <div><b>✓</b><span>Transparent status updates</span></div>
        </div>
    </section>

    <section class="auth-box">
        <?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert success"><?= h($success) ?></div><?php endif; ?>

        <div class="tabs">
            <button type="button" class="tab active" data-tab="loginPanel">Login</button>
            <button type="button" class="tab" data-tab="registerPanel">Register</button>
        </div>

        <a class="separate-admin-link" href="admin-login.php">Admin Login →</a>

        <form id="loginPanel" class="tab-panel active" method="POST">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <input type="hidden" name="action" value="login">

            <label>Email
                <input type="email" name="email" placeholder="student@example.com" required>
            </label>

            <label>Password
                <span class="password-field">
                    <input type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
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

            <button class="btn primary full" type="submit">Login to VoiceBox</button>
        </form>

        <form id="registerPanel" class="tab-panel" method="POST">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <input type="hidden" name="action" value="register">

            <label>Full Name
                <input type="text" name="name" placeholder="Your full name" maxlength="100" required>
            </label>

            <label>Student ID
                <input type="text" name="student_id" placeholder="e.g. CSE-12345" maxlength="50" required>
            </label>

            <label>Email
                <input type="email" name="email" placeholder="student@example.com" required>
            </label>

            <label>Password
                <span class="password-field">
                    <input type="password" name="password" minlength="6" placeholder="Minimum 6 characters" autocomplete="new-password" required>
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

            <label>Confirm Password
                <span class="password-field">
                    <input type="password" name="confirm_password" minlength="6" placeholder="Re-enter your password" autocomplete="new-password" required>
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

            <button class="btn primary full" type="submit">Create Student Account</button>
        </form>
    </section>
</div>

<?php require 'includes/footer.php'; ?>
