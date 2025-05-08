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
    
        $classroom_id = $_GET['classroom_id'] ?? null;
        if ($classroom_id === 'all') $classroom_id = null;
    
        $sort_likes = $_GET['sort_likes'] ?? null;
        $sort_date = $_GET['sort_date'] ?? null;
    
        // Correct filter format
        $filters = [
            'classroom_id' => $classroom_id,
            'sort_likes' => $sort_likes,
            'sort_date' => $sort_date
        ];
    
        // Get notes
        $notes = $this->noteModel->getFilteredNotes($user_id, $filters);
        // Get classrooms
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
    
        // Validate input
        if (empty($_POST['noteTitle']) || empty($_POST['noteContent'])) {
            $_SESSION['error'] = 'Please fill in all required fields.';
            header("Location: ../controllers/NoteController.php?action=create&classroom_id=$classroom_id&subject_id=$subject_id");
            exit();
        }
    
        $user_id = $_SESSION['user_id'];
        $title = trim($_POST['noteTitle']);
        $content = trim($_POST['noteContent']);
        $visibility = ($_POST['visibility'] === 'private') ? 'private' : 'public';
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
    
        // Save to DB
        $this->noteModel->createNote($user_id, $title, $content, $visibility, $attachmentPath, $subject_id);
        $this->noteModel->incrementNoteCount($subject_id);
    
        $_SESSION['success'] = 'Note created successfully.';
        header("Location: ../views/notes/subject_notes.php?subject_id=$subject_id&classroom_id=$classroom_id");
        exit();
    }
    
    public function showSubjectNotes() {

        if (!isset($_SESSION['user_id'])) {
            header("Location: ../auth/login.php");
            exit();
        }

        if (!isset($_GET['classroom_id']) || !isset($_GET['subject_id'])) {
            header("Location: ../main/dashboard.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $classroom_id = $_GET['classroom_id'];
        $subject_id = $_GET['subject_id'];

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

    public function toggleLike($note_id) {
        $user_id = $_SESSION['user_id'] ?? null;
    
        if (!$user_id || !$note_id) {
            echo json_encode(['success' => false]);
            return;
        }
    
        $result = $this->noteModel->toggleLike($user_id, $note_id);
        echo json_encode(['success' => true, 'likes' => $result]);
    }
    
    
    public function toggleBookmark($note_id) {
        $user_id = $_SESSION['user_id'] ?? null;
    
        if (!$user_id || !$note_id) {
            echo json_encode(['success' => false]);
            return;
        }
    
        $result = $this->noteModel->toggleBookmark($user_id, $note_id);
        echo json_encode(['success' => true]);
    }

    public function loadNote(){
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../auth/login.php");
            exit();
        }
        
        $user_id = $_SESSION['user_id'];
        $note_id = $_GET['note_id'];

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

    public function addComment($note_id, $content){
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        $this->noteModel->addComment($note_id, $user_id, $content);

        header("Location: ../views/notes/single.php?note_id=$note_id");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noteController = new NoteController();

    if (isset($_POST['create_note'])) {
        $classroom_id = $_POST['classroom_id'] ?? null;
        $subject_id = $_POST['subject_id'] ?? null;
        $noteController->createNote($classroom_id, $subject_id);
    }

    if(isset($_POST['action'])){
    
    if($_POST['action'] === 'add_comment'){
        $note_id = $_POST['note_id'];
        $content = $_POST['content'];
        $noteController->addComment($note_id, $content);
    }

    if ($_POST['action'] === 'toggleLike') {
        $note_id = $_POST['note_id'];
        $noteController->toggleLike($note_id);
    }

    if ($_POST['action'] === 'toggleBookmark') {
        $note_id = $_POST['note_id'];
        $noteController->toggleBookmark($note_id);
    }

    }
}
