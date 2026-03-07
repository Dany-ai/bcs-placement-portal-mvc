<section class="section admin-dashboard-padding">

    <div class="admin-title-box">
        <h1 class="admin-title">Admin Dashboard</h1>
    </div>

    <div class="card">
        <h2>Pending Placements</h2>

        <?php if (!empty($pendingPlacements)): ?>
            <ul class="list">
                <?php foreach ($pendingPlacements as $p): ?>
                    <li>
                        <strong><?= htmlspecialchars($p['title'] ?? '') ?></strong>
                        (<?= htmlspecialchars($p['company_name'] ?? '') ?>, <?= htmlspecialchars($p['location'] ?? '') ?>)<br>

                        <span class="muted small">
                            Submitted: <?= htmlspecialchars($p['created_at'] ?? '') ?>
                        </span><br>

                        <span class="small">
                            <?= nl2br(htmlspecialchars(substr($p['description'] ?? '', 0, 120))) ?>
                            <?php if (strlen($p['description'] ?? '') > 120): ?>...<?php endif; ?>
                        </span>

                        <div class="card-actions" style="margin-top:0.5rem;">
                            <form method="post" action="<?= URL_ROOT ?>/admin/approvePlacement/<?= (int)($p['id'] ?? 0) ?>" style="display:inline;">
                                <?= $_csrfField ?? '' ?>
                                <button type="submit" class="btn btn-secondary">
                                    Approve
                                </button>
                            </form>

                            <form
                                method="post"
                                action="<?= URL_ROOT ?>/admin/rejectPlacement/<?= (int)($p['id'] ?? 0) ?>"
                                style="display:inline;"
                                onsubmit="return confirm('Are you sure you want to reject this placement?');">
                                <?= $_csrfField ?? '' ?>
                                <button type="submit" class="btn btn-delete">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No pending placements right now.</p>
        <?php endif; ?>
    </div>

    <div class="card" style="margin-top:1.5rem;">
        <h2>Career Support – Student Chats</h2>
        <p class="small">
            As a career support/admin user, you can open a chat with any student to:
        </p>

        <ul class="small">
            <li>Give feedback on their CV and applications.</li>
            <li>Answer questions about placements and requirements.</li>
            <li>Guide them on skills, SFIA levels and career direction.</li>
        </ul>

        <form method="get" style="margin-top:0.75rem; margin-bottom:0.75rem; display:flex; gap:0.5rem; max-width:400px;">
            <input
                type="text"
                name="q"
                placeholder="Search students by name or email..."
                value="<?= htmlspecialchars($searchTerm ?? '') ?>"
                style="flex:1; padding:6px 8px; border-radius:6px; border:1px solid #d1d5db;"
            >
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>

        <?php if (!empty($students)): ?>
            <table class="table small" style="margin-top:0.5rem; width:100%;">
                <thead>
                    <tr>
                        <th style="text-align:left;">Student</th>
                        <th style="text-align:left;">Email</th>
                        <th style="text-align:right;">Chat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['email'] ?? '') ?></td>
                            <td style="text-align:right;">
                                <a
                                    href="<?= URL_ROOT ?>/admin/chatStudent/<?= (int)($s['user_id'] ?? 0) ?>"
                                    class="btn btn-primary btn-sm"
                                    style="position:relative; padding-right:28px;">
                                    Open chat

                                    <?php if (!empty($s['unread_from_student'])): ?>
                                        <span
                                            style="
                                                position:absolute;
                                                top:50%;
                                                right:8px;
                                                transform:translateY(-50%);
                                                width:10px;
                                                height:10px;
                                                background:#dc2626;
                                                border-radius:999px;
                                            "
                                            title="New messages from this student"
                                        ></span>
                                    <?php endif; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="muted small" style="margin-top:0.75rem;">
                There are no students matching this search. Try clearing the search box.
            </p>
        <?php endif; ?>
    </div>

</section>