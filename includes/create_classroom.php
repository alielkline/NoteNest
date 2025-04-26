<?php
include '../includes/init.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_POST['name'];
    $visibility = $_POST['visibility'];
    $description = $_POST['description'];
    
    if (empty($name) || empty($description) || empty($visibility)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../public/dashboard.php");
        exit();
    }
    
    function generateInviteCode($length = 8) {
        return bin2hex(random_bytes($length / 2)); // generates a secure random code
    }

    $invite_code = generateInviteCode();

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Insert into classrooms table
        $stmt = $pdo->prepare("INSERT INTO classrooms (name, description, visibility, invite_code, creator_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $visibility, $invite_code, $user_id]);

        // Get the ID of the newly created classroom
        $classroom_id = $pdo->lastInsertId();

        // Insert the creator as a member of the classroom
        $stmt = $pdo->prepare("INSERT INTO classroom_members (user_id, classroom_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $classroom_id]);

        // Commit transaction
        $pdo->commit();

        $_SESSION['success'] = "Classroom created successfully. Invite code: $invite_code";
        header("Location: ../public/subjects.php?classroom_id=$classroom_id");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        $_SESSION['error'] = "Failed to create classroom: " . $e->getMessage();
        header("Location: ../public/dashboard.php");
        exit();
    }
}else{
    $_SESSION['error'] = "An error occurder. Try Again Later";
    header("Location: ../public/dashboard.php");
    exit();
}
