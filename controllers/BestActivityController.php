<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/BestActivity.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\BestActivity;

$db = new DatabaseClub();
$pdo = $db->getPDO();
$bestModel = new BestActivity($pdo);
$dbUsers = new DatabaseUsers();

$termPee = \TermPee::getCurrent();
$current_year = $termPee->pee;

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

function jsonError($message, $extra = []) {
    echo json_encode(array_merge(['success' => false, 'message' => $message], $extra));
    exit;
}

switch ($action) {
    case 'list':
        $activities = $bestModel->getAll($current_year);
        foreach ($activities as &$a) {
            $a['current_members_count'] = $bestModel->countMembers(intval($a['id']), intval($a['year']));
        }
        echo json_encode(['success' => true, 'data' => $activities, 'year' => $current_year]);
        exit;

    case 'create':
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $grade_levels = trim($_POST['grade_levels'] ?? '');
        $max_members = intval($_POST['max_members'] ?? 0);

        if ($name === '' || $grade_levels === '' || $max_members <= 0) {
            jsonError('ข้อมูลไม่ครบถ้วน');
        }

        $payload = [
            'name' => $name,
            'description' => $description,
            'grade_levels' => $grade_levels,
            'max_members' => $max_members,
            'year' => $current_year
    ];
        $ok = $bestModel->create($payload);
        echo json_encode(['success' => $ok]);
        exit;

    case 'update':
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $grade_levels = trim($_POST['grade_levels'] ?? '');
        $max_members = intval($_POST['max_members'] ?? 0);
        if ($id <= 0) jsonError('ไม่พบ ID');
        $activity = $bestModel->getById($id);
        if (!$activity || intval($activity['year']) !== intval($current_year)) jsonError('ไม่พบกิจกรรมปีนี้');
        $ok = $bestModel->update($id, [
            'name' => $name,
            'description' => $description,
            'grade_levels' => $grade_levels,
            'max_members' => $max_members,
        ]);
        echo json_encode(['success' => $ok]);
        exit;

    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) jsonError('ไม่พบ ID');
        $activity = $bestModel->getById($id);
        if (!$activity || intval($activity['year']) !== intval($current_year)) jsonError('ไม่พบกิจกรรมปีนี้');
        $ok = $bestModel->delete($id);
        echo json_encode(['success' => $ok]);
        exit;

    case 'members':
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) jsonError('ไม่พบ ID');
        $activity = $bestModel->getById($id);
        if (!$activity || intval($activity['year']) !== intval($current_year)) jsonError('ไม่พบกิจกรรมปีนี้');
        $members = $bestModel->listMembers($id, $current_year);
        // join student data
        $result = [];
        foreach ($members as $m) {
            $stu = $dbUsers->getStudentByUsername($m['student_id']);
            $result[] = [
                'student_id' => $m['student_id'],
                'name' => $stu ? ($stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur']) : $m['student_id'],
                'class_name' => $stu ? ('ม.'.$stu['Stu_major'].'/'.$stu['Stu_room']) : '',
                'created_at' => $m['created_at']
            ];
        }
        echo json_encode(['success' => true, 'members' => $result, 'year' => $current_year]);
        exit;

    case 'add_member':
        $id = intval($_POST['id'] ?? 0);
        // If role is student, force student_id from session for security
        $student_id = trim($_POST['student_id'] ?? '');
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'นักเรียน') {
            if (isset($_SESSION['user']['Stu_id'])) {
                $student_id = $_SESSION['user']['Stu_id'];
            }
        }
        if ($id <= 0 || $student_id === '') jsonError('ข้อมูลไม่ครบถ้วน');
        $activity = $bestModel->getById($id);
        if (!$activity || intval($activity['year']) !== intval($current_year)) jsonError('ไม่พบกิจกรรมปีนี้');

        // check grade level eligibility
        $stu = $dbUsers->getStudentByUsername($student_id);
        if (!$stu) jsonError('ไม่พบนักเรียน');
        $allowed = array_map('trim', preg_split('/[ ,\/]+/', $activity['grade_levels']));
        $stuGrade = 'ม.'.$stu['Stu_major'];
        if (!in_array($stuGrade, $allowed, true)) {
            jsonError('ระดับชั้นไม่ตรงกับที่กำหนด');
        }

        // one activity per year per student enforcement via unique key
        // but also check capacity
        $current = $bestModel->countMembers($id, $current_year);
        if ($current >= intval($activity['max_members'])) {
            jsonError('กิจกรรมเต็มแล้ว');
        }

        // insert
        try {
            $stmt = $pdo->prepare("INSERT INTO best_members (activity_id, student_id, year, created_at) VALUES (:activity_id, :student_id, :year, NOW())");
            $ok = $stmt->execute(['activity_id' => $id, 'student_id' => $student_id, 'year' => $current_year]);
            echo json_encode(['success' => $ok]);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'uniq_student_year') !== false) {
                jsonError('นักเรียนลงทะเบียนกิจกรรม Best For Teen ไปแล้วในปีนี้');
            }
            jsonError('บันทึกไม่ได้: '.$e->getMessage());
        }
        exit;

    case 'my_status':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'นักเรียน' || !isset($_SESSION['user']['Stu_id'])) {
            jsonError('unauthorized');
        }
        $sid = $_SESSION['user']['Stu_id'];
        $stmt = $pdo->prepare("SELECT bm.activity_id, bm.created_at, ba.name, ba.grade_levels, ba.max_members
                               FROM best_members bm
                               JOIN best_activities ba ON ba.id = bm.activity_id
                               WHERE bm.student_id = :sid AND bm.year = :year");
        $stmt->execute(['sid' => $sid, 'year' => $current_year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'registered' => !!$row, 'data' => $row]);
        exit;

    case 'remove_member':
        $id = intval($_POST['id'] ?? 0);
        $student_id = trim($_POST['student_id'] ?? '');
        if ($id <= 0 || $student_id === '') jsonError('ข้อมูลไม่ครบถ้วน');
        $stmt = $pdo->prepare("DELETE FROM best_members WHERE activity_id = :id AND student_id = :student_id AND year = :year");
        $ok = $stmt->execute(['id' => $id, 'student_id' => $student_id, 'year' => $current_year]);
        echo json_encode(['success' => $ok]);
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
