<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/Club.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\Club;

$db = new DatabaseClub();
$dbUsers = new DatabaseUsers();
$pdo = $db->getPDO(); // สมมติว่า DatabaseClub มีเมธอด getPDO()
$clubModel = new Club($pdo);

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

switch ($action) {
    case 'list':
        // ดึงข้อมูลชุมนุมทั้งหมด
        $clubs = $clubModel->getAll();
        foreach ($clubs as &$club) {
            $advisor = $dbUsers->getTeacherByUsername($club['advisor_teacher']);
            $club['advisor_teacher_name'] = $advisor ? $advisor['Teach_name'] : $club['advisor_teacher'];
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

        $data = [
            'club_name' => $club_name,
            'description' => $description,
            'grade_levels' => $grade_levels,
            'max_members' => $max_members,
            'advisor_teacher' => $advisor_teacher
        ];
        try {
            $clubModel->create($data);
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

        $club = $clubModel->getById($club_id);
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
            $clubModel->update($club_id, $data);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'delete':
        $club_id = $_POST['club_id'] ?? '';
        $advisor_teacher = $_SESSION['username'] ?? '';

        $club = $clubModel->getById($club_id);
        if (!$club || $club['advisor_teacher'] !== $advisor_teacher) {
            echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์ลบ']);
            exit;
        }

        try {
            $clubModel->delete($club_id);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
