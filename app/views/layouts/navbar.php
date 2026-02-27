<?php
// Session::init() was already called in header.php
$user = Auth::user();
?>

<header class="site-header">
    <div class="container nav-container">
        <!-- Logo -->
        <a href="<?= URL_ROOT ?>/" class="logo">
            <span class="logo-mark">BCS</span>
            <span class="logo-text">Placement Portal</span>
        </a>

        <!-- Main nav links -->
        <nav class="main-nav">
            <a href="<?= URL_ROOT ?>/">Home</a>
            <a href="<?= URL_ROOT ?>/pages/benefits">Benefits</a>
            <a href="<?= URL_ROOT ?>/placement/index">Placements</a>
            <a href="<?= URL_ROOT ?>/sfia/index">SFIA Skills</a>

            <?php if ($user): ?>
                <?php $role = $user['role'] ?? null; ?>

                <?php if ($role === 'student'): ?>
                    <a href="<?= URL_ROOT ?>/student/dashboard">Student Dashboard</a>

                <?php elseif ($role === 'employer'): ?>
                    <a href="<?= URL_ROOT ?>/employer/dashboard">Employer Dashboard</a>

                <?php elseif ($role === 'admin'): ?>
                    <a href="<?= URL_ROOT ?>/admin/dashboard">Admin Dashboard</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>

        <!-- Right side auth area -->
        <div class="nav-auth">
            <?php if ($user): ?>
                <span class="nav-user">
                    <?= htmlspecialchars($user['email']) ?>
                </span>
                <a href="<?= URL_ROOT ?>/auth/logout" class="btn btn-outline">Logout</a>
            <?php else: ?>
                <a href="<?= URL_ROOT ?>/auth/login" class="btn btn-outline">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>
