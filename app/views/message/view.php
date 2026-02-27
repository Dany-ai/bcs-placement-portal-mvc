<section class="section section-narrow">
    <div class="card">
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <h2 style="margin:0;"><?= htmlspecialchars($message['subject']) ?></h2>

            <!-- Close button back to dashboard -->
            <a href="<?= $dashboardUrl ?>"
               style="
                   display:inline-block;
                   padding:4px 10px;
                   background:#dc2626;
                   color:#fff;
                   border-radius:6px;
                   font-size:0.8rem;
                   text-decoration:none;
                   font-weight:500;
               ">
                Close ✕
            </a>
        </div>

        <p class="small muted" style="margin-top:0.3rem;">
            <?= htmlspecialchars($message['created_at']) ?>
        </p>

        <p style="margin-top:1rem;">
            <?= nl2br(htmlspecialchars($message['body'])) ?>
        </p>
    </div>
</section>
