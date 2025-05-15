<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/Classroom.php';
require_once __DIR__ . '/../models/Subject.php';

class NoteController {
    private $pdo;
    private $noteModel;
    private $classroomModel;
    private $subjectModel;

    public function __construct() {
        $this->pdo = Database::getConnection();
        $this->noteModel = new Note($this->pdo);
        $this->classroomModel = new Classroom($this->pdo);
        $this->subjectModel = new Subject($this->pdo);
    }

    public function showNotesPage() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }
    
        $user_id = $_SESSION['user_id'];
    
        // Sanitize GET inputs
        $classroom_id = filter_input(INPUT_GET, 'classroom_id', FILTER_SANITIZE_NUMBER_INT);
        $sort_likes = filter_input(INPUT_GET, 'sort_likes', FILTER_SANITIZE_STRING);
        $sort_date = filter_input(INPUT_GET, 'sort_date', FILTER_SANITIZE_STRING);

        if ($classroom_id === 'all') $classroom_id = null;
    
        $filters = [
            'classroom_id' => $classroom_id,
            'sort_likes' => $sort_likes,
            'sort_date' => $sort_date
        ];
    
        // Fetch notes and classrooms for the logged-in user
        $notes = $this->noteModel->getFilteredNotes($user_id, $filters);
        $classrooms = $this->classroomModel->getClassroomsByUserId($user_id);

        // Return the data to the view
        return [
            'notes' => $notes,
            'classrooms' => $classrooms
        ];
    }

    public function createNote($classroom_id, $subject_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/auth/login.php");
            exit();
        }
    
       // Validate and sanitize inputs
        $title = trim($_POST['noteTitle'] ?? '');
        $content = trim($_POST['noteContent'] ?? '');
        $visibility = ($_POST['visibility'] ?? '') === 'private' ? 'private' : 'public';

        if (empty($title) || empty($content)) {
            $_SESSION['error'] = 'Please fill in all required fields.';
            header("Location: ../controllers/NoteController.php?action=create&classroom_id=$classroom_id&subject_id=$subject_id");
            exit();
        }

        $title = htmlspecialchars($title);
        $content = htmlspecialchars($content);
        $user_id = $_SESSION['user_id'];
        $attachmentPath = null;
    
        // Handle file upload
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/uploads/attachments';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
    
            $fileTmpPath = $_FILES['attachment']['tmp_name'];
            $fileName = basename($_FILES['attachment']['name']);
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid('note_', true) . '.' . $fileExt;
            $destPath = $uploadDir . '/' . $newFileName;
    
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $attachmentPath = 'uploads/attachments/' . $newFileName;
            }
        }
    
        // Save the note to the database
        $this->noteModel->createNote($user_id, $title, $content, $visibility, $attachmentPath, $subject_id);
        $this->noteModel->incrementNoteCount($subject_id);
    
        $_SESSION['success'] = 'Note created successfully.';
        header("Location: ../views/notes/subject_notes.php?subject_id=$subject_id&classroom_id=$classroom_id");
        exit();
    }
    
    // Displays notes for a specific subject
    public function showSubjectNotes() {

        if (!isset($_SESSION['user_id'])) {
            header("Location: ../auth/login.php");
            exit();
        }

        if (!isset($_GET['classroom_id']) || !isset($_GET['subject_id'])) {
            header("Location: ../main/dashboard.php");
            exit();
        }

        
        // Sanitize GET inputs
        $classroom_id = filter_input(INPUT_GET, 'classroom_id', FILTER_SANITIZE_NUMBER_INT);
        $subject_id = filter_input(INPUT_GET, 'subject_id', FILTER_SANITIZE_NUMBER_INT);

        if (!$classroom_id || !$subject_id) {
            header("Location: ../main/dashboard.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $classroom = $this->classroomModel->getClassroomById($classroom_id);
        $subject = $this->subjectModel->getSubject($subject_id);
        $notes = $this->noteModel->getNotesBySubjectId($subject_id);

        if (!$classroom || !$subject) {
            $_SESSION['error'] = 'An Error Has Occured!';
            header("Location: ../main/dashboard.php");
            exit();
        }

        return [
            'user_id' => $user_id,
            'classroom' => $classroom,
            'subject' => $subject,
            'notes' => $notes,
        ];
    }

    // Toggles a like for a note via AJAX
    public function toggleLike($note_id) {
        $user_id = $_SESSION['user_id'] ?? null;
        $note_id = filter_var($note_id, FILTER_SANITIZE_NUMBER_INT);
    
        if (!$user_id || !$note_id) {
            echo json_encode(['success' => false]);
            return;
        }
    
        $result = $this->noteModel->toggleLike($user_id, $note_id);
        echo json_encode(['success' => true, 'likes' => $result]);
    }
    
    // Toggles bookmark for a note via AJAX
    public function toggleBookmark($note_id) {
        $user_id = $_SESSION['user_id'] ?? null;
        $note_id = filter_var($note_id, FILTER_SANITIZE_NUMBER_INT);

        if (!$user_id || !$note_id) {
            echo json_encode(['success' => false]);
            return;
        }
    
        $result = $this->noteModel->toggleBookmark($user_id, $note_id);
        echo json_encode(['success' => true]);
    }

    // Loads a single note and related data
    public function loadNote(){
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../auth/login.php");
            exit();
        }
        
        $user_id = $_SESSION['user_id'];
        $note_id = filter_input(INPUT_GET, 'note_id', FILTER_SANITIZE_NUMBER_INT);

        $note = $this->noteModel->getNoteWithDetails($note_id);
        $userHasLiked = $this->noteModel->userHasLiked($user_id, $note_id);
        $userHasBookmarked = $this->noteModel->userHasBookmarked($user_id, $note_id);
        $comments = $this->noteModel->getComments($note_id) ?? [];

        return [
            'note' => $note,
            'userHasLiked' => $userHasLiked,
            'userHasBookmarked' => $userHasBookmarked,
            'comments' => $comments
        ];
    }
    
    // Adds a comment to a note
    public function addComment($note_id, $content){
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $note_id = filter_var($note_id, FILTER_SANITIZE_NUMBER_INT);
        $content = trim(htmlspecialchars($content));

        $this->noteModel->addComment($note_id, $user_id, $content);

        header("Location: ../views/notes/single.php?note_id=$note_id");
        exit();
    }

    public function updateNote($noteId, $title, $content, $visibility) {
    
    $attachmentPath = null;
    
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        
        $uploadDir = __DIR__ . '/../public/uploads/attachments';
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Get file info
        $fileTmpPath = $_FILES['attachment']['tmp_name'];
        $fileName = basename($_FILES['attachment']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('note_', true) . '.' . $fileExt;
        $destPath = $uploadDir . '/' . $newFileName;

        
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $attachmentPath = 'uploads/attachments/' . $newFileName;  // Save the relative file path
        }
    }
    
    
    $result = $this->noteModel->updateNote($noteId, $title, $content, $visibility, $attachmentPath);

    if (!$result) {
        $_SESSION['error'] = "Failed to update note.";
    } else {
        $_SESSION['success'] = "Note updated successfully.";
    }
}
}

// Handle POST requests and route to the correct method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noteController = new NoteController();

    if (isset($_POST['create_note'])) {
        $classroom_id = filter_input(INPUT_POST, 'classroom_id', FILTER_SANITIZE_NUMBER_INT);
        $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
        $noteController->createNote($classroom_id, $subject_id);
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_note'])) {
    $noteId = $_POST['note_id'];
    $title = $_POST['noteTitle'];
    $content = $_POST['noteContent'];
    $visibility = $_POST['visibility'];

    $controller = new NoteController();
    $controller->updateNote($noteId, $title, $content, $visibility);

    header("Location: ../views/notes/single.php?note_id=" . $noteId); // Redirect after update
    exit();
}


    // Handle note creation

    if(isset($_POST['action'])){
    
    if($_POST['action'] === 'add_comment'){
        $note_id = filter_input(INPUT_POST, 'note_id', FILTER_SANITIZE_NUMBER_INT);
        $content = $_POST['content'];
        $noteController->addComment($note_id, $content);
    }

    if ($_POST['action'] === 'toggleLike') {
        $note_id = filter_input(INPUT_POST, 'note_id', FILTER_SANITIZE_NUMBER_INT);
        $noteController->toggleLike($note_id);
    }

    if ($_POST['action'] === 'toggleBookmark') {
        $note_id = filter_input(INPUT_POST, 'note_id', FILTER_SANITIZE_NUMBER_INT);
        $noteController->toggleBookmark($note_id);
    }

    }
}
