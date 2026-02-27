<section class="section section-narrow">
    <h1>Your Messages</h1>

    <?php if (!empty($unreadCount)): ?>
        <p class="small"><strong><?= (int)$unreadCount ?></strong> unread.</p>
    <?php endif; ?>

    <?php if (!empty($messages)): ?>
        <ul class="list">
            <?php foreach ($messages as $msg): ?>
                <li style="margin-bottom:0.75rem;">
                    <a href="<?= URL_ROOT ?>/message/view/<?= (int)$msg['id'] ?>"
                       style="font-weight:<?= $msg['is_read'] ? 'normal' : 'bold' ?>;">
                        <?= htmlspecialchars($msg['subject']) ?>
                    </a>
                    <div class="small muted">
                        <?= htmlspecialchars($msg['created_at']) ?>
                        <?php if (!$msg['is_read']): ?> • new<?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="muted">You have no messages.</p>
    <?php endif; ?>
</section>
