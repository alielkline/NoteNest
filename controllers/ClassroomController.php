<?php
// app/controllers/ClassroomController.php

require_once __DIR__ . '/../models/Classroom.php';
require_once __DIR__ . '/../config/init.php';

class ClassroomController {
    private $pdo;
    private $classroomModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->pdo = Database::getConnection();
        $this->classroomModel = new Classroom($this->pdo);
    }

    public function createClassroom() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $visibility = $_POST['visibility'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name) || empty($description) || empty($visibility)) {
                $_SESSION['error'] = "All fields are required.";
                header("Location: ../views/main/dashboard.php");
                exit();
            }

            $invite_code = bin2hex(random_bytes(4)); // 8-character secure code

            try {
                $this->pdo->beginTransaction();

                $classroom_id = $this->classroomModel->create(
                    $name, $description, $visibility, $invite_code, $user_id
                );

                $this->classroomModel->addMember($user_id, $classroom_id);

                $this->pdo->commit();

                $_SESSION['success'] = "Classroom created. Invite code: $invite_code";
                header("Location: ../views/main/subjects.php?classroom_id=$classroom_id");
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
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        $classrooms = $this->classroomModel->getPublicClassroomsNotJoined($user_id);

        return [
            'classrooms' => $classrooms
        ];
    }

    public function joinClassroomWithCode() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invite_code = $_POST['invite_code'];

            $classroom = $this->classroomModel->getClassroomByInviteCode($invite_code);

            if ($classroom !== 'false') {
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
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classroom_id = $_POST['classroom_id'] ?? null;
            var_dump($classroom_id);
            if (!$classroom_id) {
                $_SESSION['error'] = "Missing classroom ID.";
                header("Location: ../views/pages/classrooms.php");
                exit();
            }

            if ($this->classroomModel->isUserInClassroom($user_id, $classroom_id)) {
                $_SESSION['error'] = "You are already a member of this classroom.";
                header("Location: ../views/pages/classrooms.php");
                exit();
            }

            $this->classroomModel->addMember($user_id, $classroom_id);
            $_SESSION['success'] = "Successfully joined the classroom.";
            header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
            exit();
        }
    }

    public function leaveClassroom() {
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

            $this->classroomModel->removeMember($user_id, $classroom_id);
            $_SESSION['success'] = "You have left the classroom.";
            header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
            exit();
        }
    }

    public function updateClassroomSettings() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classroom_id = $_POST['classroom_id'] ?? null;
            $name = trim($_POST['classroomName'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $visibility = $_POST['visibility'] ?? '';
            if (!$name || !$description || !$visibility) {
                $_SESSION['error'] = "All fields are required.";
                header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
                exit();
            }

            $success = $this->classroomModel->updateClassroom($classroom_id, $name, $description, $visibility);

            $_SESSION[$success ? 'success' : 'error'] = $success ? "Classroom updated successfully." : "Failed to update classroom.";
            header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
            exit();
        }
    }

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

        $classroom = $this->classroomModel->getClassroomById($classroom_id);

        if (!$classroom) {
            header("Location: ../views/pages/classrooms.php");
            exit();
        }

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
    
            if ($classroom['creator_id'] != $user_id) {
                $_SESSION['error'] = "You are not authorized to delete this classroom.";
                header("Location: ../views/main/dashboard.php");
                exit();
            }
    
            $deleted = $this->classroomModel->deleteClassroom($classroom_id);
    
            $_SESSION[$deleted ? 'success' : 'error'] = $deleted ? "Classroom deleted successfully." : "Failed to delete classroom.";
            header("Location: ../views/main/dashboard.php");
            exit();
        }
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
