<section class="section section-narrow">
    <h1>Create Placement Opportunity</h1>

    <form method="post" action="<?= URL_ROOT ?>/employer/storePlacement" class="form">
        <?= $_csrfField ?? '' ?>

        <label>
            Placement Title
            <input type="text" name="title" required>
        </label>

        <label>
            Placement Description
            <textarea name="description" rows="4" required></textarea>
        </label>

        <label>
            Skills Required (comma-separated, using SFIA terminology)
            <textarea name="skills_required" rows="3" placeholder="e.g. Programming/software development, Systems design, Testing"></textarea>
        </label>

        <label>
            Salary Offered
            <input type="text" name="salary" placeholder="e.g. £18,000 per annum">
        </label>

        <label>
            Location
            <input type="text" name="location" placeholder="e.g. Manchester, hybrid">
        </label>

        <div class="grid grid-2">
            <label>
                Preferred Start Date
                <input type="date" name="start_date">
            </label>

            <label>
                Preferred End Date
                <input type="date" name="end_date">
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Publish placement</button>
    </form>
</section>