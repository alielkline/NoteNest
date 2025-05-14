<?php
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/../config/init.php';

class ContactController {

    // Displays the contact form
    public function showForm() {
        header("Location: ../views/pages/contact.php");
        exit;
    }

    // Handles form submission
    public function handleForm() {
        $errors = [];

        // Sanitize and trim user input
        $name = htmlspecialchars(trim($_POST['name'] ?? ''));
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $message = htmlspecialchars(trim($_POST['message'] ?? ''));

        // Validate required fields
        if (empty($name)) $errors[] = "Name is required.";
        if (empty($email)) $errors[] = "Email is required.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
        if (empty($message)) $errors[] = "Message cannot be empty.";

        // If there are validation errors, store them in session and redirect back
        if (!empty($errors)) {
            $_SESSION['contact_errors'] = $errors;
            header("Location: ../views/pages/contact.php");
            exit();
        }

        // Save the contact message (or alternatively, send an email here)
        $contact = new Contact();
        $contact->save($name, $email, $message);

        // Store success message in session and redirect
        $_SESSION['success'] = "Thank you for your message!";
        header("Location: ../views/pages/contact.php");
        exit();
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    (new ContactController())->handleForm();
}
