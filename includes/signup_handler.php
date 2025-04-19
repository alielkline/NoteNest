<?php

session_start();

// Connect to database
require 'db.inc.php';

// Get form data
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

// Server-side validation
$errors = [];

if (empty($username)) {
    $errors[] = "Username is required";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email is required";
}

if (empty($password)) {
    $errors[] = "Password is required";
}

if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match";
}

// Check if username already exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->rowCount() > 0) {
    echo "<script>alert('Username already exists!'); window.location.href = '../public/signup.php';</script>";
    exit();
}

// If no errors, proceed with registration
if (empty($errors)) {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Prepare and bind
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    $success = $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashedPassword
    ]);

    // Execute the statement
    if ($success) {
        // Registration successful
        $_SESSION['success'] = "Registration successful!";
        header("Location: ../public/home.php");
        exit();
    } else {
        $_SESSION['error'] = "There was an error with registration!";
        header("Location: ../public/signup.php");
    }
    
    $stmt->close();
}

// If there were errors, show them
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
    echo '<p><a href="signup.html">Go back</a></p>';
}

$pdo->close();

?>