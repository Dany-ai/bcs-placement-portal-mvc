<?php
// app/views/home.php
?>

<div class="home-padding">

    <section class="hero">
        <div class="hero-text">

            <!-- Title with forced line break -->
            <h1>Connect Computing Students with<br>Year-long Placements</h1>

            <!-- Hero image under the title -->
            <div class="hero-image-container">
                <img src="<?= URL_ROOT ?>/assets/images/pexels-fauxels-3184306.jpg"
                     alt="Students collaborating in a meeting"
                     class="hero-image">
            </div>

            <p>
                The BCS Manchester Placement Portal helps match computer science students
                with employers offering year-long industrial placements.
            </p>
        </div>

        <div class="hero-highlights">
            <div class="card">
                <h3>For Students</h3>
                <p>Gain real-world experience, improve your final-year performance, and boost your employability.</p>
                <div class="card-actions">
                    <a href="<?= URL_ROOT ?>/auth/registerStudent" class="btn btn-primary">
                        I'm a Student
                    </a>
                </div>
            </div>

            <div class="card">
                <h3>For Employers</h3>
                <p>Access motivated students with SFIA-aligned skills to support your technology projects.</p>
                <div class="card-actions">
                    <a href="<?= URL_ROOT ?>/auth/registerEmployer" class="btn btn-secondary">
                        I'm an Employer
                    </a>
                </div>
            </div>

            <div class="card">
                <h3>For Career Support</h3>
                <p>Help students with CV feedback, applications, and career guidance for successful placements.</p>
                <div class="card-actions">
                    <a href="<?= URL_ROOT ?>/auth/registerCareer" class="btn btn-outline">
                        I'm Career Support
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <h2>Latest Placement Opportunities</h2>

        <?php if (!empty($placements)): ?>
            <div class="grid grid-3">
                <?php foreach ($placements as $placement): ?>
                    <article class="card placement-card">
                        <h3><?= htmlspecialchars($placement['title']) ?></h3>
                        <p class="muted">
                            <?= htmlspecialchars($placement['company_name']) ?>
                            &bullet;
                            <?= htmlspecialchars($placement['location']) ?>
                        </p>
                        <p><?= nl2br(htmlspecialchars(substr($placement['description'], 0, 160))) ?>...</p>
                        <p class="tagline">Skills: <?= htmlspecialchars($placement['skills_required']) ?></p>
                        <p class="muted">Salary: <?= htmlspecialchars($placement['salary']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>
                No placements have been published yet. Employers can
                <a href="<?= URL_ROOT ?>/auth/registerEmployer">create an account</a>
                to add opportunities.
            </p>
        <?php endif; ?>
    </section>

</div>
