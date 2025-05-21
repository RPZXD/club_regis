<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/Club.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\Club;

$db = new DatabaseClub();
$dbUsers = new DatabaseUsers();
$pdo = $db->getPDO(); // สมมติว่า DatabaseClub มีเมธอด getPDO()
$clubModel = new Club($pdo);

// เรียกใช้งาน TermPee ทุก action
$termPee = \TermPee::getCurrent();
$current_term = $termPee->term;
$current_year = $termPee->pee;

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

switch ($action) {
    case 'list':
        // ดึงข้อมูลชุมนุมทั้งหมด เฉพาะ term/year ปัจจุบัน
        $clubs = $clubModel->getAll($current_term, $current_year);
        foreach ($clubs as &$club) {
            $advisor = $dbUsers->getTeacherByUsername($club['advisor_teacher']);
            $club['advisor_teacher_name'] = $advisor ? $advisor['Teach_name'] : $club['advisor_teacher'];
            $club['current_members_count'] = $clubModel->getCurrentMembers($club['club_id']);
        }
        echo json_encode(['data' => $clubs, 'term' => $current_term, 'year' => $current_year]);
        exit;

    case 'list_by_advisor':
        // ดึงเฉพาะชุมนุมที่ advisor_teacher ตรงกับครูที่ล็อกอิน เฉพาะ term/year ปัจจุบัน
        $advisor_teacher = $_SESSION['username'] ?? '';
        $clubs = $clubModel->getByAdvisor($advisor_teacher, $current_term, $current_year);
        foreach ($clubs as &$club) {
            $advisor = $dbUsers->getTeacherByUsername($club['advisor_teacher']);
            $club['advisor_teacher_name'] = $advisor ? $advisor['Teach_name'] : $club['advisor_teacher'];
            $club['current_members_count'] = $clubModel->getCurrentMembers($club['club_id'], $current_term, $current_year);
        }
        echo json_encode(['data' => $clubs, 'term' => $current_term, 'year' => $current_year]);
        exit;

    case 'create':
        $club_name = $_POST['club_name'] ?? '';
        $description = $_POST['description'] ?? '';
        $grade_levels = $_POST['grade_levels'] ?? '';
        $max_members = $_POST['max_members'] ?? 0;
        $advisor_teacher = $_SESSION['username'] ?? 'unknown';
        // ใช้ term/year ปัจจุบันจาก TermPee
        $term = $current_term;
        $year = $current_year;

        $data = [
            'club_name' => $club_name,
            'description' => $description,
            'grade_levels' => $grade_levels,
            'max_members' => $max_members,
            'advisor_teacher' => $advisor_teacher,
            'term' => $term,
            'year' => $year
        ];
        try {
            $clubModel->create($data);
            echo json_encode(['success' => true, 'term' => $term, 'year' => $year]);
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

        $club = $clubModel->getById($club_id, $current_term, $current_year);
        if (!$club || $club['advisor_teacher'] !== $advisor_teacher) {
            echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์แก้ไข']);
            exit;
        }

        $data = [
            'club_name' => $club_name,
            'description' => $description,
            'grade_levels' => $grade_levels,
            'max_members' => $max_members
        ];
        try {
            $clubModel->update($club_id, $data, $current_term, $current_year);
            echo json_encode(['success' => true, 'term' => $current_term, 'year' => $current_year]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'delete':
        $club_id = $_POST['club_id'] ?? '';
        $advisor_teacher = $_SESSION['username'] ?? '';

        $club = $clubModel->getById($club_id, $current_term, $current_year);
        if (!$club || $club['advisor_teacher'] !== $advisor_teacher) {
            echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์ลบ']);
            exit;
        }

        try {
            $clubModel->delete($club_id, $current_term, $current_year);
            echo json_encode(['success' => true, 'term' => $current_term, 'year' => $current_year]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'members':
        $club_id = $_GET['club_id'] ?? '';
        if (!$club_id) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบ club_id']);
            exit;
        }
        // ดึงรายชื่อสมาชิกในชุมนุม เฉพาะ term/year ปัจจุบัน
        $stmt = $pdo->prepare("SELECT * FROM club_members WHERE club_id = :club_id AND term = :term AND year = :year");
        $stmt->execute(['club_id' => $club_id, 'term' => $current_term, 'year' => $current_year]);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ดึงข้อมูลนักเรียนจากฐานข้อมูล student
        require_once __DIR__ . '/../classes/DatabaseUsers.php';
        $dbUsers = new \App\DatabaseUsers();
        $result = [];
        foreach ($members as $row) {
            $stu = $dbUsers->getStudentByUsername($row['student_id']);
            $result[] = [
                'student_id' => $row['student_id'],
                'name' => $stu ? $stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur'] : '',
                'class_name' => $stu ? ('ม.'.$stu['Stu_major'].'/'.$stu['Stu_room'] ?? '') : '',
                'created_at' => $row['created_at'] // เพิ่ม created_at
            ];
        }
        echo json_encode(['success' => true, 'members' => $result, 'term' => $current_term, 'year' => $current_year]);
        exit;

    case 'delete_member':
        $student_id = $_POST['student_id'] ?? '';
        $club_id = $_POST['club_id'] ?? '';
        if (!$student_id || !$club_id) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            exit;
        }
        // ตรวจสอบสิทธิ์: ต้องเป็นครูที่ปรึกษาชุมนุมนั้น
        $advisor_teacher = $_SESSION['username'] ?? '';
        $club = $clubModel->getById($club_id, $current_term, $current_year);
        if (!$club || $club['advisor_teacher'] !== $advisor_teacher) {
            echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์ลบสมาชิก']);
            exit;
        }
        // ลบสมาชิก เฉพาะ term/year ปัจจุบัน
        $stmt = $pdo->prepare("DELETE FROM club_members WHERE student_id = :student_id AND club_id = :club_id AND term = :term AND year = :year");
        $success = $stmt->execute(['student_id' => $student_id, 'club_id' => $club_id, 'term' => $current_term, 'year' => $current_year]);
        echo json_encode(['success' => $success, 'term' => $current_term, 'year' => $current_year]);
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action', 'term' => $current_term, 'year' => $current_year]);
        exit;
}
