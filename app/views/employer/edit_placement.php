<section class="section section-narrow">
    <h1>Edit Placement Opportunity</h1>

    <form method="post" action="<?= URL_ROOT ?>/employer/updatePlacement/<?= (int)($placement['id'] ?? 0) ?>" class="form">
        <?= $_csrfField ?? '' ?>

        <label>
            Placement Title
            <input type="text" name="title" value="<?= htmlspecialchars($placement['title'] ?? '') ?>" required>
        </label>

        <label>
            Placement Description
            <textarea name="description" rows="4" required><?= htmlspecialchars($placement['description'] ?? '') ?></textarea>
        </label>

        <label>
            Skills Required (comma-separated, using SFIA terminology)
            <textarea name="skills_required" rows="3" placeholder="e.g. Programming/software development, Systems design, Testing"><?= htmlspecialchars($placement['skills_required'] ?? '') ?></textarea>
        </label>

        <label>
            Salary Offered
            <input type="text" name="salary" value="<?= htmlspecialchars($placement['salary'] ?? '') ?>">
        </label>

        <label>
            Location
            <input type="text" name="location" value="<?= htmlspecialchars($placement['location'] ?? '') ?>">
        </label>

        <div class="grid grid-2">
            <label>
                Preferred Start Date
                <input type="date" name="start_date" value="<?= htmlspecialchars($placement['start_date'] ?? '') ?>">
            </label>

            <label>
                Preferred End Date
                <input type="date" name="end_date" value="<?= htmlspecialchars($placement['end_date'] ?? '') ?>">
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Save changes</button>
    </form>
</section>