<?php

// app/controllers/DashboardController.php
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
        $this->pdo = Database::getConnection();
        $this->classroomModel = new Classroom($this->pdo);
        $this->noteModel = new Note($this->pdo);
    }

    public function getDashboardData()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Get filter and sorting
        $classroom_filter = isset($_GET['classroom_id']) && $_GET['classroom_id'] !== 'all' ? $_GET['classroom_id'] : null;
        $sort_order = isset($_GET['sort']) && $_GET['sort'] === 'oldest' ? 'ASC' : 'DESC';

        // Fetch classrooms
        $classrooms = $this->classroomModel->getClassroomsByUserId($user_id);

        // Fetch notes
        $notes = $this->noteModel->getNotesByUserId($user_id, $sort_order);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            // Include the partial view and return only the notes section
            ob_start();
            include __DIR__ . '/../views/partials/notes.php'; // assumes $notes is accessible inside notes.php
            echo ob_get_clean();
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
