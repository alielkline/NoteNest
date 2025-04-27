<?php
include '../includes/init.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note_title = $_POST['noteTitle'];
    $note_content = $_POST['noteContent'];
    $visibility = $_POST['visibility'];
    $comments = $_POST['comments'];

    // Handle file upload
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $file_name = basename($_FILES['attachment']['name']);
        $file_path = $upload_dir . $file_name;

        // Move uploaded file to the server
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $file_path)) {
            $attachment = $file_path;
        } else {
            $attachment = null;
        }
    } else {
        $attachment = null;
    }

    // Insert the new note into the database
    $stmt = $pdo->prepare("INSERT INTO classroom_notes (user_id, title, content, visibility, attachment) 
                           VALUES (?, ?, ?, ?, ?,)");
    $stmt->execute([$user_id, $note_title, $note_content, $visibility, $attachment]);

    // Redirect to the notes page after submission
    $_SESSION['success'] = 'Note created successfully!';
    header("Location: ../public/classroom_notes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Note</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/subjectNotes.css">
</head>

<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container my-4">
        <h2 class="fw-bold mb-4">Create New Note</h2>

        <!-- New Note Form -->
        <form action="../includes/create_note.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="noteTitle" class="form-label">Note Title</label>
                <input type="text" class="form-control" id="noteTitle" name="noteTitle" required>
            </div>

            <div class="mb-3">
                <label for="noteContent" class="form-label">Note Content</label>
                <textarea class="form-control" id="noteContent" name="noteContent" rows="4" required></textarea>
            </div>

            <!-- File Attachment -->
            <div class="mb-3">
                <label for="attachment" class="form-label">Add Attachment (Photo or Video)</label>
                <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*,video/*">
            </div>

            <!-- Privacy Option -->
            <div class="mb-3">
                <label class="form-label">Visibility</label>
                <div>
                    <input type="radio" id="public" name="visibility" value="public" checked>
                    <label for="public">Public</label>

                    <input type="radio" id="private" name="visibility" value="private">
                    <label for="private">Private</label>
                </div>
            </div>
            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-purple text-white">
                    <i class="bi bi-cloud-upload me-2"></i> Upload Note
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
