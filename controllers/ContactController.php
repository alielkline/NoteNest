<?php
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/../config/init.php';

class ContactController {
    public function showForm() {
        include __DIR__ . '/../views/pages/contact.php';
    }

    public function handleForm() {

        $errors = [];
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name)) $errors[] = "Name is required.";
        if (empty($email)) $errors[] = "Email is required.";
        if (empty($message)) $errors[] = "Message cannot be empty.";

        if (!empty($errors)) {
            $_SESSION['contact_errors'] = $errors;
            header("Location: ../views/pages/contact.php");
            exit();
        }

        // Save the contact or send an email (you decide)
        $contact = new Contact();
        $contact->save($name, $email, $message);

        $_SESSION['success'] = "Thank you for your message!";
        header("Location: ../views/pages/contact.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    (new ContactController())->showForm();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'submit') {
    (new ContactController())->handleForm();
}