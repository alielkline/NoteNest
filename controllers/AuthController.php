<?php
// app/controllers/AuthController.php
require_once '../config/init.php';
require_once '../models/User.php';

class AuthController {
    private $pdo;
    private $userModel;

    public function __construct() {
        $this->pdo = Database::getConnection(); // Get PDO from static method
        $this->userModel = new User($this->pdo);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['profile_image'] = $user['profile_image'];
                header("Location: ../views/main/dashboard.php");
                exit;
            }

            $_SESSION['error'] = $user ? "Incorrect password." : "No account found with that email.";
            header("Location: ../views/auth/login.php");
            exit;
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../views/main/home.php");
        exit;
    }

    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];

            if (empty($username)) $errors[] = "Username is required.";
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
            if (empty($password)) $errors[] = "Password is required.";
            if ($password !== $confirmPassword) $errors[] = "Passwords do not match.";

            if ($this->userModel->findByUsername($username)) {
                $errors[] = "Username already exists.";
            }

            if($this->userModel->findByEmail($email)){
                $errors[] = "Email already exists.";
            }

            if (!empty($errors)) {
                $_SESSION['signup_errors'] = $errors;
                header("Location: ../views/auth/signup.php");
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($this->userModel->create($username, $email, $hashedPassword)) {
                $_SESSION['signup_success'] = "Registration successful!";
                header("Location: ../views/auth/login.php");
            } else {
                $_SESSION['signup_errors'] = ["There was an error with registration!"];
                header("Location: ../views/auth/signup.php");
            }
            exit;
        }
    }
}

$controller = new AuthController();

$action = $_GET['action'] ?? '';

if ($action === 'login') {
    $controller->login();
} elseif ($action === 'signup') {
    $controller->signup();
} elseif ($action === 'logout') {
    $controller->logout();
} else {
    http_response_code(404);
    echo "404 Not Found";
}