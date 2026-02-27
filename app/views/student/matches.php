<section class="section">
    <h1>Your Matches</h1>

    <div class="card">
        <h2>Recommended Placements</h2>

        <?php if (!empty($matches)): ?>
            <ul class="list">
                <?php foreach ($matches as $match): ?>
                    <?php
                    // Try to get a placement id: either 'placement_id' or 'id'
                    $placementId = isset($match['placement_id'])
                            ? (int)$match['placement_id']
                            : (int)$match['id'];

                    $applied = !empty($applicationsByPlacement[$placementId]);
                    ?>
                    <li>
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:0.75rem;">
                            <div>
                                <strong><?= htmlspecialchars($match['title']) ?></strong>
                                (<?= htmlspecialchars($match['company_name']) ?>,
                                <?= htmlspecialchars($match['location']) ?>)
                                <span class="badge">Match: <?= (int)$match['score'] ?>%</span><br>
                                <span class="muted">Salary: <?= htmlspecialchars($match['salary']) ?></span><br>
                                <span class="small">
                                    Skills required: <?= htmlspecialchars($match['skills_required'] ?? '') ?>
                                </span>
                            </div>

                            <div style="text-align:right;">
                                <?php if ($applied): ?>
                                    <span class="badge"
                                          style="background:#16a34a; color:#fff; margin-bottom:0.25rem; display:inline-block;">
                                        Applied
                                    </span><br>
                                <?php else: ?>
                                    <a href="<?= URL_ROOT ?>/student/apply/<?= $placementId ?>"
                                       class="btn btn-primary btn-sm"
                                       style="margin-bottom:0.25rem;">
                                        Apply
                                    </a><br>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No matches yet. Once placements are published and matching is run, you'll see recommended opportunities here.</p>
        <?php endif; ?>

        <p class="muted small">
            Matching uses overlapping SFIA-style skills between your profile and placement descriptions.
        </p>
    </div>
</section>
