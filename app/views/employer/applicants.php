<section class="section">
    <h1>Applicants for <?= htmlspecialchars($placement['title']) ?></h1>

    <p class="muted small">
        Location: <?= htmlspecialchars($placement['location']) ?> |
        Salary: <?= htmlspecialchars($placement['salary']) ?>
    </p>

    <?php if (!empty($applicants)): ?>
        <div class="card">
            <table class="table small" style="width:100%;">
                <thead>
                <tr>
                    <th style="text-align:left;">Student</th>
                    <th style="text-align:left;">Email</th>
                    <th style="text-align:left;">Skills</th>
                    <th style="text-align:left;">CV</th>
                    <th style="text-align:left;">Applied</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($applicants as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['name']) ?></td>
                        <td><?= htmlspecialchars($a['email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($a['skills'])) ?></td>
                        <td>
                            <?php if (!empty($a['cv_filename'])): ?>
                                <a href="<?= URL_ROOT ?>/storage/cv/<?= urlencode($a['cv_filename']) ?>"
                                   target="_blank">
                                    View CV
                                </a>
                            <?php else: ?>
                                <span class="muted small">No CV uploaded</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($a['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="card">
            <p>No students have applied for this placement yet.</p>
        </div>
    <?php endif; ?>

    <p style="margin-top:1rem;">
        <a href="<?= URL_ROOT ?>/employer/dashboard" class="btn btn-secondary">
            ← Back to dashboard
        </a>
    </p>
</section>
