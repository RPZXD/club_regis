<?php
require_once('../config/Database.php');

class ClubMember {
    public static function getMembersByClub($club_id) {
        $db = new Database('phichaia_club');
        $pdo = $db->getConnection();
        // เชื่อมต่อฐานข้อมูล student แยกต่างหาก
        $dbStudent = new Database('phichaia_student');
        $pdoStudent = $dbStudent->getConnection();

        // ดึง student_id ทั้งหมดใน club นี้
        $sql = "SELECT m.id as member_id, m.student_id
                FROM club_members m
                WHERE m.club_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$club_id]);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($members as $idx => $member) {
            // ดึงข้อมูลนักเรียนจากฐานข้อมูล student
            $stmtStu = $pdoStudent->prepare("SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room FROM student WHERE Stu_id = ?");
            $stmtStu->execute([$member['student_id']]);
            $stu = $stmtStu->fetch(PDO::FETCH_ASSOC);
            if ($stu) {
                $result[] = [
                    'member_id' => $member['member_id'],
                    'student_id' => $stu['Stu_id'],
                    'student_name' => $stu['Stu_pre'].$stu['Stu_name'] . ' ' . $stu['Stu_sur'],
                    'class_room' => 'ม.'.$stu['Stu_major'] . '/' . $stu['Stu_room']
                ];
            }
        }
        return $result;
    }

    public static function deleteMember($member_id) {
        $db = new Database('phichaia_club');
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("DELETE FROM club_members WHERE id = ?");
        return $stmt->execute([$member_id]);
    }
}
