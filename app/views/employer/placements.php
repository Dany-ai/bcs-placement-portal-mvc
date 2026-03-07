<section class="section">
    <h1>Your Placement Opportunities</h1>

    <?php if (!empty($placements)): ?>
        <div class="grid grid-2">
            <?php foreach ($placements as $placement): ?>
                <article class="card placement-card">
                    <h2><?= htmlspecialchars($placement['title'] ?? '') ?></h2>
                    <p class="muted"><?= htmlspecialchars($placement['location'] ?? '') ?></p>
                    <p><?= nl2br(htmlspecialchars(substr($placement['description'] ?? '', 0, 200))) ?>...</p>
                    <p><strong>Skills:</strong> <?= htmlspecialchars($placement['skills_required'] ?? '') ?></p>
                    <p><strong>Salary:</strong> <?= htmlspecialchars($placement['salary'] ?? '') ?></p>

                    <?php if (!empty($placement['status'])): ?>
                        <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($placement['status'])) ?></p>
                    <?php endif; ?>

                    <div style="display:flex; gap:0.5rem; margin-top:1rem; flex-wrap:wrap;">
                        <a href="<?= URL_ROOT ?>/employer/editPlacement/<?= (int)($placement['id'] ?? 0) ?>" class="btn btn-secondary btn-sm">
                            Edit
                        </a>

                        <a href="<?= URL_ROOT ?>/employer/applicants/<?= (int)($placement['id'] ?? 0) ?>" class="btn btn-outline btn-sm">
                            View Applicants
                        </a>

                        <form method="post" action="<?= URL_ROOT ?>/employer/deletePlacement/<?= (int)($placement['id'] ?? 0) ?>" style="display:inline;">
                            <?= $_csrfField ?? '' ?>
                            <button
                                type="submit"
                                class="btn btn-delete btn-sm"
                                onclick="return confirm('Are you sure you want to delete this placement?');">
                                Delete
                            </button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>You have not published any placements yet.</p>
    <?php endif; ?>
</section>