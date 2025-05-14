<?php
// NoteNestMVC/controllers/ClassroomController.php

require_once __DIR__ . '/../models/Classroom.php';
require_once __DIR__ . '/../config/init.php';

class ClassroomController {
    private $pdo;
    private $classroomModel;

    public function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get database connection and initialize the Classroom model
        $this->pdo = Database::getConnection();
        $this->classroomModel = new Classroom($this->pdo);
    }

    // Helper to sanitize input
    private function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    // Password strength validator
    private function isStrongPassword($password) {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }

    public function createClassroom() {
        // Redirect if user is not logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize inputs
            $name = $this->sanitize($_POST['name'] ?? '');
            $visibility = $this->sanitize($_POST['visibility'] ?? '');
            $description = $this->sanitize($_POST['description'] ?? '');

            // Check for empty fields
            if (empty($name) || empty($description) || empty($visibility)) {
                $_SESSION['error'] = "All fields are required.";
                header("Location: ../views/main/dashboard.php");
                exit();
            }

            // Generate a secure 8-character invite code
            $invite_code = bin2hex(random_bytes(4)); 

            try {
                $this->pdo->beginTransaction();

                 // Create classroom and add creator as a member
                $classroom_id = $this->classroomModel->create(
                    $name, $description, $visibility, $invite_code, $user_id
                );

                $this->classroomModel->addMember($user_id, $classroom_id);

                $this->pdo->commit();

                $_SESSION['success'] = "Classroom created. Invite code: $invite_code";
                header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
                exit();
            } catch (Exception $e) {
                $this->pdo->rollBack();
                $_SESSION['error'] = "Failed to create classroom: " . $e->getMessage();
                header("Location: ../views/main/dashboard.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid request.";
            header("Location: ../views/main/dashboard.php");
            exit();
        }
    }

    public function getClassrooms() {
        // Redirect if user is not logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Return classrooms that are public and not joined by the user
        $classrooms = $this->classroomModel->getPublicClassroomsNotJoined($user_id);

        return [
            'classrooms' => $classrooms
        ];
    }

    public function joinClassroomWithCode() {
        // Redirect if user is not logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize and validate invite code
            $invite_code = $this->sanitize($_POST['invite_code'] ?? '');

            // Get the classroom with the invite code
            $classroom = $this->classroomModel->getClassroomByInviteCode($invite_code);

            if ($classroom) {
                // Prevent duplicate joining
                if (!$this->classroomModel->isUserInClassroom($user_id, $classroom['classroom_id'])) {
                    $this->classroomModel->addMember($user_id, $classroom['classroom_id']);

                    $_SESSION['success'] = 'Joined classroom successfully';
                    header("Location: ../views/pages/classrooms.php");
                    exit();
                } else {
                    $_SESSION['error'] = 'Already in this classroom';
                    header("Location: ../views/pages/classrooms.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Invalid invite code';
                header("Location: ../views/pages/classrooms.php");
                exit();
            }
        }
    }

    public function joinClassroom() {
        // Redirect if user is not logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classroom_id = $this->sanitize($_POST['classroom_id'] ?? null);
            var_dump($classroom_id);

            // Validate input
            if (!$classroom_id) {
                $_SESSION['error'] = "Missing classroom ID.";
                header("Location: ../views/pages/classrooms.php");
                exit();
            }

            // Prevent user from joining the same class twice
            if ($this->classroomModel->isUserInClassroom($user_id, $classroom_id)) {
                $_SESSION['error'] = "You are already a member of this classroom.";
                header("Location: ../views/pages/classrooms.php");
                exit();
            }

            // Add user to classroom
            $this->classroomModel->addMember($user_id, $classroom_id);
            $_SESSION['success'] = "Successfully joined the classroom.";
            header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
            exit();
        }
    }

    // Leave a classroom
    public function leaveClassroom() {
        // Redirect if user is not logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classroom_id = $_POST['classroom_id'] ?? null;

            // Validate input
            if (!$classroom_id) {
                $_SESSION['error'] = "Missing classroom ID.";
                header("Location: ../views/pages/classrooms.php");
                exit();
            }

            $this->classroomModel->removeMember($user_id, $classroom_id);
            $_SESSION['success'] = "You have left the classroom.";
            header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
            exit();
        }
    }

    public function updateClassroomSettings() {
        // Redirect if user is not logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classroom_id = $_POST['classroom_id'] ?? null;
            $name = trim($_POST['classroomName'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $visibility = $_POST['visibility'] ?? '';

            // Validate all fields
            if (!$name || !$description || !$visibility) {
                $_SESSION['error'] = "All fields are required.";
                header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
                exit();
            }

            // Update the classroom
            $success = $this->classroomModel->updateClassroom($classroom_id, $name, $description, $visibility);

            $_SESSION[$success ? 'success' : 'error'] = $success ? "Classroom updated successfully." : "Failed to update classroom.";
            header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
            exit();
        }
    }

    // Display subjects in a classroom
    public function viewSubjects() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        if (!isset($_GET['classroom_id'])) {
            header("Location: ../views/pages/classrooms.php");
            exit();
        }

        $classroom_id = $_GET['classroom_id'];

        // Get classroom details
        $classroom = $this->classroomModel->getClassroomById($classroom_id);

        if (!$classroom) {
            header("Location: ../views/pages/classrooms.php");
            exit();
        }

        // Get subjects and membership info
        $subjects = $this->classroomModel->getSubjects($classroom_id);
        $is_member = $this->classroomModel->isMember($user_id, $classroom_id);

        return [
            'classroom' => $classroom,
            'subjects' => $subjects,
            'is_member' => $is_member,
            'user_id' => $user_id,
        ];
    }

    public function deleteClassroom() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }
    
        $user_id = $_SESSION['user_id'];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classroom_id = $_POST['classroom_id'] ?? null;
    
            if (!$classroom_id) {
                $_SESSION['error'] = "Missing classroom ID.";
                header("Location: ../views/pages/classrooms.php");
                exit();
            }
    
            $classroom = $this->classroomModel->getClassroomById($classroom_id);
    
            if (!$classroom) {
                $_SESSION['error'] = "Classroom not found.";
                header("Location: ../views/pages/classrooms.php");
                exit();
            }
    
            // Check if user is authorized
            if ($classroom['creator_id'] != $user_id) {
                $_SESSION['error'] = "You are not authorized to delete this classroom.";
                header("Location: ../views/main/dashboard.php");
                exit();
            }
    
            // Delete the classroom
            $deleted = $this->classroomModel->deleteClassroom($classroom_id);
    
            $_SESSION[$deleted ? 'success' : 'error'] = $deleted ? "Classroom deleted successfully." : "Failed to delete classroom.";
            header("Location: ../views/main/dashboard.php");
            exit();
        }
    }

    // Return subjects via AJAX as JSON
    public function getSubjectsAjax() {
        if (!isset($_GET['classroom_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing classroom ID']);
            return;
        }
    
        $classroom_id = $_GET['classroom_id'];
        $subjects = $this->classroomModel->getSubjects($classroom_id);
    
        header('Content-Type: application/json'); // This tells the browser that the response from the server is JSON ddata, not HTML
        echo json_encode($subjects); // This takes the $subjects array and converts it into a JSON string.
    }
    
    
}

// === Action Dispatcher ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $controller = new ClassroomController();

    switch ($_GET['action']) {
        case 'create':
            $controller->createClassroom();
            break;
        case 'Codejoin':
            $controller->joinClassroomWithCode();
            break;
        case 'join':
            $controller->joinClassroom();
            break;
        case 'leave':
            $controller->leaveClassroom();
            break;
        case 'update':
            $controller->updateClassroomSettings();
            break;
        case 'delete':
            $controller->deleteClassroom();
            break;
        default:
            $_SESSION['error'] = "Invalid action.";
            header("Location: ../views/pages/classrooms.php");
            exit();
    }
}
