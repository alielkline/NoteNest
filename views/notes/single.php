<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/main.css">
    <link rel="stylesheet" href="../../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../../public/assets/css/single_note.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <title>Note</title>
</head>

<body>
    <?php
    require_once __DIR__ . '/../../controllers/NoteController.php';
    $controller = new NoteController();
    $data = $controller->loadNote();
    $note = $data['note'];
    $userHasLiked = $data['userHasLiked'];
    $userHasBookmarked = $data['userHasBookmarked'];
    $comments = $data['comments'];

    $d = strtotime($note['upload_date']);
    $date = date("M d, Y, h:i A", $d);
    $heartIconClass = $userHasLiked ? "bi-heart-fill" : "bi-heart";
    $bookmarkIconClass = $userHasBookmarked ? "bi-bookmark-fill" : "bi-bookmark";

    if (!$note['profile_image']) {
        $note['profile_image'] = 'profile-default.jpg';
    }

    $fileSegment = '';
    if (!empty($note['attachment'])) {
        $attachmentFileName = basename($note['attachment']);
        $attachmentPath = __DIR__ . '/../../public/uploads/attachments/' . $attachmentFileName;
        $fileSize = file_exists($attachmentPath) ? round(filesize($attachmentPath) / 1000000, 3) : "Unknown";
        $fileName = $attachmentFileName;
        $displayName = strlen($fileName) > 30 ? substr($fileName, 0, 10) . "..." : $fileName;

        ob_start(); ?>
        <p class='attachment-title'>Attachments</p>
        <div class='d-flex justify-content-between download-bg'>
            <div class='d-flex'>
                <div class='download-icon-bg p-2 px-3 m-3'>
                    <i class='bi bi-book'></i>
                </div>
                <div>
                    <p class='mt-3 mb-0 attachment-filename'><?= htmlspecialchars($displayName) ?></p>
                    <p class='text-muted'><?= htmlspecialchars($fileSize) ?> MB</p>
                </div>
            </div>
            <div>
                <a class='btn download-btn m-4' href='../../public/uploads/attachments/<?= htmlspecialchars($fileName) ?>' download>Download</a>
            </div>
        </div>
    <?php
        $fileSegment = ob_get_clean();
    }
    ?>

    <?php include '../partials/navbar.php'; ?>

    <div class="main-content p-4 w-100 container">
        <div class="row mt-5">
            <div class="col-12 col-md-8 mb-4">
                <div class="note-pane border">
                    <div class='d-flex justify-content-between align-items-center'>
                        <div class='d-flex'>
                            <img class='single-note-pfp' src='../../public/uploads/profile_pictures/<?= htmlspecialchars($note['profile_image']) ?>'>
                            <div>
                                <p class='my-0'><?= htmlspecialchars($note['username']) ?></p>
                                <p class='text-muted date'><?= htmlspecialchars($date) ?></p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">


                            <div class='d-flex align-items-center'>
                                <span id='like-btn' data-note-id='<?= $note['note_id'] ?>' onclick='toggleLike(this)' class='d-flex align-items-center px-2 like-button'>
                                    <i class='bi <?= $heartIconClass ?> mx-1 like-heart'></i>
                                    <span class='mx-1 note-likes' id='like-count'><?= htmlspecialchars($note['likes']) ?></span>
                                </span>
                                <span id='bookmark-btn' data-note-id='<?= $note['note_id'] ?>' onclick='toggleBookmark(this)' class='mx-2 p-2 bookmark-button'>
                                    <i class='bi <?= $bookmarkIconClass ?> bookmark-icon d-flex align-items-center'></i>
                                </span>


                            </div>

                        </div>
                    </div>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mt-3">
                        <h1 class="note-title mb-0 text-break" style="word-break: break-word; max-width: 100%;">
                            <?= htmlspecialchars($note['title']) ?>
                        </h1>

                        <?php if ($note['uploader_user_id'] == $_SESSION['user_id']): ?>
                            <span class="d-flex align-items-center mt-2 mt-md-0">
                                <a href="/NoteNest/views/notes/update_note.php?note_id=<?= $note['note_id'] ?>">
                                    <button class="btn btn-secondary">Edit</button>
                                </a>
                            </span>
                        <?php endif; ?>
                    </div>

                    <p class='subject-pill px-2 py-1 mt-2'><?= htmlspecialchars($note['subject_name']) ?></p>
                    <p><?= htmlspecialchars($note['content']) ?></p>
                    <div><?= $fileSegment ?></div>
                </div>
            </div>

            <div class="col-12 col-md-4 note-pane border">
                <span class="d-flex align-items-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-chat-left comments-icon" viewBox="0 0 16 16">
                        <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                    </svg>
                    <p class='px-2 my-0 comments-text'>Comments (<?= count($comments) ?>)</p>
                </span>
                <form method="post" class="form-group" action="../../controllers/NoteController.php">
                    <input type="hidden" name="action" value="add_comment">
                    <input type="hidden" name="note_id" value="<?= $note['note_id'] ?>">
                    <textarea class="form-control w-100 comment-textarea" name="content" rows="3" placeholder="Write a comment..." required></textarea>
                    <button type="submit" class="btn-add-comment mt-2">Add Comment</button>
                </form>
                <hr class="text-black-50">
                <?php foreach ($comments as $comment): ?>
                    <?php
                    $d = strtotime($comment['comment_date']);
                    $date = date("M d, Y, h:i A", $d);
                    ?>
                    <div class='comment-pane'>
                        <div class='d-flex p-2'>
                            <img class='comment-pfp' src='../../public/uploads/profile_pictures/<?= htmlspecialchars($comment['profile_image']) ?>'>
                            <div>
                                <p class='my-0'><?= htmlspecialchars($comment['username']) ?></p>
                                <p class='text-muted date mb-0'><?= htmlspecialchars($date) ?></p>
                            </div>
                        </div>
                        <p class='p-3 pb-2 pt-0'><?= htmlspecialchars($comment['comment_text']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/single_note.js"></script>
</body>

</html>