<section class="section">
    <h1>Your Placement Opportunities</h1>
    <?php if (!empty($placements)): ?>
        <div class="grid grid-2">
            <?php foreach ($placements as $placement): ?>
                <article class="card placement-card">
                    <h2><?= htmlspecialchars($placement['title']) ?></h2>
                    <p class="muted"><?= htmlspecialchars($placement['location']) ?></p>
                    <p><?= nl2br(htmlspecialchars(substr($placement['description'], 0, 200))) ?>...</p>
                    <p><strong>Skills:</strong> <?= htmlspecialchars($placement['skills_required']) ?></p>
                    <p><strong>Salary:</strong> <?= htmlspecialchars($placement['salary']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>You have not published any placements yet.</p>
    <?php endif; ?>
</section>
