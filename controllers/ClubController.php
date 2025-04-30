<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';

use App\DatabaseClub;
use App\DatabaseUsers;

$db = new DatabaseClub();
$dbUsers = new DatabaseUsers();

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

switch ($action) {
    case 'list':
        // ดึงข้อมูลชุมนุมทั้งหมด
        $stmt = $db->query("SELECT * FROM clubs");
        $clubs = $stmt->fetchAll();
        // เพิ่มชื่อครูที่ปรึกษา (ถ้ามี)
        foreach ($clubs as &$club) {
            $advisor = $dbUsers->getTeacherByUsername($club['advisor_teacher']);
            $club['advisor_teacher_name'] = $advisor ? $advisor['Teach_name'] : $club['advisor_teacher'];
            // หากไม่มี current_members ในฐานข้อมูล ให้กำหนดเป็น 0
            if (!isset($club['current_members'])) $club['current_members'] = 0;
        }
        echo json_encode(['data' => $clubs]);
        exit;

    case 'create':
        $club_name = $_POST['club_name'] ?? '';
        $description = $_POST['description'] ?? '';
        $grade_levels = $_POST['grade_levels'] ?? '';
        $max_members = $_POST['max_members'] ?? 0;
        $advisor_teacher = $_SESSION['username'] ?? 'unknown';

        $sql = "INSERT INTO clubs (club_name, description, grade_levels, max_members, current_members, advisor_teacher, status)
                VALUES (:club_name, :description, :grade_levels, :max_members, 0, :advisor_teacher, 'active')";
        $params = [
            'club_name' => $club_name,
            'description' => $description,
            'grade_levels' => $grade_levels,
            'max_members' => intval($max_members),
            'advisor_teacher' => $advisor_teacher
        ];
        try {
            $db->query($sql, $params);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'update':
        $club_id = $_POST['club_id'] ?? '';
        $club_name = $_POST['club_name'] ?? '';
        $description = $_POST['description'] ?? '';
        $grade_levels = $_POST['grade_levels'] ?? '';
        $max_members = $_POST['max_members'] ?? 0;
        $advisor_teacher = $_SESSION['username'] ?? '';

        // ตรวจสอบสิทธิ์
        $stmt = $db->query("SELECT * FROM clubs WHERE club_id = :club_id", ['club_id' => $club_id]);
        $club = $stmt->fetch();
        if (!$club || $club['advisor_teacher'] !== $advisor_teacher) {
            echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์แก้ไข']);
            exit;
        }

        $sql = "UPDATE clubs SET club_name = :club_name, description = :description, grade_levels = :grade_levels, max_members = :max_members WHERE club_id = :club_id";
        $params = [
            'club_id' => $club_id,
            'club_name' => $club_name,
            'description' => $description,
            'grade_levels' => $grade_levels,
            'max_members' => intval($max_members)
        ];
        try {
            $db->query($sql, $params);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'delete':
        $club_id = $_POST['club_id'] ?? '';
        $advisor_teacher = $_SESSION['username'] ?? '';

        // ตรวจสอบสิทธิ์
        $stmt = $db->query("SELECT * FROM clubs WHERE club_id = :club_id", ['club_id' => $club_id]);
        $club = $stmt->fetch();
        if (!$club || $club['advisor_teacher'] !== $advisor_teacher) {
            echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์ลบ']);
            exit;
        }

        $sql = "DELETE FROM clubs WHERE club_id = :club_id";
        try {
            $db->query($sql, ['club_id' => $club_id]);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
