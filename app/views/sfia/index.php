<section class="section skills-padding">

    <!-- Title box (matches Benefits page) -->
    <div class="skills-title-box">
        <h1 class="skills-title">SFIA Skills Overview</h1>
    </div>

    <?php
    // Descriptions + importance text for each skill code
    $skillDetails = [
            'ANLY' => [
                    'description' => 'Analyses data from multiple sources to identify patterns, trends, and insights that support decision-making.',
                    'importance'  => 'Data analysis underpins evidence-based decisions, reporting, and many analytics and AI solutions.'
            ],
            'BUAN' => [
                    'description' => 'Works with stakeholders to understand business needs and translate them into clear, testable requirements.',
                    'importance'  => 'Good business analysis ensures that solutions solve the right problem and deliver real value.'
            ],
            'DESN' => [
                    'description' => 'Designs systems, components, and user journeys that meet functional and non-functional requirements.',
                    'importance'  => 'Strong design skills lead to systems that are maintainable, secure, and user-friendly.'
            ],
            'PROG' => [
                    'description' => 'Plans, writes, tests, and maintains code using appropriate languages, tools, and practices.',
                    'importance'  => 'Programming/software development is the core skill for building and evolving digital products and services.'
            ],
            'TEST' => [
                    'description' => 'Prepares and executes tests to verify that systems behave as expected and meet quality standards.',
                    'importance'  => 'Testing reduces defects, improves reliability, and protects users and organisations from failures.'
            ],
            'RLMT' => [
                    'description' => 'Builds and maintains productive relationships with stakeholders, customers, and partners.',
                    'importance'  => 'Strong relationship management keeps projects aligned with stakeholder needs and improves collaboration.'
            ],
    ];
    ?>

    <?php if (!empty($skills)): ?>
        <div class="skills-accordion">
            <?php foreach ($skills as $skill): ?>
                <?php
                $code    = htmlspecialchars($skill['code']);
                $name    = htmlspecialchars($skill['name']);
                $details = $skillDetails[$skill['code']] ?? null;
                ?>
                <details class="skill-item">
                    <summary>
                        <span class="skill-text">
                            <span class="skill-code"><?= $code ?></span>
                            <span class="skill-name"><?= $name ?></span>
                        </span>
                        <span class="skill-toggle">+</span>
                    </summary>

                    <div class="skill-body">
                        <?php if ($details): ?>
                            <p><?= htmlspecialchars($details['description']) ?></p>
                            <p>
                                <strong>Why this skill matters: </strong>
                                <?= htmlspecialchars($details['importance']) ?>
                            </p>
                        <?php else: ?>
                            <p>A description for this skill will be added soon.</p>
                        <?php endif; ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No SFIA skills loaded yet.</p>
    <?php endif; ?>

</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.skill-item').forEach(function (item) {
            const toggle = item.querySelector('.skill-toggle');

            function updateToggle() {
                if (toggle) {
                    toggle.textContent = item.open ? '–' : '+';
                }
            }

            item.addEventListener('toggle', updateToggle);
            updateToggle();
        });
    });
</script>
