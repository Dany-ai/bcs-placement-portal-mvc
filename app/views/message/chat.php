<section class="section section-narrow">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
            <h2 style="margin:0;">
                Chat with <?= htmlspecialchars($otherUser['name'] ?? 'Career Support') ?>
            </h2>
            <a href="<?= $backUrl ?>"
               style="font-size:0.8rem; text-decoration:none; padding:4px 10px; border-radius:6px; background:#e5e7eb;">
                ← Back
            </a>
        </div>

        <?php if (!empty($error)): ?>
            <p class="error" style="color:#b91c1c; font-weight:600;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <div id="chat-messages" style="
            max-height:300px;
            overflow-y:auto;
            border:1px solid #e5e7eb;
            border-radius:6px;
            padding:0.75rem;
            margin-bottom:0.75rem;
            background:#f9fafb;
        ">
            <?php if (!empty($conversation)): ?>
                <?php foreach ($conversation as $msg): ?>
                    <?php $isMe = ($msg['sender_user_id'] == $currentUser['id']); ?>
                    <div style="margin-bottom:0.5rem; text-align:<?= $isMe ? 'right' : 'left' ?>;">
                        <div style="
                                display:inline-block;
                                padding:6px 10px;
                                border-radius:12px;
                                background:<?= $isMe ? '#2563eb' : '#e5e7eb' ?>;
                                color:<?= $isMe ? '#fff' : '#111827' ?>;
                                max-width:80%;
                                text-align:left;
                                font-size:0.9rem;
                                ">
                            <?= nl2br(htmlspecialchars($msg['body'])) ?>
                        </div>
                        <div class="small muted" style="margin-top:2px;">
                            <?= htmlspecialchars($msg['created_at']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="muted small">No messages yet. Start the conversation below.</p>
            <?php endif; ?>
        </div>

        <form method="post">
            <label style="display:block; margin-bottom:0.5rem;">
                <span class="small muted">Type your message</span>
                <textarea name="body" rows="3" style="width:100%;"></textarea>
            </label>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
    </div>
</section>

<script>
    // Simple auto-refresh chat every 5 seconds.
    // Skips reload if the user is currently typing in an input/textarea.
    (function () {
        setInterval(function () {
            var active = document.activeElement;
            if (active && (active.tagName === 'TEXTAREA' || active.tagName === 'INPUT')) {
                return; // don't interrupt typing
            }
            window.location.reload();
        }, 5000);
    })();
</script>
