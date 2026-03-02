<section class="section section-narrow">
    <div class="card">
        <h1>Create Career Support Account</h1>
        <p class="muted small">
            This account can provide career advice, review CVs, and chat with students.
        </p>

        <?php if (!empty($error)): ?>
            <p class="error" style="color:#b91c1c; font-weight:600;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="post" action="<?= URL_ROOT ?>/auth/registerCareer">
            <?= $_csrfField ?? '' ?>

            <div class="form-field">
                <label>Email <span style="color:red">*</span></label>
                <input type="email" name="email" required>
            </div>

            <div class="form-field">
                <label>Password <span style="color:red">*</span></label>
                <input type="password" name="password" required>
            </div>

            <div class="form-field">
                <label>Full Name (optional)</label>
                <input type="text" name="name" placeholder="E.g. Jane Doe">
            </div>

            <button type="submit" class="btn btn-primary">Create Account</button>
            <a href="<?= URL_ROOT ?>/auth/login" class="btn btn-secondary">Back to Login</a>
        </form>
    </div>
</section>