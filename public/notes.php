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
                    <button class="btn btn-notes-dropdown dropdown-toggle border w-100" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        All Classrooms
                    </button>
                    <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                        
                    

                        <button class="dropdown-item" href="#">All Classrooms</button>

                        <?php
                        $stmt = $pdo->prepare("SELECT name FROM classrooms t1 
                        JOIN classroom_members t2 ON t1.classroom_id = t2.classroom_id
                        JOIN users u ON u.id = t2.user_id
                        WHERE username = :username;");

                        $stmt->bindParam(":username", $_SESSION["username"]);
                        $stmt->execute();

                        $classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach($classrooms as $classroom)
                        {
                            echo "<button class='dropdown-item'>" . $classroom . "</button>";
                        }

                        ?>

                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3 mb-md-0">
                <div class="dropdown">

                    <!--Notes Dropdown!-->
                    <button class="btn btn-notes-dropdown dropdown-toggle border w-100" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        All Notes 
                    </button>
                    <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">All Notes</a>
                        <a class="dropdown-item" href="#">Liked Notes</a>
                        <a class="dropdown-item" href="#">Bookmarked Notes</a>
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
        t1.title, 
        t1.upload_date,
        t1.content,
        t2.subject_name, 
        u.username,
        COUNT(l.like_id) AS likes
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
            <h4>No notes currently!</h4>
            <a href="classrooms.php" class="btn-no-notes">Join a classroom!</a>
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

