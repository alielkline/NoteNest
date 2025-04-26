<?php

include '../includes/init.php';

// Initialize errors array
$errors = [];

// Get form data
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

// Server-side validation
if (empty($username)) {
    $errors[] = "Username is required.";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email is required.";
}

if (empty($password)) {
    $errors[] = "Password is required.";
}

if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}

// Check if username already exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->rowCount() > 0) {
    $errors[] = "Username already exists.";
}

// If there are errors, store them in session and redirect back
if (!empty($errors)) {
    $_SESSION['signup_errors'] = $errors;
    header("Location: ../public/signup.php");
    exit();
}

// If no errors, proceed with registration
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, email, password, profile_image) VALUES (:username, :email, :password, :image)");
$success = $stmt->execute([
    ':username' => $username,
    ':email' => $email,
    ':password' => $hashedPassword,
    ':image' => 'profile-default.jpg'
]);

if ($success) {
    $_SESSION['signup_success'] = "Registration successful! You can now log in.";
    header("Location: ../public/login.php");
} else {
    $_SESSION['signup_errors'] = ["There was an error with registration!"];
    header("Location: ../public/signup.php");
}
exit();
?>
