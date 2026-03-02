<section class="section section-narrow">
    <h1>Employer Registration</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form">
        <?= $_csrfField ?? '' ?>

        <label>
            Work Email
            <input type="email" name="email" required>
        </label>

        <label>
            Password
            <input type="password" name="password" required>
        </label>

        <label>
            Company Name
            <input type="text" name="company_name" required>
        </label>

        <label>
            Contact Name
            <input type="text" name="contact_name">
        </label>

        <label>
            Phone Number
            <input type="text" name="phone">
        </label>

        <label>
            Postal Address
            <textarea name="address" rows="2"></textarea>
        </label>

        <button type="submit" class="btn btn-primary">Create employer account</button>
    </form>
</section>