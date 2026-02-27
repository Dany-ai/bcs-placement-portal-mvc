<section class="section section-narrow">
    <h1>Your Profile</h1>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="post" class="form">
        <label>
            Full Name
            <input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
        </label>
        <label>
            Phone Number
            <input type="text" name="phone" value="<?= htmlspecialchars($student['phone']) ?>">
        </label>
        <label>
            Postal Address
            <textarea name="address" rows="2"><?= htmlspecialchars($student['address']) ?></textarea>
        </label>
        <label>
            Skills (comma-separated, using SFIA terms where possible)
            <textarea name="skills" rows="3"><?= htmlspecialchars($student['skills']) ?></textarea>
        </label>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </form>
</section>
