<?php
// app/models/Subject.php
class Subject {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create a new subject in the database
    public function createSubject($classroom_id, $name, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO classroom_subjects (classroom_id, subject_name, subject_desc) VALUES (?, ?, ?)");
        $stmt->execute([$classroom_id, $name, $description]);
    }
    
    public function getSubject($subject_id){
        $stmt = $this->pdo->prepare("SELECT * FROM classroom_subjects WHERE subject_id = ?");
        $stmt->execute([$subject_id]);
        return $stmt->fetch();
    }

    public function updateSubject($subject_id, $name, $desc) {
        $stmt = $this->pdo->prepare("UPDATE classroom_subjects SET subject_name = ?, subject_desc = ?WHERE subject_id = ?");
        return $stmt->execute([$name, $desc, $subject_id]);
    }

    public function deleteSubject($subject_id) {
        $stmt = $this->pdo->prepare("DELETE FROM classroom_subjects WHERE subject_id = ?");
        return $stmt->execute([$subject_id]);
    }
}
