<?php
require_once 'config/db.php';

$email = 'admin@voicebox.com';
$password = 'Admin@123';
$name = 'VoiceBox Administrator';

$stmt = $pdo->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    echo '<h2>Admin account already exists.</h2>';
    echo '<p>You can now log in with the admin account. Delete setup_admin.php after setup.</p>';
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO users (name,email,student_id,password,role)
     VALUES (?, ?, NULL, ?, 'admin')"
);
$stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);

echo '<h2>Admin account created successfully.</h2>';
echo '<p>Email: <strong>admin@voicebox.com</strong></p>';
echo '<p>Password: <strong>Admin@123</strong></p>';
echo '<p><strong>Important:</strong> Delete setup_admin.php after this page runs successfully.</p>';
?>