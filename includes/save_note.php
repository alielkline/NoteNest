<?php
include 'init.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['noteTitle']);
    $content = trim($_POST['noteContent']);
    $visibility = $_POST['visibility'] === 'private' ? 'private' : 'public';
    $attachmentPath = null;
    $subject_id = $_POST['subject_id'];
    $classroom_id = $_POST['classroom_id'];

    // Handle file upload
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/attachments';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmpPath = $_FILES['attachment']['tmp_name'];
        $fileName = basename($_FILES['attachment']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('note_', true) . '.' . $fileExt;
        $destPath = $uploadDir . '/' . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $attachmentPath = $destPath;
        }
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO classroom_notes (uploader_user_id, title, content, visibility, attachment, subject_id) 
                           VALUES (:user_id, :title, :content, :visibility, :attachment_path, :subject_id)");
    $stmt->execute([
        ':user_id' => $user_id,
        ':title' => $title,
        ':content' => $content,
        ':visibility' => $visibility,
        ':attachment_path' => $attachmentPath,
        ':subject_id' => $subject_id
    ]);

    $stmt = $pdo->prepare("UPDATE classroom_subjects SET notes = notes + 1 WHERE subject_id = :subject_id");
    $stmt->execute([':subject_id' => $subject_id]);


    $_SESSION['success'] = 'Note Created Successfuly';
    header("Location: ../public/subjectNotes.php?subject_id=$subject_id&classroom_id=$classroom_id");
    exit();    
} else {
    $_SESSION['error'] = 'An error has occured';
    header("Location: ../pages/create_note.php?error=invalid_request");
    exit();
}
