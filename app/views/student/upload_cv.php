<section class="section section-narrow">
    <h1>Upload CV</h1>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <p>Please upload your CV in PDF format. Careers staff and employers may review it when considering you for placements.</p>
    <form method="post" enctype="multipart/form-data" class="form">
        <label>
            Select PDF file
            <input type="file" name="cv" accept="application/pdf" required>
        </label>
        <button type="submit" class="btn btn-primary">Upload CV</button>
    </form>
</section>
