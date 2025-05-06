<?php
class Classroom {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getClassroomsByUserId($user_id) {
        $stmt = $this->pdo->prepare("SELECT c.* FROM classrooms c 
                                    JOIN classroom_members cm ON c.classroom_id = cm.classroom_id
                                    WHERE cm.user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name, $description, $visibility, $invite_code, $creator_id) {
        $stmt = $this->pdo->prepare("INSERT INTO classrooms (name, description, visibility, invite_code, creator_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $visibility, $invite_code, $creator_id]);
        return $this->pdo->lastInsertId();
    }

    public function addMember($user_id, $classroom_id) {
        $stmt = $this->pdo->prepare("INSERT INTO classroom_members (user_id, classroom_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $classroom_id]);

        $stmt = $this->pdo->prepare("UPDATE classrooms SET members = members + 1 WHERE classroom_id = ?");
        $stmt->execute([$classroom_id]);
    }

    public function getPublicClassroomsNotJoined($userId) {
        $stmt = $this->pdo->prepare("
            SELECT c.* FROM classrooms AS c
            WHERE c.visibility = 'public' 
            AND c.classroom_id NOT IN (
                SELECT cm.classroom_id FROM classroom_members AS cm WHERE cm.user_id = ?
            )
            ORDER BY c.members
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getClassroomByInviteCode($invite_code){
        $stmt = $this->pdo->prepare("SELECT * FROM classrooms WHERE invite_code = ?");
        $stmt->execute([$invite_code]);
        return $stmt->fetch();
    }

    public function isUserInClassroom($user_id, $classroom_id) {
        $stmt = $this->pdo->prepare("SELECT 1 FROM classroom_members WHERE user_id = ? AND classroom_id = ?");
        $stmt->execute([$user_id, $classroom_id]);
        return $stmt->fetchColumn() !== false;
    }

    public function getClassroomById($classroom_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM classrooms WHERE classroom_id = ?");
        $stmt->execute([$classroom_id]);
        return $stmt->fetch();
    }

    public function getSubjects($classroom_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM classroom_subjects WHERE classroom_id = ?");
        $stmt->execute([$classroom_id]);
        return $stmt->fetchAll();
    }

    public function isMember($user_id, $classroom_id) {
        $stmt = $this->pdo->prepare("SELECT 1 FROM classroom_members WHERE user_id = ? AND classroom_id = ?");
        $stmt->execute([$user_id, $classroom_id]);
        return $stmt->fetchColumn() !== false;
    }

    public function deleteClassroom($classroom_id) {
        $stmt = $this->pdo->prepare("DELETE FROM classrooms WHERE classroom_id = ?");
        return $stmt->execute([$classroom_id]);
    }

    public function removeMember($user_id, $classroom_id) {
        // Remove the user from the classroom_members table
        $stmt = $this->pdo->prepare("DELETE FROM classroom_members WHERE user_id = ? AND classroom_id = ?");
        $stmt->execute([$user_id, $classroom_id]);

        // Update the classroom's member count
        $stmt = $this->pdo->prepare("UPDATE classrooms SET members = members - 1 WHERE classroom_id = ?");
        $stmt->execute([$classroom_id]);
    }

    public function updateClassroom($classroom_id, $name, $description, $visibility)
    {
        $stmt = $this->pdo->prepare("UPDATE classrooms SET name = ?, description = ?, visibility = ? WHERE classroom_id = ?");
        return $stmt->execute([$name, $description, $visibility, $classroom_id]);
    }

}
