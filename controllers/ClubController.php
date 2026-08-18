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
        echo json_encode(['success' => true, 'data' => $clubs, 'term' => $current_term, 'year' => $current_year]);
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
        echo json_encode(['success' => true, 'data' => $clubs, 'term' => $current_term, 'year' => $current_year]);
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
        // ตรวจสอบสิทธิ์: ต้องเป็นครูที่ปรึกษาชุมนุมนั้น หรือเป็นเจ้าหน้าที่
        $advisor_teacher = $_SESSION['username'] ?? '';
        $club = $clubModel->getById($club_id, $current_term, $current_year);
        if (!$club || ($club['advisor_teacher'] !== $advisor_teacher && ($_SESSION['role'] ?? '') !== 'เจ้าหน้าที่')) {
            echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์ลบสมาชิก']);
            exit;
        }
        // ลบสมาชิก เฉพาะ term/year ปัจจุบัน
        $stmt = $pdo->prepare("DELETE FROM club_members WHERE student_id = :student_id AND club_id = :club_id AND term = :term AND year = :year");
        $success = $stmt->execute(['student_id' => $student_id, 'club_id' => $club_id, 'term' => $current_term, 'year' => $current_year]);
        echo json_encode(['success' => $success, 'term' => $current_term, 'year' => $current_year]);
        exit;

    case 'search_students':
        $query = trim($_GET['q'] ?? $_GET['query'] ?? '');
        if (mb_strlen($query, 'UTF-8') < 1) {
            echo json_encode(['success' => true, 'students' => []]);
            exit;
        }

        // Search in phichaia_student.student table
        $sql = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_no 
                FROM student 
                WHERE Stu_status = '1' 
                  AND (
                    Stu_id LIKE :q 
                    OR Stu_name LIKE :q 
                    OR Stu_sur LIKE :q 
                    OR CONCAT(Stu_name, ' ', Stu_sur) LIKE :q 
                    OR CONCAT(Stu_pre, Stu_name, ' ', Stu_sur) LIKE :q
                  )
                ORDER BY CAST(Stu_major AS UNSIGNED) ASC, CAST(Stu_room AS UNSIGNED) ASC, CAST(Stu_no AS UNSIGNED) ASC 
                LIMIT 20";
        $stmt = $dbUsers->query($sql, ['q' => "%$query%"]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch their current club registrations in this term/year
        $clubStmt = $pdo->prepare("
            SELECT cm.student_id, cm.club_id, c.club_name 
            FROM club_members cm 
            LEFT JOIN clubs c ON cm.club_id = c.club_id AND cm.term = c.term AND cm.year = c.year 
            WHERE cm.term = :term AND cm.year = :year
        ");
        $clubStmt->execute(['term' => $current_term, 'year' => $current_year]);
        $registeredMap = [];
        while ($row = $clubStmt->fetch(PDO::FETCH_ASSOC)) {
            $registeredMap[$row['student_id']] = [
                'club_id' => $row['club_id'],
                'club_name' => $row['club_name'] ?? 'ไม่ทราบชื่อชุมนุม'
            ];
        }

        $list = [];
        foreach ($students as $stu) {
            $sid = $stu['Stu_id'];
            $regInfo = $registeredMap[$sid] ?? null;
            $list[] = [
                'student_id' => $sid,
                'prefix' => $stu['Stu_pre'],
                'name' => $stu['Stu_name'],
                'surname' => $stu['Stu_sur'],
                'fullname' => $stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur'],
                'level' => $stu['Stu_major'],
                'room' => $stu['Stu_room'],
                'number' => $stu['Stu_no'],
                'class_name' => 'ม.' . $stu['Stu_major'] . '/' . $stu['Stu_room'] . ($stu['Stu_no'] ? ' (เลขที่ ' . $stu['Stu_no'] . ')' : ''),
                'registered_club_id' => $regInfo ? $regInfo['club_id'] : null,
                'registered_club_name' => $regInfo ? $regInfo['club_name'] : null
            ];
        }

        echo json_encode(['success' => true, 'students' => $list]);
        exit;

    case 'add_member':
        $student_id = trim($_POST['student_id'] ?? '');
        $club_id = trim($_POST['club_id'] ?? '');
        if (!$student_id || !$club_id) {
            echo json_encode(['success' => false, 'message' => 'กรุณาระบุนักเรียนและชุมนุม']);
            exit;
        }

        // Check permissions: officer or advisor
        $advisor_teacher = $_SESSION['username'] ?? '';
        $club = $clubModel->getById($club_id, $current_term, $current_year);
        if (!$club) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลชุมนุม']);
            exit;
        }
        if ($club['advisor_teacher'] !== $advisor_teacher && ($_SESSION['role'] ?? '') !== 'เจ้าหน้าที่') {
            echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์จัดการสมาชิกในชุมนุมนี้']);
            exit;
        }

        // Check if student exists
        $stu = $dbUsers->getStudentByUsername($student_id);
        if (!$stu) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลนักเรียนในระบบ']);
            exit;
        }

        // Check existing registration in this term/year
        $chkStmt = $pdo->prepare("SELECT * FROM club_members WHERE student_id = :student_id AND term = :term AND year = :year");
        $chkStmt->execute(['student_id' => $student_id, 'term' => $current_term, 'year' => $current_year]);
        $existing = $chkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            if ($existing['club_id'] == $club_id) {
                echo json_encode(['success' => false, 'message' => 'นักเรียนคนนี้เป็นสมาชิกชุมนุมนี้อยู่แล้ว']);
                exit;
            } else {
                // Update to new club
                $upStmt = $pdo->prepare("UPDATE club_members SET club_id = :club_id, created_at = NOW() WHERE student_id = :student_id AND term = :term AND year = :year");
                $upStmt->execute([
                    'club_id' => $club_id,
                    'student_id' => $student_id,
                    'term' => $current_term,
                    'year' => $current_year
                ]);
                echo json_encode(['success' => true, 'message' => 'ย้ายนักเรียนเข้าสู่ชุมนุมเรียบร้อยแล้ว']);
                exit;
            }
        } else {
            // Insert new member
            $inStmt = $pdo->prepare("INSERT INTO club_members (student_id, club_id, term, year, created_at) VALUES (:student_id, :club_id, :term, :year, NOW())");
            $inStmt->execute([
                'student_id' => $student_id,
                'club_id' => $club_id,
                'term' => $current_term,
                'year' => $current_year
            ]);
            echo json_encode(['success' => true, 'message' => 'เพิ่มนักเรียนเข้าชุมนุมสำเร็จ']);
            exit;
        }

    case 'stats':
        // ดึงสถิติภาพรวมสำหรับ Charts
        $clubs = $clubModel->getAll($current_term, $current_year);
        
        $totalClubs = count($clubs);
        $totalStudents = 0;
        $openClubs = 0;
        $fullClubs = 0;
        $gradeStats = [
            'ม.1' => 0,
            'ม.2' => 0,
            'ม.3' => 0,
            'ม.4' => 0,
            'ม.5' => 0,
            'ม.6' => 0
        ];
        $clubCapacityData = [];
        
        foreach ($clubs as $club) {
            $currentMembers = $clubModel->getCurrentMembers($club['club_id']);
            $maxMembers = (int)($club['max_members'] ?? 0);
            $totalStudents += $currentMembers;
            
            if ($currentMembers >= $maxMembers && $maxMembers > 0) {
                $fullClubs++;
            } else {
                $openClubs++;
            }
            
            // เก็บข้อมูลสำหรับ Top clubs chart
            $clubCapacityData[] = [
                'name' => mb_substr($club['club_name'], 0, 15, 'UTF-8'),
                'current' => $currentMembers,
                'max' => $maxMembers
            ];
            
            // นับตามระดับชั้น
            $grades = explode(',', $club['grade_levels'] ?? '');
            foreach ($grades as $grade) {
                $grade = trim($grade);
                if (isset($gradeStats[$grade])) {
                    $gradeStats[$grade] += $currentMembers;
                }
            }
        }
        
        // เรียงลำดับ clubs ตามจำนวนสมาชิก
        usort($clubCapacityData, function($a, $b) {
            return $b['current'] - $a['current'];
        });
        $topClubs = array_slice($clubCapacityData, 0, 8);
        
        echo json_encode([
            'success' => true,
            'totalClubs' => $totalClubs,
            'totalStudents' => $totalStudents,
            'openClubs' => $openClubs,
            'fullClubs' => $fullClubs,
            'gradeStats' => $gradeStats,
            'topClubs' => $topClubs,
            'term' => $current_term,
            'year' => $current_year
        ]);
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action', 'term' => $current_term, 'year' => $current_year]);
        exit;
}
