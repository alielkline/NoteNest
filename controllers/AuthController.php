<?php
// NoteNestMVC/controllers/AuthController.php

// Include required config and model files
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $pdo;
    private $userModel;

    public function __construct() {
        // Include required config and model files
        $this->pdo = Database::getConnection();
        $this->userModel = new User($this->pdo);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize and trim inputs
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Validate input presence
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = "Email and password are required.";
                header("Location: ../views/auth/login.php");
                exit;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Invalid email format.";
                header("Location: ../views/auth/login.php");
                exit;
            }
            // Check if the user exists by email
            $user = $this->userModel->findByEmail($email);

            // Verify password
            if ($user && password_verify($password, $user['password'])) {
                // Set session variables for the logged-in user
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['profile_image'] = $user['profile_image'];
                
                // after successful login check
                if (isset($_POST['remember'])) {
                    // Generate a secure random token
                    $token = bin2hex(random_bytes(32)); // 64 chars
                    
                    // Set token expiry (e.g., 30 days from now)
                    $expiry = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60);
                    
                    // Save token and expiry to DB for the user
                    $this->userModel->updateRememberToken($user['id'], $token, $expiry);
                    
                    // Set cookie with token, expires in 30 days, HttpOnly, Secure if HTTPS
                    setcookie('remember_token', $token, time() + 30*24*60*60, "/", "", false, true);
                } else {
                    // Clear any existing token in DB and cookie
                    $this->userModel->updateRememberToken($user['id'], null, null);
                    if (isset($_COOKIE['remember_token'])) {
                        setcookie('remember_token', '', time() - 3600, "/");
                    }
                }

                // Redirect to dashboard
                header("Location: ../views/main/dashboard.php");
                exit;
            }

             // Error message if login credentials are wrong
            $_SESSION['error'] = $user ? "Incorrect password." : "No account found with that email.";
            header("Location: ../views/auth/login.php");
            exit;
        }
    }

    public function logout() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_COOKIE['remember_token'])) {
            $this->userModel->updateRememberToken($_SESSION['user_id'], null, null);
            setcookie('remember_token', '', time() - 3600, "/");
        }

        // Clear session data and destroy session
        session_unset();
        session_destroy();

        // Redirect to homepage
        header("Location: ../views/main/home.php");
        exit;
    }

    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            // Input sanitation
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];

            // Validation

            // Validate username
            if (empty($username)) $errors[] = "Username is required.";

            // Validate email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";

            // Validate password strength using regex
            if (empty($password)) {
                $errors[] = "Password is required.";
            } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
                $errors[] = "Password must be at least 8 characters long, include upper and lowercase letters, a number, and a special character.";
            }

            // Check password confirmation
            if ($password !== $confirmPassword) $errors[] = "Passwords do not match.";

            // Check if username already exists
            if ($this->userModel->findByUsername($username)) {
                $errors[] = "Username already exists.";
            }

            // Check if email already exists
            if($this->userModel->findByEmail($email)){
                $errors[] = "Email already exists.";
            }

            // If any validation errors occurred, redirect back with errors
            if (!empty($errors)) {
                $_SESSION['signup_errors'] = $errors;
                header("Location: ../views/auth/signup.php");
                exit;
            }

            // Hash password securely
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Attempt to create the user in the database
            if ($this->userModel->create($username, $email, $hashedPassword)) {
            // ✅ Auto-login after successful signup
            $user = $this->userModel->findByEmail($email);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['profile_image'] = $user['profile_image'];

            // ✅ Optional: Remember me token (if checkbox was added on signup form)
            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60); // 30 days
                $this->userModel->updateRememberToken($user['id'], $token, $expiry);
                setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, "/", "", false, true);
            }

            header("Location: ../views/main/dashboard.php");
            exit;
        } else {
                $_SESSION['signup_errors'] = ["There was an error with registration!"];
                header("Location: ../views/auth/signup.php");
            }
            exit;
        }
    }
}

// Instantiate the controller
$controller = new AuthController();

// Route based on the `action` parameter in the URL
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