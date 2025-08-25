<?php
session_start();

header('Content-Type: application/json');
header('Cache-Control: private, max-age=60'); // Cache for 1 minute for GET requests

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/BestActivity.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\BestActivity;

// Initialize with connection pooling
$db = new DatabaseClub();
$pdo = $db->getPDO();
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Use native prepared statements for better performance
$bestModel = new BestActivity($pdo, false); // Disable auto table initialization
$dbUsers = new DatabaseUsers();

// Cache current year to avoid repeated calculations
static $current_year_cache = null;
if ($current_year_cache === null) {
    $termPee = \TermPee::getCurrent();
    $current_year_cache = $termPee->pee;
}
$current_year = $current_year_cache;

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

function jsonError($message, $extra = []) {
    echo json_encode(array_merge(['success' => false, 'message' => $message], $extra));
    exit;
}

function checkRegistrationTime($stuGrade) {
    static $settings_cache = null;
    static $cache_time = null;
    
    // Cache settings for 5 minutes
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
    $stuGrade = 'ม.'.$stu['Stu_major'];
    
    if (!in_array($stuGrade, $allowed, true)) {
        return ['valid' => false, 'message' => 'ระดับชั้น ' . $stuGrade . ' ไม่สามารถสมัครกิจกรรมนี้ได้'];
    }
    return ['valid' => true, 'grade' => $stuGrade];
}

switch ($action) {
    case 'list':
        try {
            // Use optimized method to get activities with member counts in single query
            $activities = $bestModel->getAllWithMemberCounts($current_year);
            echo json_encode(['success' => true, 'data' => $activities, 'year' => $current_year]);
        } catch (Exception $e) {
            jsonError('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . $e->getMessage());
        }
        exit;

    case 'register':
        // Student registration action
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

            // Check grade level eligibility
            $stu = $dbUsers->getStudentByUsername($student_id);
            if (!$stu) {
                jsonError('ไม่พบข้อมูลนักเรียน');
            }

            $gradeValidation = validateGradeLevel($activity, $stu);
            if (!$gradeValidation['valid']) {
                jsonError($gradeValidation['message']);
            }

            // Check Best registration time setting
            $timeValidation = checkRegistrationTime($gradeValidation['grade']);
            if (!$timeValidation['valid']) {
                jsonError($timeValidation['message']);
            }

        // Check if already registered for any Best activity this year - optimized single query
        $existing = $bestModel->getStudentRegistration($student_id, $current_year);
        
        if ($existing) {
            jsonError('คุณได้สมัครกิจกรรม "' . $existing['name'] . '" ไปแล้วในปีนี้');
        }

        // Use atomic transaction to check capacity and insert
        $pdo->beginTransaction();
        try {
            // Lock activity row to prevent race conditions
            $stmt = $pdo->prepare("SELECT max_members FROM best_activities WHERE id = :id AND year = :year FOR UPDATE");
            $stmt->execute(['id' => $activity_id, 'year' => $current_year]);
            $activityData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$activityData) {
                throw new Exception('กิจกรรมไม่พบ');
            }

            // Check current count with lock
            $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM best_members WHERE activity_id = :id AND year = :year FOR UPDATE");
            $stmt->execute(['id' => $activity_id, 'year' => $current_year]);
            $currentCount = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];

            if ($currentCount >= intval($activityData['max_members'])) {
                throw new Exception('กิจกรรมเต็มแล้ว (รับได้ ' . $activityData['max_members'] . ' คน)');
            }

            // Insert registration
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
            
            // Clear cache
            $bestModel->clearActivityCache($activity_id, $current_year);

            echo json_encode([
                'success' => true, 
                'message' => 'สมัครกิจกรรม "' . $activity['name'] . '" เรียบร้อยแล้ว'
            ]);

        } catch (Exception $e) {
            $pdo->rollBack();
            jsonError($e->getMessage());
        }        } catch (PDOException $e) {
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
        
        // Get activity and members in optimized single call
        $activity = $bestModel->getById($id);
        if (!$activity || intval($activity['year']) !== intval($current_year)) jsonError('ไม่พบกิจกรรมปีนี้');
        
        // Use optimized method to get members with student data in single query
        $members = $bestModel->listMembersWithStudentData($id, $current_year);
        
        echo json_encode(['success' => true, 'members' => $members, 'year' => $current_year]);
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
        
        $gradeValidation = validateGradeLevel($activity, $stu);
        if (!$gradeValidation['valid']) {
            jsonError($gradeValidation['message']);
        }

        // Check Best registration time setting (only for students)
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'นักเรียน') {
            $timeValidation = checkRegistrationTime($gradeValidation['grade']);
            if (!$timeValidation['valid']) {
                jsonError($timeValidation['message']);
            }
        }

        // one activity per year per student enforcement via unique key
        // but also check capacity
        $current = $bestModel->countMembers($id, $current_year);
        if ($current >= intval($activity['max_members'])) {
            jsonError('กิจกรรมเต็มแล้ว');
        }

        // Insert - use optimized method
        try {
            $ok = $bestModel->addMember($id, $student_id, $current_year);
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
        $ok = $bestModel->removeMember($id, $student_id, $current_year);
        echo json_encode(['success' => $ok]);
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
