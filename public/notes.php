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
$sort_order = isset($_GET['sort_date']) && $_GET['sort_date'] === 'oldest' ? 'ASC' : 'DESC';
$like_order = isset($_GET['sort_likes']) && $_GET['sort_likes'] === 'leastLiked' ? 'ASC' : 'DESC';

// 2. Base query to get notes
$note_query = "
    SELECT cn.*, subject_name, username
    FROM classroom_notes cn
    JOIN classroom_subjects cs ON cn.subject_id = cs.subject_id
    JOIN users u ON u.id = cn.uploader_user_id
";

// 3. Apply filter if a specific classroom is selected
$params = [];
if ($classroom_filter) {
    $note_query .= " WHERE cs.classroom_id = ?";
    $params[] = $classroom_filter;
}

// 4. Apply sorting
$note_query .= " ORDER BY ";
$sorting_criteria = [];

if (isset($_GET['sort_likes'])) {
    $sorting_criteria[] = "cn.likes $like_order";
}

// Only apply date sorting *if likes sorting is not active* OR add it as secondary
if (isset($_GET['sort_date'])) {
    $sorting_criteria[] = "cn.upload_date $sort_order";
}

if (count($sorting_criteria) > 0) {
    $note_query .= implode(", ", $sorting_criteria);
} else {
    // If no sorting is applied, default to upload date descending
    $note_query .= "cn.upload_date DESC";
}

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
    
    

    <!--Headers and early buttons!-->
    <div class="container d-flex justify-content-between mt-4">

        <div>
            <h2 class="header p-0 mb-0">All Notes</h2>
            <p class="text-muted">Browse and discover notes from all classrooms</p>

            
        </div>


        <div class="p-3">
            <button class="btn-login border px-3">
            
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-lg me-2" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
            </svg>
            
            New Note</button>
        </div>

    </div>

    <form method="GET" class="d-flex flex-wrap align-items-center gap-3">
    <!-- Filter by Classroom -->
    <div>
        <label for="classroomFilter" class="form-label custom-label me-2 visually-hidden">Filter by Classroom:</label>
        <select name="classroom_id" id="classroomFilter" class="form-select custom-style d-inline-block w-auto btn-notes-dropdown">
            <option value="all" <?= !isset($_GET['classroom_id']) || $_GET['classroom_id'] === 'all' ? 'selected' : '' ?>>All Classrooms</option>
            <?php foreach ($classrooms as $class): ?>
                <option value="<?= $class['classroom_id'] ?>" <?= (isset($_GET['classroom_id']) && $_GET['classroom_id'] == $class['classroom_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($class['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Sort by Upload Date -->
    <div>
        <label for="sortDate" class="form-label custom-label me-2 visually-hidden">Sort by Date:</label>
        <select name="sort_date" id="sortDate" class="form-select d-inline-block w-auto btn-notes-dropdown">
            <option value="newest" <?= (!isset($_GET['sort_date']) || $_GET['sort_date'] === 'newest') ? 'selected' : '' ?>>Newest</option>
            <option value="oldest" <?= (isset($_GET['sort_date']) && $_GET['sort_date'] === 'oldest') ? 'selected' : '' ?>>Oldest</option>
        </select>
    </div>

    <!-- Sort by Likes -->
    <div>
        <label for="likeOrder" class="form-label custom-label me-2 visually-hidden">Sort by Likes:</label>
        <select name="sort_likes" id="likeOrder" class="form-select d-inline-block w-auto btn-notes-dropdown">
            <option value="mostLiked" <?= (!isset($_GET['sort_likes']) || $_GET['sort_likes'] === 'mostLiked') ? 'selected' : '' ?>>Most Liked</option>
            <option value="leastLiked" <?= (isset($_GET['sort_likes']) && $_GET['sort_likes'] === 'leastLiked') ? 'selected' : '' ?>>Least Liked</option>
        </select>
    </div>

    <!-- Submit Button -->
    <div>
        <button type="submit" class="btn custom-style">
            <i class="bi bi-filter"></i> Apply
        </button>
    </div>
</form>

    

                </div>
            </div>
            </div>
        </div>

    </div>

    <!--foreach loop for the notes!-->
    <?php 
    
    //Selecting the data
    


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

                echo "<div class='container'";
                echo "<div class='note-pane border p-3 mb-3'><div class='d-flex justify-content-between'>";
                echo "<div class='d-flex'><p class='mb-1'>" . htmlspecialchars($note['username']) . "</p>";
                echo "<p class='mb-1 text-muted'> •" . htmlspecialchars($date) . "</p></div>";
                echo "<div><h4 class='mb-2'>" . htmlspecialchars($note['title']) . "</h4>";
                echo "<p class='mt-2 text-muted'>" . htmlspecialchars($excerpt) . "</p></div>";
                echo "<p class='subject-pill mb-1'> " . htmlspecialchars($note['subject_name']) . "</p>";
                echo "<div><p class='mt-2'>" . "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-heart-fill like-heart' viewBox='0 0 16 16'>
                    <path fill-rule='evenodd' d='M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314'/>
                    </svg>" . htmlspecialchars($note['likes']) . "</p></div>"; 
                echo "</div></div></div>";
            }
        }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>