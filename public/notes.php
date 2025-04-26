<?php
include '../includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$username = $user['username'];

// Fetch classrooms (public or those created by the user)
$stmt = $pdo->prepare("SELECT c.*
FROM classrooms c
JOIN classroom_members cm ON c.classroom_id = cm.classroom_id
WHERE cm.user_id = ?;");
$stmt->execute([$user_id]);
$classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 1. Get selected filter/sort from request (GET or POST)
$classroom_filter = isset($_GET['classroom_id']) && $_GET['classroom_id'] !== 'all' ? $_GET['classroom_id'] : null;
$sort_order = isset($_GET['sort']) && $_GET['sort'] === 'oldest' ? 'ASC' : 'DESC';

// 2. Base query to get notes
$note_query = "
    SELECT cn.*
    FROM classroom_notes cn
    JOIN classroom_subjects cs ON cn.subject_id = cs.subject_id
";

// 3. Apply filter if a specific classroom is selected
$params = [];
if ($classroom_filter) {
    $note_query .= " WHERE cs.classroom_id = ?";
    $params[] = $classroom_filter;
}

// 4. Add sorting
$note_query .= " ORDER BY cn.upload_date $sort_order";

// 5. Prepare and execute
$note_stmt = $pdo->prepare($note_query);
$note_stmt->execute($params);
$notes = $note_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/notes.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <title>Notes</title>
</head>
<body>
    <script src="../js/notes.js"></script>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/init.php'; ?>
    

    <!--Headers and early buttons!-->
    <div class="container d-flex justify-content-between mt-4">

        <div>
            <h2 class="header p-0 mb-0">All Notes</h2>
            <p class="text-muted">Browse and discover notes from all classrooms</p>

            
        </div>


        <div class="p-3">
            <button class="btn-login border px-3">
            
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-sort-down me-2" viewBox="0 0 16 16">
            <path  d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"/>
            </svg>
            
            Most Liked</button>
            <button class="btn-signup px-3">
                
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-lg me-2" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
            </svg>
            
            New Note</button>
        </div>

    </div>

    <!--Two Dropdowns!-->
    <div class="container">

    <div class="d-flex my-4">
        <div class="row justify-content-left w-100">
            <div class="col-12 col-md-3 mb-3 mb-md-0">
                <div class="dropdown">

                    <!--Classroom Dropdown!-->


                    <form method="GET" class="d-flex flex-wrap justify-content-center align-items-center gap-3">
    <!-- Filter by Classroom -->
    <div>
                <div>
                <label for="classroomFilter" class="form-label custom-label me-2">Filter by Classroom:</label>
                <select name="classroom_id" id="classroomFilter" class="form-select custom-style d-inline-block w-auto ">
                    <option value="all" <?= !isset($_GET['classroom_id']) || $_GET['classroom_id'] === 'all' ? 'selected' : '' ?>>All Classrooms</option>
                    <?php foreach ($classrooms as $class): ?>
                        <option value="<?= $class['classroom_id'] ?>" <?= (isset($_GET['classroom_id']) && $_GET['classroom_id'] == $class['classroom_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($class['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Submit Button -->
    <div>
        <button type="submit" class="btn custom-style">
            <i class="bi bi-filter"></i> Apply
        </button>
    </div>
</form>

            <div class="col-12 col-md-3 mb-3 mb-md-0">
                <div class="dropdown">

                    <!--Notes Dropdown!-->
                    <button class="btn btn-notes-dropdown dropdown-toggle border w-100" type="button" id="dropdownNotesButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        All Notes 
                    </button>
                    <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item">All Notes</a>
                        <a class="dropdown-item">Liked Notes</a>
                        <a class="dropdown-item">Bookmarked Notes</a>
                    </div>
                </div>
            </div>
            </div>
        </div>

    </div>

    <!--foreach loop for the notes!-->
    <?php 
    
    //Selecting the data
    $stmt = $pdo->prepare("
    SELECT 
        t1.note_id,
        t1.title, 
        t1.upload_date,
        t1.content,
        t1.likes
        t2.subject_name, 
        u.username,
        
    FROM 
        classroom_notes t1
    JOIN 
        classroom_subjects t2 ON t1.subject_id = t2.subject_id
    JOIN 
        users u ON u.id = t1.uploader_user_id
    LEFT JOIN
        likes l ON l.note_id = t1.note_id
    GROUP BY 
        t1.note_id, t1.title, t1.upload_date, t1.content, t2.subject_name, u.username
        ");
        $stmt->execute();

        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);


        //checking if there are 0 notes
        if(empty($notes)){
            
            ?>
            <div class="container">
            <h4>No notes currently!</h4>
            <a href="classrooms.php" class="btn-no-notes p-1">Join a classroom!</a>
            </div>
            <?php
        }
        else{
            foreach ($notes as $note) {
                $excerpt = substr($note['content'], 0, 60) . '...';
                $date = date("M d, Y", strtotime($note['upload_date']));

                echo "<div class='note-pane border p-3 mb-3'><div class='d-flex justify-content-between'>";
                echo "<div><h4 class='mb-2'>" . htmlspecialchars($note['title']) . "</h4>";
                echo "<p class='mb-1'><strong>By:</strong> " . htmlspecialchars($note['username']) . "</p>";
                echo "<p class='subject-pill mb-1'> " . htmlspecialchars($note['subject_name']) . "</p>";
                echo "<p class='mb-1'><strong>Date:</strong> " . htmlspecialchars($date) . "</p>";
                echo "<p class='mt-2 text-muted'>" . htmlspecialchars($excerpt) . "</p></div>";
                echo "<div><p class='mt-2'>" . htmlspecialchars($note['likes']) . "</p></div>"; 
                echo "</div></div>";
            }
        }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

