<?php

session_start();

// Connect to database
require 'db.inc.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the query to retrieve the user based on the email
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':email' => $email
    ]);
    // Fetch the result (assuming there is only one row for each email)
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // User exists, now check the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, log the user in
            $_SESSION['user_id'] = $user['id']; // Store user ID in session
            $_SESSION["email"] = $user["email"];
            $_SESSION['username'] = $user['username']; // Store username in session
            $_SESSION['profile_picture'] = $user['profile_picture']; // Store profile picture in session

            // Redirect to the dashboard or home page
            header("Location: ../public/dashboard.php");
            exit();
        } else {
            // Incorrect password
            $_SESSION['error'] = "Incorrect password.";
            header("Location: ../public/login.php");
            exit();
        }
    } else {
        // No user found with that email
        $_SESSION['error'] = "No account found with that email.";
        header("Location: ../public/login.php");
        exit();
    }
}
