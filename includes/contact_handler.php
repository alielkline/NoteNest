<?php

session_start();


//Check if the user accessed this page legitimately
if (isset($_POST['submit'])){

    //Interpret all the form-variables
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $subject = "NoteNest Feedback";

    //Validate the input
    $errors = array();

    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required.";
    }
    if (!empty($errors)) {
        $_SESSION['contact_errors'] = $errors;
        header("Location: ../public/contact.php");
        exit();
    }

    //Prepare mail() parameters
    $mailto = "example@gmail.com";
    $headers = "From: " . $email;
    
    //mail($mailto, $subject, $message, $headers);

}

