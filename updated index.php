<?php
require_once 'includes/auth.php';
$pageTitle = 'VoiceBox | Student Complaint Management';
require 'includes/header.php';
?>

<section class="hero">
    <div class="hero-copy">
        <div class="badge"><span class="pulse"></span> Student Support Platform</div>
        <h1>Make your voice <span>heard.</span></h1>
        <p>
            VoiceBox gives students a simple way to submit complaints,
            follow their progress, and stay informed until the issue is resolved.
        </p>

        <div class="hero-actions">
            <?php if (isLoggedIn() && $_SESSION['role'] === 'student'): ?>
                <a href="submit.php" class="btn primary">Submit a Complaint <span>→</span></a>
                <a href="complaints.php" class="btn light">Track Complaints</a>
            <?php elseif (isLoggedIn() && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" class="btn primary">Open Admin Panel <span>→</span></a>
            <?php else: ?>
                <a href="auth.php" class="btn primary">Get Started <span>→</span></a>
                <a href="#process" class="btn light">How It Works</a>
            <?php endif; ?>
        </div>

        <div class="trust-row">
            <div><strong>01</strong><span>Submit</span></div>
            <div><strong>02</strong><span>Review</span></div>
            <div><strong>03</strong><span>Resolve</span></div>
        </div>
    </div>

    <div class="hero-visual">
        <div class="visual-glow"></div>
        <div class="dashboard-card">
            <div class="mini-header">
                <span>Complaint Status</span>
                <span class="live-dot">● Live</span>
            </div>
            <div class="mini-main">
                <div class="ring"><span>72%</span></div>
                <div>
                    <strong>Resolution journey</strong>
                    <p>Your complaint moves through clear stages.</p>
                </div>
            </div>
            <div class="timeline">
                <div class="done"><i>✓</i><span>Complaint submitted</span></div>
                <div class="done"><i>✓</i><span>Admin review</span></div>
                <div class="active"><i>•</i><span>In progress</span></div>
                <div><i>○</i><span>Resolution</span></div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="process">
    <div class="section-title">
        <span>THE PROCESS</span>
        <h2>Everything in one place.</h2>
        <p>A clear full-stack workflow from student submission to administrative resolution.</p>
    </div>

    <div class="feature-grid">
        <article class="feature-card">
            <div class="feature-icon purple">01</div>
            <h3>Register & Login</h3>
            <p>Create a student account and access the protected complaint portal.</p>
        </article>
        <article class="feature-card">
            <div class="feature-icon blue">02</div>
            <h3>Submit Complaint</h3>
            <p>Select a category and provide a clear subject and detailed description.</p>
        </article>
        <article class="feature-card">
            <div class="feature-icon green">03</div>
            <h3>Track Status</h3>
            <p>See whether each complaint is pending, in progress, resolved, or rejected.</p>
        </article>
        <article class="feature-card">
            <div class="feature-icon orange">04</div>
            <h3>Admin Review</h3>
            <p>Administrators manage complaints, categories, statuses, and invalid records.</p>
        </article>
    </div>
</section>

<section class="cta">
    <div>
        <span class="eyebrow white">VOICEBOX</span>
        <h2>Better communication starts with being heard..</h2>
    </div>
    <a class="btn white-btn" href="auth.php">Start Now →</a>
</section>

<?php require 'includes/footer.php'; ?>
