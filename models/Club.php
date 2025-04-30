<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/TermPee.php';

class ClubModel {
    private $conn;

    public function __construct() {
        $db = new Database('phichaia_club');
        $this->conn = $db->getConnection();
    }

    public function getAllClubsWithCurrentMembers() {
        $termPeeModel = new TermPeeModel();
        $termpees = $termPeeModel->getTermPee();
        $term = $termpees['term'];
        $pee = $termpees['pee'];

        $sql = "SELECT 
                    c.club_id, 
                    c.club_name, 
                    c.description, 
                    c.advisor_teacher, 
                    c.grade_levels, 
                    c.max_members,
                    (
                        SELECT COUNT(*) 
                        FROM club_members m 
                        WHERE m.club_id = c.club_id 
                    ) AS current_members
                FROM clubs c
                WHERE c.term = :term AND c.year = :pee";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
        $stmt->execute();
        $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $clubs;
    }

    public function createClub($data) {
        $termPeeModel = new TermPeeModel();
        $termpees = $termPeeModel->getTermPee();
        $term = $termpees['term'];
        $pee = $termpees['pee'];

        $sql = "INSERT INTO clubs (club_name, description, advisor_teacher, grade_levels, max_members, term, year)
                VALUES (:club_name, :description, :advisor_teacher, :grade_levels, :max_members, :term, :pee)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':club_name', $data['club_name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':advisor_teacher', $data['advisor_teacher']);
        $stmt->bindParam(':grade_levels', $data['grade_levels']);
        $stmt->bindParam(':max_members', $data['max_members']);
        $stmt->bindParam(':term', $term);
        $stmt->bindParam(':pee', $pee);
        return $stmt->execute();
    }

    public function deleteClub($club_id) {
        // ตรวจสอบว่ามีสมาชิกใน club หรือไม่
        $sqlCheck = "SELECT COUNT(*) FROM club_members WHERE club_id = :club_id";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':club_id', $club_id);
        $stmtCheck->execute();
        $memberCount = $stmtCheck->fetchColumn();

        if ($memberCount > 0) {
            // มีสมาชิกใน club, ไม่อนุญาตให้ลบ
            return false;
        }

        // ไม่มีสมาชิก, สามารถลบได้
        $sql = "DELETE FROM clubs WHERE club_id = :club_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':club_id', $club_id);
        return $stmt->execute();
    }

    public function updateClub($data) {
        $sql = "UPDATE clubs 
                SET club_name = :club_name, 
                    description = :description, 
                    grade_levels = :grade_levels, 
                    max_members = :max_members
                WHERE club_id = :club_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':club_name', $data['club_name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':grade_levels', $data['grade_levels']);
        $stmt->bindParam(':max_members', $data['max_members']);
        $stmt->bindParam(':club_id', $data['club_id']); // club_id เป็น string (เช่น CL25680004) ไม่ต้องใช้ PDO::PARAM_INT
        return $stmt->execute();
    }

    public function getClubsByAdvisor($advisor) {
        $sql = "SELECT 
                    c.club_id, 
                    c.club_name, 
                    c.advisor_teacher,
                    t.Teach_name AS advisor_teacher_name,
                    t.Teach_phone AS advisor_phone
                FROM clubs c
                LEFT JOIN phichaia_student.teacher t ON c.advisor_teacher = t.Teach_id
                WHERE c.advisor_teacher = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$advisor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClubDetail($club_id) {
        $sql = "SELECT 
                    c.club_id, 
                    c.club_name, 
                    c.advisor_teacher,
                    t.Teach_name AS advisor_teacher_name,
                    t.Teach_phone AS advisor_phone
                FROM clubs c
                LEFT JOIN phichaia_student.teacher t ON c.advisor_teacher = t.Teach_id
                WHERE c.club_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$club_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
