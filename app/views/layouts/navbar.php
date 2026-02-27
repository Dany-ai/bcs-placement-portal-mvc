<?php
// Session::init() already called in header.php
$user = Auth::user();
$role = $user['role'] ?? null;
?>

<header class="site-header">
    <div class="container nav-container">
        <a href="<?= URL_ROOT ?>/" class="logo">
            <span class="logo-mark">BCS</span>
            <span class="logo-text">Placement Portal</span>
        </a>

        <nav class="main-nav">
            <a href="<?= URL_ROOT ?>/">Home</a>
            <a href="<?= URL_ROOT ?>/pages/benefits">Benefits</a>
            <a href="<?= URL_ROOT ?>/placement/index">Placements</a>
            <a href="<?= URL_ROOT ?>/sfia/index">SFIA Skills</a>

            <?php if ($user): ?>
                <?php if ($role === 'student'): ?>
                    <a href="<?= URL_ROOT ?>/student/dashboard">Student Dashboard</a>
                <?php elseif ($role === 'employer'): ?>
                    <a href="<?= URL_ROOT ?>/employer/dashboard">Employer Dashboard</a>
                <?php elseif ($role === 'admin'): ?>
                    <a href="<?= URL_ROOT ?>/admin/dashboard">Admin Dashboard</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>

        <div class="nav-auth">
            <?php if ($user): ?>
                <span class="nav-user">
                    <?= htmlspecialchars($user['email'] ?? '') ?>
                </span>
                <a href="<?= URL_ROOT ?>/auth/logout" class="btn btn-outline">Logout</a>
            <?php else: ?>
                <a href="<?= URL_ROOT ?>/auth/login" class="btn btn-outline">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if (!empty($_flash) && is_array($_flash)): ?>
    <?php
        $type = $_flash['type'] ?? 'info';
        $msg  = $_flash['message'] ?? '';
        $class = $type === 'error' ? 'alert-error' : 'alert-success';
    ?>
    <div class="container" style="margin-top: 1rem;">
        <div class="alert <?= $class ?>"><?= htmlspecialchars($msg) ?></div>
    </div>
<?php endif; ?>