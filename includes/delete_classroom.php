<?php
include '../includes/init.php'; // Connect to DB, start session, etc.

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if classroom_id is sent by POST
if (!isset($_POST['classroom_id'])) {
    die("No classroom selected.");
}

$classroom_id = $_POST['classroom_id'];

// Fetch the classroom to verify ownership
$stmt = $pdo->prepare("SELECT creator_id FROM classrooms WHERE classroom_id = ?");
$stmt->execute([$classroom_id]);
$classroom = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$classroom) {
    die("Classroom not found.");
}

// Check if the current user is the creator/admin
if ($classroom['creator_id'] != $user_id) {
    die("You do not have permission to delete this classroom.");
}

// Delete classroom (you might need to also delete related data like subjects, notes, members)
try {
    $pdo->beginTransaction();

    // Delete notes related to subjects in this classroom
    $pdo->prepare("
        DELETE cn FROM classroom_notes cn
        JOIN classroom_subjects cs ON cn.subject_id = cs.subject_id
        WHERE cs.classroom_id = ?
    ")->execute([$classroom_id]);

    // Delete subjects
    $pdo->prepare("DELETE FROM classroom_subjects WHERE classroom_id = ?")->execute([$classroom_id]);

    // Delete members
    $pdo->prepare("DELETE FROM classroom_members WHERE classroom_id = ?")->execute([$classroom_id]);

    // Finally, delete the classroom itself
    $pdo->prepare("DELETE FROM classrooms WHERE classroom_id = ?")->execute([$classroom_id]);

    $pdo->commit();

    // Redirect after successful delete
    header("Location: ../public/dashboard.php?message=classroom_deleted");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error deleting classroom: " . $e->getMessage());
}
?>
