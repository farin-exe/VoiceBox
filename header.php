<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? 'VoiceBox') ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php">
        <img src="assets/images/logo.jpg" alt="VoiceBox" class="brand-logo">
        <span>VoiceBox</span>
    </a>

    <button class="menu-toggle" id="menuToggle" type="button" aria-label="Open menu">☰</button>

    <nav id="mainNav">
        <a href="index.php">Home</a>

        <?php if (isLoggedIn() && $_SESSION['role'] === 'student'): ?>
            <a href="submit.php">Submit Complaint</a>
            <a href="complaints.php">My Complaints</a>
        <?php endif; ?>

        <?php if (isLoggedIn() && $_SESSION['role'] === 'admin'): ?>
            <a href="admin.php">Admin Panel</a>
        <?php endif; ?>

        <?php if (isLoggedIn()): ?>
            <span class="user-pill"><?= h($_SESSION['name']) ?></span>
            <a class="nav-button" href="logout.php">Logout</a>
        <?php else: ?>
            <a class="nav-button" href="auth.php">Login / Register</a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
