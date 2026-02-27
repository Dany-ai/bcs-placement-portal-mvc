<section class="section section-narrow">
    <h1>Login</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= URL_ROOT ?>/auth/login" class="form">
        <?= $_csrfField ?? '' ?>

        <label>
            Email
            <input type="email" name="email" required>
        </label>

        <label>
            Password
            <input type="password" name="password" required>
        </label>

        <button type="submit" class="btn btn-primary" style="width:100%;">Sign in</button>
    </form>

    <p class="small" style="margin-top:1rem;">
        New here?
        <a href="<?= URL_ROOT ?>/auth/registerStudent">Register as a student</a>
        or
        <a href="<?= URL_ROOT ?>/auth/registerEmployer">register as an employer</a>.
    </p>
</section>