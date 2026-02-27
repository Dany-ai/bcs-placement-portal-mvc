<section class="section placements-padding placement-page-bg">

    <!-- Title in a white box -->
    <div class="placements-title-box">
        <h1>Placement Opportunities</h1>
    </div>

    <!-- 🔍 Filters wrapped in a clean white card -->
    <div class="card" style="padding:1.2rem; margin-bottom:1.5rem;">
        <form method="get" action="<?= URL_ROOT ?>/placement/index" class="form">
            <div class="grid grid-3">
                <label>
                    Keyword
                    <input type="text"
                           name="q"
                           placeholder="Job title or description"
                           value="<?= htmlspecialchars($filters['q'] ?? '') ?>">
                </label>

                <label>
                    Location
                    <input type="text"
                           name="location"
                           placeholder="City or region (e.g. Manchester)"
                           value="<?= htmlspecialchars($filters['location'] ?? '') ?>">
                </label>

                <label>
                    Company
                    <input type="text"
                           name="company"
                           placeholder="Company name"
                           value="<?= htmlspecialchars($filters['company'] ?? '') ?>">
                </label>

                <label>
                    Skills
                    <input type="text"
                           name="skills"
                           placeholder="e.g. Programming, Data analysis"
                           value="<?= htmlspecialchars($filters['skills'] ?? '') ?>">
                </label>
            </div>

            <div class="card-actions">
                <button type="submit" class="btn placement-btn">Apply filters</button>
                <a href="<?= URL_ROOT ?>/placement/index" class="btn placement-btn">Clear filters</a>
            </div>
        </form>
    </div>

    <!-- Results -->
    <?php if (!empty($placements)): ?>
        <div class="grid grid-2">
            <?php
            $currentUser = Auth::user();
            $applicationsByPlacement = $applicationsByPlacement ?? [];
            ?>

            <?php foreach ($placements as $placement): ?>
                <?php
                $placementId = (int)$placement['id'];
                $isStudent   = $currentUser && $currentUser['role'] === 'student';
                $applied     = !empty($applicationsByPlacement[$placementId]);
                ?>

                <article class="card placement-card">
                    <h2><?= htmlspecialchars($placement['title']) ?></h2>
                    <p class="muted">
                        <?= htmlspecialchars($placement['company_name']) ?>
                        &bullet;
                        <?= htmlspecialchars($placement['location']) ?>
                    </p>
                    <p><?= nl2br(htmlspecialchars($placement['description'])) ?></p>

                    <?php if (!empty($placement['skills_required'])): ?>
                        <p><strong>Skills:</strong> <?= htmlspecialchars($placement['skills_required']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($placement['salary'])): ?>
                        <p class="muted"><strong>Salary:</strong> <?= htmlspecialchars($placement['salary']) ?></p>
                    <?php endif; ?>

                    <!-- 🎓 Student Apply Button -->
                    <?php if ($isStudent): ?>
                        <div style="margin-top:1rem;">
                            <?php if ($applied): ?>
                                <span class="badge" style="background:#16a34a; color:#fff; padding:6px 10px;">
                                    ✔ Applied
                                </span>
                            <?php else: ?>
                                <a href="<?= URL_ROOT ?>/student/apply/<?= $placementId ?>"
                                   class="btn placement-btn"
                                   style="padding:6px 12px;">
                                    Apply
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No placements match your filters. Try widening your search.</p>
    <?php endif; ?>

</section>
