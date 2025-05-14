<?php

// NoteNestMVC/controllers/DashboardController.php

require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../models/Classroom.php';
require_once __DIR__ . '/../models/Note.php';

class DashboardController
{
    private $classroomModel;
    private $noteModel;
    private $pdo;

    public function __construct()
    {
        // Initialize database connection and models
        $this->pdo = Database::getConnection();
        $this->classroomModel = new Classroom($this->pdo);
        $this->noteModel = new Note($this->pdo);
    }

    public function getDashboardData()
    {
        // Redirect to login if user is not authenticated
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // === Sanitize and retrieve filters ===

        // Sanitize classroom_id filter from GET (null means show all)
        $classroom_filter_raw = $_GET['classroom_id'] ?? null;
        $classroom_filter = ($classroom_filter_raw && $classroom_filter_raw !== 'all')
            ? htmlspecialchars(trim($classroom_filter_raw))
            : null;

        // Sanitize sorting order
        $sort_raw = $_GET['sort'] ?? '';
        $sort_order = ($sort_raw === 'oldest') ? 'ASC' : 'DESC';

        // === Fetch classrooms and notes for the user ===

        $classrooms = $this->classroomModel->getClassroomsByUserId($user_id);
        $notes = $this->noteModel->getNotesByUserId($user_id, $classroom_filter, $sort_order);

        // === Handle AJAX request ===
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            // If it's an AJAX request, return only the updated notes section
            ob_start(); // Begin capturing output that will be sent to the browser.
            include __DIR__ . '/../views/partials/notes.php';
            echo ob_get_clean(); // end output buffering and return the captured HTML as a string.
            exit();
        }

        // Return data for full page rendering
        return [
            'classrooms' => $classrooms,
            'notes' => $notes,
            'user_id' => $user_id
        ];
    }
}
