<section class="section employer-dashboard-padding">

    <!-- Title Box -->
    <div class="employer-title-box">
        <h1 class="employer-title">Employer Dashboard</h1>
    </div>

    <div class="grid grid-2">
        <div>
            <div class="card">
                <h2>Your Organisation</h2>
                <p><strong>Company:</strong> <?= htmlspecialchars($employer['company_name']) ?></p>
                <p><strong>Contact:</strong> <?= htmlspecialchars($employer['contact_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars(Auth::user()['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($employer['phone']) ?></p>
                <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($employer['address'])) ?></p>
                <p class="muted small">
                    Use SFIA-style skill descriptions in your placement adverts to help find the best student matches.
                </p>
                <div class="card-actions">
                    <a href="<?= URL_ROOT ?>/employer/profile" class="btn btn-primary">Edit organisation</a>
                    <a href="<?= URL_ROOT ?>/employer/createPlacement" class="btn btn-primary">Create placement</a>
                </div>
            </div>

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

                                        <a href="<?= URL_ROOT ?>/employer/message/<?= (int)$message['id'] ?>"
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
        </div>

        <div>
            <div class="card">
                <h2>Your Placements</h2>

                <?php if (!empty($placements)): ?>
                    <ul class="placement-list">
                        <?php foreach ($placements as $placement): ?>
                            <li class="placement-list-item">

                                <div>
                                    <strong><?= htmlspecialchars($placement['title']) ?></strong>
                                    (<?= htmlspecialchars($placement['location']) ?>)<br>
                                    <span class="muted">Salary: <?= htmlspecialchars($placement['salary']) ?></span>
                                </div>

                                <div class="small" style="margin-top:0.25rem; display:flex; gap:0.4rem;">

                                    <a href="<?= URL_ROOT ?>/employer/editPlacement/<?= (int)$placement['id'] ?>"
                                       style="
                                           display:inline-block;
                                           padding:2px 10px;
                                           border-radius:6px;
                                           background:#16a34a;
                                           color:#ffffff;
                                           font-size:0.78rem;
                                           font-weight:500;
                                           text-decoration:none;
                                           border:1px solid #15803d;
                                       ">
                                        Edit
                                    </a>

                                    <form method="post"
                                          action="<?= URL_ROOT ?>/employer/deletePlacement/<?= (int)$placement['id'] ?>"
                                          style="display:inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this placement?');">
                                        <button type="submit"
                                                style="
                                                    display:inline-block;
                                                    padding:2px 10px;
                                                    border-radius:6px;
                                                    background:#dc2626;
                                                    color:#ffffff;
                                                    font-size:0.78rem;
                                                    font-weight:500;
                                                    border:1px solid #b91c1c;
                                                    cursor:pointer;
                                                ">
                                            Delete
                                        </button>
                                    </form>

                                    <a href="<?= URL_ROOT ?>/employer/applicants/<?= (int)$placement['id'] ?>"
                                       style="
                                           display:inline-block;
                                           padding:2px 10px;
                                           border-radius:6px;
                                           background:#2563eb;
                                           color:#ffffff;
                                           font-size:0.78rem;
                                           font-weight:500;
                                           text-decoration:none;
                                           border:1px solid #1d4ed8;
                                       ">
                                        View applicants
                                    </a>

                                </div>

                            </li>
                        <?php endforeach; ?>
                    </ul>

                <?php else: ?>
                    <p>You have not published any placements yet.</p>
                <?php endif; ?>

            </div>
        </div>

    </div>

</section>
