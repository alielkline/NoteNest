<?php
require_once __DIR__ . '/../../config/init.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$subject_id = $_GET['subject_id'];
$classroom_id = $_GET['classroom_id'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Create a new educational note with title, content, and attachments. Share your knowledge with your classroom.">
    <meta name="keywords" content="create note, education, classroom, share content, attachments, learning materials">
    <meta name="author" content="YourProjectName">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="../../public/assets/css/main.css">
    <link rel="stylesheet" href="../../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../../public/assets/css/create_note.css">

    <title>Create Note</title>
</head>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('attachment');
        const fileNameDisplay = document.getElementById('file-name');
        const uploadIcon = document.querySelector('.upload-icon');
        const uploadTitle = document.querySelector('.upload-title');
        const uploadSubtext = document.querySelector('.upload-subtext');

        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                const fileName = fileInput.files[0].name;
                fileNameDisplay.textContent = `Selected: ${fileName}`;
                uploadIcon.innerHTML = '<i class="bi bi-file-earmark-check" style="font-size: 1.5rem; color: green;"></i>';
                uploadTitle.textContent = 'File Attached';
            } else {
                fileNameDisplay.textContent = '';
                uploadIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.6v3a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-3a.5.5 0 0 1 1 0v3a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-3a.5.5 0 0 1 .5-.6z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V10.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>`;
                uploadTitle.textContent = 'Upload Attachments';
                uploadSubtext.textContent = 'Click to browse files';
            }
        });
    });
</script>

<body>
    <?php include '../partials/navbar.php'; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message" id="error-message">
            <div><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="container my-5">
        <div class="row">
            <!-- Note form -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-3">Create a New Note</h2>

                        <form action="../../controllers/NoteController.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject_id) ?>">
                            <input type="hidden" name="classroom_id" value="<?= htmlspecialchars($classroom_id) ?>">
                            <input type="hidden" name="create_note" value="1">
                            <div class="mb-3">
                                <label for="noteTitle" class="form-label">Title</label>
                                <input type="text" class="form-control" id="noteTitle" name="noteTitle" required>
                            </div>

                            <div class="mb-3">
                                <label for="noteContent" class="form-label">Content</label>
                                <textarea class="form-control" id="noteContent" name="noteContent" rows="6" required></textarea>
                                <div class="form-text">[Please fill out this field]</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Visibility</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="visibility" value="public" id="public" checked>
                                    <label class="form-check-label" for="public">Public</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="visibility" value="private" id="private">
                                    <label class="form-check-label" for="private">Private</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="attachment" class="form-label">Attachments</label>
                                <label class="custom-file-upload">
                                    <div class="upload-icon">
                                        <!-- Upload Icon SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                                            <path d="M.5 9.9a.5.5 0 0 1 .5.6v3a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-3a.5.5 0 0 1 1 0v3a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-3a.5.5 0 0 1 .5-.6z" />
                                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V10.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z" />
                                        </svg>
                                    </div>
                                    <div class="upload-title">Upload Attachments</div>
                                    <div class="upload-subtext">Click to browse files</div>
                                    <input type="file" id="attachment" name="attachment">
                                    <div id="file-name" class="mt-2 text-muted" style="font-size: 0.9rem;"></div>
                                </label>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-md btn-purple">Create Note</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <!-- Tips section -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Tips for Great Notes</h4>

                        <div class="mb-3">
                            <h6 class="fw-semibold">Add Rich Content</h6>
                            <p class="text-muted">Use markdown for formatting like <strong>bold</strong>, <em>italic</em>, and <code>code blocks</code>.</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-semibold">Include Visual Content</h6>
                            <p class="text-muted">Add images or diagrams to explain complex topics.</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-semibold">Organize with Headings</h6>
                            <p class="text-muted">Structure your notes with clear sections.</p>
                        </div>

                        <div>
                            <h6 class="fw-semibold">Attach Relevant Files</h6>
                            <p class="text-muted">Include PDFs, code samples, or helpful resources.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include '../partials/footer.php'; ?>