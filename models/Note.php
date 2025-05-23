<?php
class Note
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getNotes($classroom_filter = null, $sort_order = 'DESC')
    {
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


    public function getNotesByUserId($user_id, $classroom_filter = null, $sort_order = 'DESC')
    {
        $query = "SELECT cn.* FROM classroom_notes cn
                  JOIN classroom_subjects cs ON cn.subject_id = cs.subject_id
                  WHERE cn.uploader_user_id = ?";
        $params = [$user_id];

        if ($classroom_filter) {
            $query .= " AND cs.classroom_id = ?";
            $params[] = $classroom_filter;
        }

        $query .= " ORDER BY cn.upload_date $sort_order";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFilteredNotes($user_id, $filters = [])
    {
        $query = "
            SELECT cn.*, cs.subject_name, u.username
        FROM classroom_notes cn
        JOIN classroom_subjects cs ON cn.subject_id = cs.subject_id
        JOIN classroom_members cm ON cs.classroom_id = cm.classroom_id
        JOIN users u ON u.id = cn.uploader_user_id
        WHERE cm.user_id = ?
            
        ";

        $params = [$user_id];
        if (!empty($filters['classroom_id']) && $filters['classroom_id'] !== 'all') {
            $query .= " AND cs.classroom_id = ?";
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

    public function createNote($user_id, $title, $content, $visibility, $attachmentPath, $subject_id)
    {
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

    public function deleteNote($note_id, $subject_id) {
        $stmt = $this->pdo->prepare("UPDATE classroom_subjects SET notes = notes - 1 WHERE subject_id = ?");
        $stmt->execute([$subject_id]);

        $stmt = $this->pdo->prepare("DELETE FROM classroom_notes WHERE note_id = ?");
        return $stmt->execute([$note_id]);
    }

    public function updateNote($note_id, $title, $content, $visibility, $attachment) {
    
    $query = "UPDATE classroom_notes SET title = ?, content = ?, visibility = ?";
    
    
    if ($attachment) {
        $query .= ", attachment = ?";
    }


    
    $query .= " WHERE note_id = ?";

    
    $stmt = $this->pdo->prepare($query);
    
    // Bind parameters
    if ($attachment) {
        return $stmt->execute([$title, $content, $visibility, $attachment, $note_id]);
    } else {
        return $stmt->execute([$title, $content, $visibility, $note_id]);
    }
}

    public function incrementNoteCount($subject_id)
    {
        $stmt = $this->pdo->prepare("
            UPDATE classroom_subjects 
            SET notes = notes + 1 
            WHERE subject_id = :subject_id
        ");
        $stmt->execute([':subject_id' => $subject_id]);
    }

    public function getNotesBySubjectId($subject_id)
    {
        $query = "SELECT * FROM classroom_notes
                  WHERE subject_id = ?";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$subject_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNoteWithDetails($note_id)
    {
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

    public function toggleLike($user_id, $note_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND note_id = ?");
        $stmt->execute([$user_id, $note_id]);

        if ($stmt->rowCount() > 0) {
            // Unlike
            $this->pdo->prepare("DELETE FROM likes WHERE user_id = ? AND note_id = ?")->execute([$user_id, $note_id]);
            $this->pdo->prepare("UPDATE classroom_notes SET likes = likes - 1 WHERE note_id = ?")->execute([$note_id]);
        } else {
            // Like
            $this->pdo->prepare("INSERT INTO likes (user_id, note_id) VALUES (?, ?)")->execute([$user_id, $note_id]);
            $this->pdo->prepare("UPDATE classroom_notes SET likes = likes + 1 WHERE note_id = ?")->execute([$note_id]);
        }

        $countStmt = $this->pdo->prepare("SELECT likes FROM classroom_notes WHERE note_id = ?");
        $countStmt->execute([$note_id]);
        return $countStmt->fetchColumn();
    }

    public function getComments($note_id)
    {
        $stmt = $this->pdo->prepare("SELECT c.*, username, profile_image
        FROM comments c
        JOIN users u ON u.id = c.user_id
        WHERE c.note_id = ?;");
        $stmt->execute([$note_id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $comments;
    }

    public function userHasLiked($user_id, $note_id)
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM likes WHERE user_id = ? AND note_id = ?");
        $stmt->execute([$user_id, $note_id]);
        return $stmt->fetch() ? true : false;
    }


    public function addComment($note_id, $user_id, $comment_content)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO comments (note_id, user_id, comment_text, comment_date)
            VALUES (:note_id, :user_id, :content, NOW())
        ");
        $stmt->execute([
            ':note_id' => $note_id,
            ':user_id' => $user_id,
            ':content' => $comment_content
        ]);
    }
}
