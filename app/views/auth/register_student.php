<section class="section section-narrow">
    <h1>Student Registration</h1>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="form">
        <label>
            University Email
            <input type="email" name="email" required>
        </label>
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        <label>
            Full Name
            <input type="text" name="name" required>
        </label>
        <label>
            Phone Number
            <input type="text" name="phone">
        </label>
        <label>
            Postal Address
            <textarea name="address" rows="2"></textarea>
        </label>
        <label>
            Skills (comma-separated, using SFIA terms where possible)
            <textarea name="skills" rows="3" placeholder="e.g. Programming/software development, Data analysis, Testing"></textarea>
        </label>
        <button type="submit" class="btn btn-primary">Create student account</button>
    </form>
</section>
