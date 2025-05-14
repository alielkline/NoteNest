<?php
// app/controllers/SubjectController.php
require_once __DIR__ . '/../config/init.php';

// Check if the user is logged in
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../models/Classroom.php';
require_once __DIR__ . '/../models/Subject.php';

class SubjectController {

    private $pdo;
    private $classroomModel;
    private $subjectModel;
    private $user_id;

    public function __construct() {
        $this->pdo = Database::getConnection();
        $this->classroomModel = new Classroom($this->pdo);
        $this->subjectModel = new Subject($this->pdo);
        $this->user_id = $_SESSION['user_id'] ?? null;
    }

    // Check if user is logged in
    public function checkUserLoggedIn() {
        if (!$this->user_id) {
            header("Location: ../views/auth/login.php");
            exit();
        }
    }

    // Check if classroom exists and the user is a member
    public function checkClassroom($classroom_id) {
        $classroom_id = intval($classroom_id);

        if (empty($classroom_id)) {
            header("Location: ../views/pages/classrooms.php");
            exit();
        }

        $classroom = $this->classroomModel->getClassroomById($classroom_id);

        if (!$classroom) {
            header("Location: ../views/main/dashboard.php");
            exit();
        }

        $is_member = $this->classroomModel->isMember($this->user_id, $classroom_id);
        if (!$is_member) {
            header("Location: ../views/main/dashboard.php");
            exit();
        }

        return $classroom;
    }

    // Handle the creation of a subject
    public function createSubject($classroom_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_subject'])) {
            $subject_name = htmlspecialchars(trim($_POST['subject_name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $subject_desc = htmlspecialchars(trim($_POST['subject_desc'] ?? ''), ENT_QUOTES, 'UTF-8');
            $classroom_id = intval($classroom_id);

            // Validate inputs
            if (empty($subject_name) || empty($subject_desc)) {
                $_SESSION['error'] = "All fields are required.";
                header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
                exit();
            }

            // Create the subject
            $this->subjectModel->createSubject($classroom_id, $subject_name, $subject_desc);
            $_SESSION['success'] = "Subject created successfully!";
            header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
            exit();
        }
    }

    // Render the subjects page
    public function renderSubjectsPage($classroom_id) {
        $classroom = $this->checkClassroom($classroom_id);
        $subjects = $this->classroomModel->getSubjects($classroom_id);
        include '../views/pages/subjects.php';
    }

    public function updateSubject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_subject'])) {
            $subject_id = intval($_POST['subject_id'] ?? 0);
            $classroom_id = intval($_POST['classroom_id'] ?? 0);
            $name = htmlspecialchars(trim($_POST['subjectName'] ?? ''), ENT_QUOTES, 'UTF-8');
            $desc = htmlspecialchars(trim($_POST['subjectDesc'] ?? ''), ENT_QUOTES, 'UTF-8');

            if ($subject_id > 0 && $classroom_id > 0 && $name && $desc) {
                $this->subjectModel->updateSubject($subject_id, $name, $desc);
                $_SESSION['success'] = "Subject updated successfully!";
                header("Location: ../views/notes/subject_notes.php?classroom_id=$classroom_id&subject_id=$subject_id");
            } else {
                $_SESSION['error'] = "Invalid input for update.";
                header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
            }
            exit();
        }
    }

    // Delete a subject
    public function deleteSubject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_subject'])) {
            $subject_id = intval($_POST['subject_id'] ?? 0);
            $classroom_id = intval($_POST['classroom_id'] ?? 0);

            if ($subject_id > 0 && $classroom_id > 0) {
                $this->subjectModel->deleteSubject($subject_id);
                $_SESSION['success'] = "Subject deleted successfully!";
            } else {
                $_SESSION['error'] = "Invalid deletion request.";
            }

            header("Location: ../views/pages/subjects.php?classroom_id=$classroom_id");
            exit();
        }
    }

}

// Instantiate the controller
$subjectController = new SubjectController();

// Check if the user is logged in
$subjectController->checkUserLoggedIn();

// If the form is submitted, handle the creation of the subject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_subject'])) {
        $classroom_id = $_POST['classroom_id'] ?? null;
        $subjectController->createSubject($classroom_id);
    } elseif (isset($_POST['update_subject'])) {
        $subjectController->updateSubject();
    } elseif (isset($_POST['delete_subject'])) {
        $subjectController->deleteSubject();
    }
} else {
    $classroom_id = $_GET['classroom_id'] ?? null;
    $subjectController->renderSubjectsPage($classroom_id);
}