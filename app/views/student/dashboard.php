<section class="section student-dashboard-padding">

    <!-- Title Box -->
    <div class="student-title-box">
        <h1 class="student-title">Student Dashboard</h1>
    </div>

    <div class="grid grid-2">
        <div>
            <div class="card">
                <h2>Your Profile</h2>
                <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars(Auth::user()['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($student['phone']) ?></p>
                <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($student['address'])) ?></p>
                <p><strong>Skills:</strong> <?= htmlspecialchars($student['skills']) ?></p>
                <p><strong>CV:</strong>
                    <?php if (!empty($student['cv_filename'])): ?>
                        Uploaded
                    <?php else: ?>
                        Not uploaded
                    <?php endif; ?>
                </p>
                <div class="card-actions">
                    <a href="<?= URL_ROOT ?>/student/profile" class="btn btn-primary">Edit profile</a>
                    <a href="<?= URL_ROOT ?>/student/uploadCv" class="btn btn-primary">Upload CV (PDF)</a>
                </div>
            </div>

            <!-- Messages Card -->
            <div class="card" style="margin-top:1.5rem;">
                <h2 style="display:flex; align-items:center; gap:0.5rem;">
                    Your Messages
                    <?php if (!empty($unreadCount)): ?>
                        <span class="badge" style="background:#dc2626; color:#fff;">
                            New: <?= (int)$unreadCount ?>
                        </span>
                    <?php endif; ?>
                </h2>

                <?php if (!empty($messages)): ?>
                    <ul class="list">
                        <?php foreach ($messages as $message): ?>
                            <?php $isUnread = ((int)$message['is_read'] === 0); ?>
                            <li <?= $isUnread ? 'style="background:#fee2e2; border-left:4px solid #dc2626;"' : '' ?>>
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:0.75rem;">
                                    <div>
                                        <strong style="<?= $isUnread ? 'color:#b91c1c; font-weight:700;' : '' ?>">
                                            <?= htmlspecialchars($message['subject']) ?>
                                        </strong><br>

                                        <span class="muted small" style="<?= $isUnread ? 'color:#991b1b;' : '' ?>">
                                            <?= htmlspecialchars($message['created_at']) ?>
                                        </span><br>

                                        <span class="small">
                                            <?= nl2br(htmlspecialchars(substr($message['body'], 0, 80))) ?>
                                            <?php if (strlen($message['body']) > 80): ?>...<?php endif; ?>
                                        </span>
                                    </div>

                                    <div style="text-align:right;">
                                        <?php if ($isUnread): ?>
                                            <span class="badge" style="background:#dc2626; color:#fff;">New</span><br>
                                        <?php endif; ?>
                                        <a href="<?= URL_ROOT ?>/student/message/<?= (int)$message['id'] ?>"
                                           class="btn btn-primary btn-sm"
                                           style="margin-top:0.25rem;">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                <?php else: ?>
                    <p class="muted small">You have no messages yet.</p>
                <?php endif; ?>
            </div>

            <!-- Career Support Chat -->
            <div class="card" style="margin-top:1.5rem;">
                <h2>Career Support</h2>
                <p class="small">
                    Need help with your CV, applications, or choosing placements?
                    You can chat with the careers / placement support team directly.
                </p>

                <?php if (!empty($careerChatCount)): ?>
                    <p class="muted small">
                        You already have <strong><?= (int)$careerChatCount ?></strong> message(s) in your chat.
                    </p>
                <?php else: ?>
                    <p class="muted small">You haven't started a chat yet. Click below to begin.</p>
                <?php endif; ?>

                <a href="<?= URL_ROOT ?>/student/chatCareerSupport" class="btn btn-primary">
                    Open chat with Career Support
                </a>
            </div>
        </div>

        <div>
            <div class="card">
                <h2>Your Matches</h2>
                <?php if (!empty($matches)): ?>
                    <ul class="list">
                        <?php foreach ($matches as $match): ?>
                            <li>
                                <strong><?= htmlspecialchars($match['title']) ?></strong>
                                (<?= htmlspecialchars($match['company_name']) ?>, <?= htmlspecialchars($match['location']) ?>)
                                <span class="badge">Match: <?= (int)$match['score'] ?>%</span><br>
                                <span class="muted">Salary: <?= htmlspecialchars($match['salary']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No matches yet.</p>
                <?php endif; ?>

                <p class="muted small">
                    Matching uses SFIA-style skills to compare your profile with placement descriptions.
                </p>
            </div>
        </div>
    </div>
</section>
