<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController {
    private $pdo;
    private $userModel;
    private $userId;

    public function __construct($userId) {
        $this->pdo = Database::getConnection();
        $this->userModel = new User($this->pdo);
        $this->userId = intval($userId); 
    }

     // Entry point for form submissions
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formType = $_POST['form_type'] ?? '';

            switch ($formType) {
                case 'photo':
                    $this->updatePhoto();
                    break;
                case 'general':
                    $this->updateGeneral();
                    break;
                case 'security':
                    $this->updatePassword();
                    break;
                default:
                    header("Location: ../main/profile.php?error=invalid_form_type");
                    exit();
            }
        }
    }

    // Handles profile picture upload
    private function updatePhoto() {
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_image']['tmp_name'];
            $fileName = basename($_FILES['profile_image']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            // Validate file extension
            if (!in_array($fileExtension, $allowedExtensions)) {
                header("Location: ../main/profile.php?error=invalid_file_extension");
                exit();
            }
    
            // Construct safe new filename
            $newFileName = $this->userId . '_profile.' . $fileExtension;
    
            // Absolute server-side path to the uploads directory
            $uploadDir = __DIR__ . '/../public/uploads/profile_pictures/';
            $destPath = $uploadDir . $newFileName;
    
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
    
            // Move file and update DB
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $this->userModel->updateProfileImage($this->userId, $newFileName);
                header("Location: ../main/profile.php?success=photo_updated");
                exit();
            } else {
                header("Location: ../main/profile.php?error=file_upload_failed");
                exit();
            }
        } else {
            header("Location: ../main/profile.php?error=file_upload_error");
            exit();
        }
    }
    
    // Updates general info (username and email)
    private function updateGeneral() {
        // Basic sanitization
        $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

        if (!empty($username) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->userModel->updateGeneral($this->userId, $username, $email);
            header("Location: ../main/profile.php?success=profile_updated");
        } else {
            header("Location: ../main/profile.php?error=missing_fields");
        }

        exit();
    }

    // Changes the user password securely
    private function updatePassword() {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword && $confirmPassword && $newPassword === $confirmPassword) {
            if (strlen($newPassword) < 8) {
                header("Location: ../main/profile.php?error=weak_password");
                exit();
            }

             // Hash and update
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->userModel->updatePassword($this->userId, $hashedPassword);
            header("Location: ../main/profile.php?success=password_changed");
        } else {
            header("Location: ../main/profile.php?error=password_mismatch");
        }

        exit();
    }

    // Fetches user info for profile page
    public function getUserData() {
        return $this->userModel->getById($this->userId);
    }
}
