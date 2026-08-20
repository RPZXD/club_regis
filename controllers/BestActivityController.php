<?php
session_start();

header('Content-Type: application/json');
header('Cache-Control: private, max-age=60');

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/BestActivity.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\BestActivity;

// Initialize
$db = new DatabaseClub();
$pdo = $db->getPDO();
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$bestModel = new BestActivity($pdo, false);
$dbUsers = new DatabaseUsers();

$termPee = \TermPee::getCurrent();
$current_year = intval($termPee->pee ?: (date('Y') + 543));

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

function jsonError($message, $extra = []) {
    echo json_encode(array_merge(['success' => false, 'message' => $message], $extra));
    exit;
}

function checkRegistrationTime($stuGrade) {
    static $settings_cache = null;
    static $cache_time = null;
    
    if ($settings_cache === null || (time() - $cache_time) > 300) {
        $best_setting_file = __DIR__ . '/../best_regis_setting.json';
        if (file_exists($best_setting_file)) {
            $settings_cache = json_decode(file_get_contents($best_setting_file), true);
            $cache_time = time();
        } else {
            $settings_cache = [];
        }
    }
    
    if (isset($settings_cache[$stuGrade])) {
        $regis_start = $settings_cache[$stuGrade]['regis_start'] ?? '';
        $regis_end = $settings_cache[$stuGrade]['regis_end'] ?? '';
        
        if ($regis_start && $regis_end) {
            $now = new DateTime();
            $start = new DateTime($regis_start);
            $end = new DateTime($regis_end);
            
            if ($now < $start) {
                return ['valid' => false, 'message' => 'ยังไม่ถึงเวลาเปิดรับสมัครกิจกรรม Best สำหรับ ' . $stuGrade];
            }
            if ($now > $end) {
                return ['valid' => false, 'message' => 'หมดเวลาการสมัครกิจกรรม Best สำหรับ ' . $stuGrade . ' แล้ว'];
            }
        }
    }
    return ['valid' => true];
}

function validateGradeLevel($activity, $stu) {
    $allowed = array_map('trim', preg_split('/[ ,\/]+/', $activity['grade_levels']));
    $stuGrade = 'ม.' . $stu['Stu_major'];
    
    if (!in_array($stuGrade, $allowed, true)) {
        return ['valid' => false, 'message' => 'ระดับชั้น ' . $stuGrade . ' ไม่สามารถสมัครกิจกรรมนี้ได้'];
    }
    return ['valid' => true, 'grade' => $stuGrade];
}

switch ($action) {
    case 'years':
        try {
            $years = $bestModel->getDistinctYears();
            if ($current_year > 0 && !in_array($current_year, $years)) {
                array_unshift($years, $current_year);
            }
            if (empty($years)) {
                $years = [$current_year];
            }
            $years = array_values(array_unique(array_map('intval', $years)));
            rsort($years);
            echo json_encode([
                'success' => true, 
                'years' => $years, 
                'current_year' => $current_year
            ]);
        } catch (Exception $e) {
            jsonError('เกิดข้อผิดพลาดในการโหลดปีการศึกษา: ' . $e->getMessage());
        }
        exit;

    case 'search_students':
        $query = trim($_GET['q'] ?? ($_GET['query'] ?? ''));
        $req_year = isset($_GET['year']) && intval($_GET['year']) > 0 ? intval($_GET['year']) : $current_year;
        
        if (mb_strlen($query, 'UTF-8') < 1) {
            echo json_encode(['success' => true, 'students' => []]);
            exit;
        }

        try {
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

            // Fetch registration info for Best activities in this year
            $bestRegStmt = $pdo->prepare("
                SELECT bm.student_id, bm.activity_id, ba.name as activity_name 
                FROM best_members bm 
                LEFT JOIN best_activities ba ON bm.activity_id = ba.id AND bm.year = ba.year
                WHERE bm.year = :year
            ");
            $bestRegStmt->execute(['year' => $req_year]);
            $registeredMap = [];
            while ($row = $bestRegStmt->fetch(PDO::FETCH_ASSOC)) {
                $registeredMap[$row['student_id']] = [
                    'activity_id' => $row['activity_id'],
                    'activity_name' => $row['activity_name'] ?? 'กิจกรรม Best'
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
                    'registered_activity_id' => $regInfo ? $regInfo['activity_id'] : null,
                    'registered_activity_name' => $regInfo ? $regInfo['activity_name'] : null
                ];
            }

            echo json_encode(['success' => true, 'students' => $list]);
        } catch (Exception $e) {
            jsonError('ค้นหานักเรียนไม่สำเร็จ: ' . $e->getMessage());
        }
        exit;

    case 'list':
        try {
            $req_year = isset($_GET['year']) && intval($_GET['year']) > 0 ? intval($_GET['year']) : $current_year;
            $activities = $bestModel->getAllWithMemberCounts($req_year);
            echo json_encode([
                'success' => true, 
                'data' => $activities, 
                'year' => $req_year,
                'current_year' => $current_year,
                'is_current_year' => ($req_year === $current_year)
            ]);
        } catch (Exception $e) {
            jsonError('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . $e->getMessage());
        }
        exit;

    case 'register':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'นักเรียน' || !isset($_SESSION['user']['Stu_id'])) {
            jsonError('unauthorized - กรุณาเข้าสู่ระบบใหม่');
        }

        $activity_id = intval($_POST['activity_id'] ?? 0);
        $student_id = $_SESSION['user']['Stu_id'];

        if ($activity_id <= 0) {
            jsonError('ข้อมูลไม่ครบถ้วน');
        }

        try {
            $activity = $bestModel->getById($activity_id);
            if (!$activity || intval($activity['year']) !== intval($current_year)) {
                jsonError('ไม่พบกิจกรรมปีนี้');
            }

            $stu = $dbUsers->getStudentByUsername($student_id);
            if (!$stu) {
                jsonError('ไม่พบข้อมูลนักเรียน');
            }

            $gradeValidation = validateGradeLevel($activity, $stu);
            if (!$gradeValidation['valid']) {
                jsonError($gradeValidation['message']);
            }

            $timeValidation = checkRegistrationTime($gradeValidation['grade']);
            if (!$timeValidation['valid']) {
                jsonError($timeValidation['message']);
            }

            $existing = $bestModel->getStudentRegistration($student_id, $current_year);
            if ($existing) {
                jsonError('คุณได้สมัครกิจกรรม "' . $existing['name'] . '" ไปแล้วในปีนี้');
            }

            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("SELECT max_members FROM best_activities WHERE id = :id AND year = :year FOR UPDATE");
                $stmt->execute(['id' => $activity_id, 'year' => $current_year]);
                $activityData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$activityData) {
                    throw new Exception('กิจกรรมไม่พบ');
                }

                $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM best_members WHERE activity_id = :id AND year = :year FOR UPDATE");
                $stmt->execute(['id' => $activity_id, 'year' => $current_year]);
                $currentCount = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];

                if ($currentCount >= intval($activityData['max_members'])) {
                    throw new Exception('กิจกรรมเต็มแล้ว (รับได้ ' . $activityData['max_members'] . ' คน)');
                }

                $stmt = $pdo->prepare("INSERT INTO best_members (activity_id, student_id, year, created_at) VALUES (:activity_id, :student_id, :year, NOW())");
                $success = $stmt->execute([
                    'activity_id' => $activity_id,
                    'student_id' => $student_id,
                    'year' => $current_year
                ]);

                if (!$success) {
                    throw new Exception('ไม่สามารถบันทึกการสมัครได้');
                }

                $pdo->commit();
                $bestModel->clearActivityCache($activity_id, $current_year);

                echo json_encode([
                    'success' => true, 
                    'message' => 'สมัครกิจกรรม "' . $activity['name'] . '" เรียบร้อยแล้ว'
                ]);

            } catch (Exception $e) {
                $pdo->rollBack();
                jsonError($e->getMessage());
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'uniq_student_year') !== false) {
                jsonError('คุณได้สมัครกิจกรรม Best For Teen ไปแล้วในปีนี้');
            }
            jsonError('เกิดข้อผิดพลาดในการสมัคร: ' . $e->getMessage());
        } catch (Exception $e) {
            jsonError('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
        exit;

    case 'create':
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $grade_levels = trim($_POST['grade_levels'] ?? '');
        $max_members = intval($_POST['max_members'] ?? 0);
        $year = isset($_POST['year']) && intval($_POST['year']) > 0 ? intval($_POST['year']) : $current_year;

        if ($name === '' || $grade_levels === '' || $max_members <= 0) {
            jsonError('ข้อมูลไม่ครบถ้วน');
        }

        $payload = [
            'name' => $name,
            'description' => $description,
            'grade_levels' => $grade_levels,
            'max_members' => $max_members,
            'year' => $year
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
        if (!$activity) jsonError('ไม่พบกิจกรรม');
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
        if (!$activity) jsonError('ไม่พบกิจกรรม');
        $ok = $bestModel->delete($id);
        echo json_encode(['success' => $ok]);
        exit;

    case 'members':
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) jsonError('ไม่พบ ID');
        
        $activity = $bestModel->getById($id);
        if (!$activity) jsonError('ไม่พบกิจกรรม');
        $actYear = intval($activity['year']);
        
        $members = $bestModel->listMembersWithStudentData($id, $actYear);
        
        echo json_encode([
            'success' => true, 
            'members' => $members, 
            'year' => $actYear,
            'activity' => $activity,
            'is_current_year' => ($actYear === $current_year)
        ]);
        exit;

    case 'add_member':
        $id = intval($_POST['id'] ?? 0);
        $student_id = trim($_POST['student_id'] ?? '');
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'นักเรียน') {
            if (isset($_SESSION['user']['Stu_id'])) {
                $student_id = $_SESSION['user']['Stu_id'];
            }
        }
        if ($id <= 0 || $student_id === '') jsonError('ข้อมูลไม่ครบถ้วน');
        $activity = $bestModel->getById($id);
        if (!$activity) jsonError('ไม่พบกิจกรรม');
        $actYear = intval($activity['year']);

        $stu = $dbUsers->getStudentByUsername($student_id);
        if (!$stu) jsonError('ไม่พบนักเรียน');
        
        $gradeValidation = validateGradeLevel($activity, $stu);
        if (!$gradeValidation['valid']) {
            jsonError($gradeValidation['message']);
        }

        if (isset($_SESSION['role']) && $_SESSION['role'] === 'นักเรียน') {
            $timeValidation = checkRegistrationTime($gradeValidation['grade']);
            if (!$timeValidation['valid']) {
                jsonError($timeValidation['message']);
            }
        }

        $current = $bestModel->countMembers($id, $actYear);
        if ($current >= intval($activity['max_members'])) {
            jsonError('กิจกรรมเต็มแล้ว');
        }

        try {
            $ok = $bestModel->addMember($id, $student_id, $actYear);
            echo json_encode(['success' => $ok]);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'uniq_student_year') !== false) {
                jsonError('นักเรียนลงทะเบียนกิจกรรม Best For Teen ไปแล้วในปีการศึกษานี้');
            }
            jsonError('บันทึกไม่ได้: ' . $e->getMessage());
        }
        exit;

    case 'remove_member':
        $id = intval($_POST['id'] ?? 0);
        $student_id = trim($_POST['student_id'] ?? '');
        if ($id <= 0 || $student_id === '') jsonError('ข้อมูลไม่ครบถ้วน');
        $activity = $bestModel->getById($id);
        $actYear = $activity ? intval($activity['year']) : $current_year;
        $ok = $bestModel->removeMember($id, $student_id, $actYear);
        echo json_encode(['success' => $ok]);
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

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
