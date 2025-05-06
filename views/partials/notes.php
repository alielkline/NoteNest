<?php foreach ($notes as $index => $note): ?>
    <div class="col-12 note-card <?= $index >= 4 ? 'd-none extra-note' : '' ?>">
        <div class="card card-custom p-3 d-flex flex-column position-relative w-100">
            <h5 class="fw-semibold mb-2"><?= htmlspecialchars($note['title']) ?></h5>
            <p class="text-muted mb-3"><?= htmlspecialchars(mb_strimwidth($note['content'], 0, 120, '...')) ?></p>
            <div class="mt-auto d-flex justify-content-between align-items-center text-muted small">
                <span>📅 <?= date('M d, Y', strtotime($note['upload_date'])) ?></span>
                <span>👍 <?= $note['likes'] ?? 0 ?> | 🔖 <?= $note['bookmarkes'] ?? 0 ?></span>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php if (count($notes) > 4): ?>
    <div class="text-center mt-3">
        <button class="btn btn-outline-dark" id="toggle-notes-btn">View More</button>
    </div>
<?php endif; ?>
<?php if (empty($notes)): ?>
    <div class="col-12">
        <div class="alert alert-secondary text-center" role="alert">
            No notes found.
        </div>
    </div>
<?php endif; ?>
