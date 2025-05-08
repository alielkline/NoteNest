<?php
class Note {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getNotes($classroom_filter = null, $sort_order = 'DESC') {
        $query = "SELECT cn.* FROM classroom_notes cn
                  JOIN classroom_subjects cs ON cn.subject_id = cs.subject_id";
        $params = [];

        if ($classroom_filter) {
            $query .= " WHERE cs.classroom_id = ?";
            $params[] = $classroom_filter;
        }

        $query .= " ORDER BY cn.upload_date $sort_order";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNotesByUserId($user_id, $sort_order = 'DESC') {
        $query = "SELECT cn.* FROM classroom_notes cn
                  JOIN classroom_subjects cs ON cn.subject_id = cs.subject_id
                  WHERE cn.uploader_user_id = ?";
        $params = [$user_id];

        $query .= " ORDER BY cn.upload_date $sort_order";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFilteredNotes($filters = []) {
        $query = "
            SELECT cn.*, cs.subject_name, u.username
            FROM classroom_notes cn
            JOIN classroom_subjects cs ON cn.subject_id = cs.subject_id
            JOIN users u ON u.id = cn.uploader_user_id
        ";
    
        $params = [];
        if (!empty($filters['classroom_id']) && $filters['classroom_id'] !== 'all') {
            $query .= " WHERE cs.classroom_id = ?";
            $params[] = $filters['classroom_id'];
        }
    
        $query .= " ORDER BY ";
        $sorts = [];
    
        if (!empty($filters['sort_likes'])) {
            $likeOrder = $filters['sort_likes'] === 'leastLiked' ? 'ASC' : 'DESC';
            $sorts[] = "cn.likes $likeOrder";
        }
    
        if (!empty($filters['sort_date'])) {
            $dateOrder = $filters['sort_date'] === 'oldest' ? 'ASC' : 'DESC';
            $sorts[] = "cn.upload_date $dateOrder";
        }
    
        if (empty($sorts)) {
            $query .= "cn.upload_date DESC";
        } else {
            $query .= implode(', ', $sorts);
        }
    
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createNote($user_id, $title, $content, $visibility, $attachmentPath, $subject_id) {
        $stmt = $this->pdo->prepare("
            INSERT INTO classroom_notes 
                (uploader_user_id, title, content, visibility, attachment, subject_id) 
            VALUES 
                (:user_id, :title, :content, :visibility, :attachment, :subject_id)
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':content' => $content,
            ':visibility' => $visibility,
            ':attachment' => $attachmentPath,
            ':subject_id' => $subject_id
        ]);
    }

    public function incrementNoteCount($subject_id) {
        $stmt = $this->pdo->prepare("
            UPDATE classroom_subjects 
            SET notes = notes + 1 
            WHERE subject_id = :subject_id
        ");
        $stmt->execute([':subject_id' => $subject_id]);
    }
    
    public function getNotesBySubjectId($subject_id) {
        $query = "SELECT * FROM classroom_notes
                  WHERE subject_id = ?";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$subject_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNoteWithDetails($note_id) {
        $stmt = $this->pdo->prepare("
            SELECT cn.*, u.username, u.profile_image, cs.subject_name
            FROM classroom_notes cn
            JOIN users u ON u.id = cn.uploader_user_id
            JOIN classroom_subjects cs ON cs.subject_id = cn.subject_id
            WHERE cn.note_id = ?
        ");
        $stmt->execute([$note_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function hasUserLiked($user_id, $note_id) {
        $stmt = $this->pdo->prepare("SELECT 1 FROM likes WHERE user_id = ? AND note_id = ?");
        $stmt->execute([$user_id, $note_id]);
        return $stmt->fetch() ? true : false;
    }
    
    public function hasUserBookmarked($user_id, $note_id) {
        $stmt = $this->pdo->prepare("SELECT 1 FROM bookmarks WHERE user_id = ? AND note_id = ?");
        $stmt->execute([$user_id, $note_id]);
        return $stmt->fetch() ? true : false;
    }
    
    public function updateLikes($note_id, $increment = true) {
        $query = $increment 
            ? "UPDATE classroom_notes SET likes = likes + 1 WHERE note_id = ?"
            : "UPDATE classroom_notes SET likes = likes - 1 WHERE note_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$note_id]);
    }
    
    public function updateBookmarks($note_id, $increment = true) {
        $query = $increment 
            ? "UPDATE classroom_notes SET bookmarkes = bookmarkes + 1 WHERE note_id = ?"
            : "UPDATE classroom_notes SET bookmarkes = bookmarkes - 1 WHERE note_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$note_id]);
    }
    
    public function getLikeCount($note_id) {
        $stmt = $this->pdo->prepare("SELECT likes FROM classroom_notes WHERE note_id = ?");
        $stmt->execute([$note_id]);
        return $stmt->fetchColumn();
    }
    
    public function addLike($user_id, $note_id) {
        $stmt = $this->pdo->prepare("INSERT INTO likes (user_id, note_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $note_id]);
    }
    
    public function removeLike($user_id, $note_id) {
        $stmt = $this->pdo->prepare("DELETE FROM likes WHERE user_id = ? AND note_id = ?");
        $stmt->execute([$user_id, $note_id]);
    }
    
    public function addBookmark($user_id, $note_id) {
        $stmt = $this->pdo->prepare("INSERT INTO bookmarks (user_id, note_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $note_id]);
    }
    
    public function removeBookmark($user_id, $note_id) {
        $stmt = $this->pdo->prepare("DELETE FROM bookmarks WHERE user_id = ? AND note_id = ?");
        $stmt->execute([$user_id, $note_id]);
    }
    
}


