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
    
}


