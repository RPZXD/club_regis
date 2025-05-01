<?php
require_once('../classes/DatabaseClub.php');
require_once('../models/RegisClub.php');
require_once('../models/TermPee.php');

use App\DatabaseClub;
use App\Models\RegisClub;

header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'นักเรียน') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = $_SESSION['user'];
$student_id = $user['Stu_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $club_id = $_POST['club_id'] ?? null;

    // ใช้ TermPee หากไม่ได้ส่ง year/term มา
    if (isset($_POST['year']) && isset($_POST['term'])) {
        $year = $_POST['year'];
        $term = $_POST['term'];
    } else {
        $termPee = TermPee::getCurrent();
        $year = $termPee->pee;
        $term = $termPee->term;
    }

    if (!$student_id || !$club_id) {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit;
    }

    $db = new DatabaseClub();
    $pdo = $db->getPDO();
    $regisClub = new RegisClub($pdo);

    // ตรวจสอบว่าสมัครไปแล้วหรือยัง
    if ($regisClub->hasRegistered($student_id, $year, $term)) {
        echo json_encode(['success' => false, 'message' => 'คุณได้สมัครชุมนุมไปแล้วในปีนี้']);
        exit;
    }

    // สมัคร
    if ($regisClub->register($student_id, $club_id, $year, $term)) {
        echo json_encode(['success' => true, 'message' => 'สมัครสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการสมัคร']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
