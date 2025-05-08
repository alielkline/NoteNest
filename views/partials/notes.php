<div id="notes-container">
    <?php if (empty($notes)): ?>
        <div class="col-12">
            <div class="alert alert-secondary text-center" role="alert">
                No notes found.
            </div>
        </div>
    <?php endif; ?>
    <?php foreach ($notes as $index => $note): ?>
        <a href="../notes/single.php?note_id=<?php $note["note_id"] ?>" class="text-decoration-none text-reset">
            <div class="col-12 mb-3 note-card <?= $index >= 4 ? 'd-none extra-note' : '' ?>">
                <div class="card card-custom p-3 d-flex flex-column position-relative w-100">
                    <h5 class="fw-semibold mb-2"><?= htmlspecialchars($note['title']) ?></h5>
                    <p class="text-muted mb-3"><?= htmlspecialchars(mb_strimwidth($note['content'], 0, 60, '...')) ?></p>
                    <div class="mt-auto d-flex justify-content-between align-items-center text-muted small">
                        <span>📅 <?= date('M d, Y', strtotime($note['upload_date'])) ?></span>
                        <span><i class="bi bi-heart-fill like-heart me-1 purple-icon"></i> <?= $note['likes'] ?? 0 ?> </span>
                    </div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
    <?php if (count($notes) > 4): ?>
        <div class="text-center mt-3">
            <button class="btn veiw-more-btn" id="toggle-notes-btn">View More</button>
        </div>
    <?php endif; ?>
</div>