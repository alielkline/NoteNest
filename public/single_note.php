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

$note_id = $_GET['note_id'] ?? null;

if (!$note_id) {
    die("No note ID specified.");
}

//Fetch the note & comments data
$stmt = $pdo->prepare("SELECT cn.*, username, profile_image, subject_name
FROM classroom_notes cn
JOIN users u ON u.id = cn.uploader_user_id
JOIN classroom_subjects cs ON cs.subject_id = cn.subject_id
WHERE cn.note_id = ?;");
$stmt->execute([$note_id]);
$note = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT c.*, username, profile_image
FROM comments c
JOIN users u ON u.id = c.user_id
WHERE c.note_id = ?;");
$stmt->execute([$note_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Fetch the likes & bookmarks data
$stmt = $pdo->prepare("SELECT 1 FROM likes WHERE user_id = ? AND note_id = ?");
$stmt->execute([$user_id, $note_id]);
$userHasLiked = $stmt->fetch() ? true : false;

$stmt = $pdo->prepare("SELECT 1 FROM bookmarks WHERE user_id = ? AND note_id = ?");
$stmt->execute([$user_id, $note_id]);
$userHasBookmarked = $stmt->fetch() ? true : false;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/single_note.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <title>Note</title>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <script src="../js/single_note.js"></script>

    <div class="container">
        <div class="row mt-5">
            <div class="col-8">
                <div class="note-pane border">
                    <?php
                        $d = strtotime($note['upload_date']);
                        $date = date("M d, Y, h:i A", $d);
                        $heartIconClass = $userHasLiked ? "bi-heart-fill" : "bi-heart";
                        $bookmarkIconClass = $userHasBookmarked ? "bi-bookmark-fill" : "bi-bookmark";

                        $fileSegment = "";

                        if(!$note['profile_image']) 
                        {$note['profile_image'] = 'profile-default.jpg';
                        }

                        if(isset($note['attachment']) && $note['attachment'] !== NULL){

                        $parts = explode("/", $note['attachment']);

                        $fileName = end($parts);
                        $fileSize = round((filesize($note['attachment']) / 1000000), 3);
                        
                        //The download/attachment section
                        $fileSegment =
                        
                        "<p class='attachment-title'>Attachments</p>
                        <div class='d-flex justify-content-between download-bg'>
                            <div class='d-flex'>
                                <div class='download-icon-bg p-2 px-3 m-3'>

                                    <i class='bi bi-book'></i>

                                </div>
                                <div>
                                    <p class='mt-3 mb-0'>" . htmlspecialchars($fileName) . "</p>
                                    <p class='text-muted'>" . htmlspecialchars($fileSize) . " MB</p>
                                </div>
                            </div>
                            <div>
                                <a class='btn download-btn m-4'>Download</a>
                            </div>

                        </div>";
                        }

                        echo
                        "<div class='d-flex justify-content-between'>
                            <div class='d-flex'>
                                    <img class='single-note-pfp' src='../uploads/profile_images/" . htmlspecialchars($note['profile_image']) . "'>
                                    <div>
                                        <p class='my-0'>" . htmlspecialchars($note['username']) . "</p>
                                        <p class='text-muted date'>" . htmlspecialchars($date) . "</p>
                                    </div>       
                            </div>                       
                            <div class='d-flex align-items-center'>
                                <span id='like-btn' data-note-id='" . $note['note_id'] . "' onclick='toggleLike(this)' class='d-flex align-items-center px-2 like-button'>
                                    <i class='bi " . $heartIconClass . " mx-1 like-heart'></i>

                                    <span class='mx-1 note-likes' id='like-count'>" . htmlspecialchars($note['likes']) . "</span>
                                </span>

                                <span id='bookmark-btn' data-note-id='" . $note['note_id'] . "'onclick='toggleBookmark(this)' class='mx-3 ms-5 p-2 bookmark-button'>
                                    <i class='bi " . $bookmarkIconClass . " bookmark-icon d-flex align-items-center'></i>
                                </span>

                            </div>
                        
                        </div>
                        <h1 class='note-title'>" . htmlspecialchars($note['title']) . "</h1>
                        <p class='subject-pill px-2 py-1'> " . htmlspecialchars($note['subject_name']) . "</p>
                        <p>" . htmlspecialchars($note['content']) . "</p> 
                        <div>" . $fileSegment . "</div>
                        ";
                    ?>
                </div>
            </div>
            <div class="col-4 note-pane border">
                <span class="d-flex align-items-center mb-3">

                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-chat-left comments-icon" viewBox="0 0 16 16">
                <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                </svg>
                
                <?php
                    $commentsAmount = count($comments);
                    echo "<p class='px-2 my-0 comments-text'>Comments (" . $commentsAmount . ")</p>" 
                ?>
                </span>
                <form method="post" class="form-group" action="../includes/add_comment.php">
                    <input type="hidden" name="note_id" value="<?php echo $note['note_id']?>">
                    <textarea class="form-control w-100 comment-textarea" name="content" rows="3" placeholder="Write a comment..." required></textarea>
                    <button type="submit" class="btn-add-comment mt-2">Add Comment</button>
                </form>
                <hr class="text-black-50">    
                <?php
                foreach ($comments as $comment) {
                    $d = strtotime($comment['comment_date']);
                    $date = date("M d, Y, h:i A", $d);

                    echo 
                    "<div class='comment-pane'>
                        <div class='d-flex p-2'>
                            <img class='comment-pfp' src='../uploads/profile_images/" . htmlspecialchars($comment['profile_image']) . "'>
                            <div>
                                <p class='my-0'>" . htmlspecialchars($comment['username']) . "</p>
                                <p class='text-muted date mb-0'>" . htmlspecialchars($date) . "</p>
                            </div>
                        </div>                   
                        <p class='p-3 pb-2 pt-0'>" . htmlspecialchars($comment['comment_text']) . "</p>
                    </div>";
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>