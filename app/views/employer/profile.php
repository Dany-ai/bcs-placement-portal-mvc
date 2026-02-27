<section class="section section-narrow">
    <h1>Your Organisation</h1>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" class="form">
        <label>
            Company Name
            <input type="text" name="company_name" value="<?= htmlspecialchars($employer['company_name']) ?>" required>
        </label>
        <label>
            Contact Name
            <input type="text" name="contact_name" value="<?= htmlspecialchars($employer['contact_name']) ?>">
        </label>
        <label>
            Phone Number
            <input type="text" name="phone" value="<?= htmlspecialchars($employer['phone']) ?>">
        </label>
        <label>
            Postal Address
            <textarea name="address" rows="3"><?= htmlspecialchars($employer['address']) ?></textarea>
        </label>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </form>
</section>
