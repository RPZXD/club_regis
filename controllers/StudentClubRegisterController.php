<?php
require_once __DIR__ . '/../config/Database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Student_login']) || !isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit();
}

$action = $_GET['action'] ?? '';
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['user'];
    $club_id = $_POST['club_id'] ?? '';

    if (!$club_id) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสชมรม']);
        exit();
    }

    // เชื่อมต่อฐานข้อมูล
    $db = (new Database('phichaia_club'))->getConnection();

    // ดึง term/pee ปัจจุบัน
    require_once __DIR__ . '/../models/TermPee.php';
    $termPeeModel = new TermPeeModel();
    $termpee = $termPeeModel->getTermPee();
    $term = $termpee['term'];
    $pee = $termpee['pee'];

    // ตรวจสอบว่านักเรียนสมัครชมรมใน term/pee นี้ไปแล้วหรือยัง
    $stmt = $db->prepare(
        "SELECT COUNT(*) 
         FROM club_members m
         INNER JOIN clubs c ON m.club_id = c.club_id
         WHERE m.student_id = :student_id AND c.term = :term AND c.year = :pee"
    );
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':term', $term);
    $stmt->bindParam(':pee', $pee);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'คุณได้สมัครชมรมในเทอมนี้แล้ว ไม่สามารถสมัครซ้ำได้']);
        exit();
    }

    // ตรวจสอบว่านักเรียนสมัครชมรมนี้ไปแล้วหรือยัง (สำรอง เผื่อกรณีข้อมูลผิดปกติ)
    $stmt = $db->prepare("SELECT COUNT(*) FROM club_members WHERE club_id = :club_id AND student_id = :student_id");
    $stmt->bindParam(':club_id', $club_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'คุณได้สมัครชมรมนี้แล้ว']);
        exit();
    }

    // ตรวจสอบจำนวนสมาชิกปัจจุบัน
    $stmt = $db->prepare("SELECT max_members FROM clubs WHERE club_id = :club_id");
    $stmt->bindParam(':club_id', $club_id);
    $stmt->execute();
    $max_members = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM club_members WHERE club_id = :club_id");
    $stmt->bindParam(':club_id', $club_id);
    $stmt->execute();
    $current_members = $stmt->fetchColumn();

    if ($current_members >= $max_members) {
        echo json_encode(['success' => false, 'message' => 'ชมรมนี้เต็มแล้ว']);
        exit();
    }

    // สมัคร (insert) พร้อม term และ year
    $stmt = $db->prepare("INSERT INTO club_members (club_id, student_id, term, year) VALUES (:club_id, :student_id, :term, :year)");
    $stmt->bindParam(':club_id', $club_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':term', $term);
    $stmt->bindParam(':year', $pee);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'สมัครเข้าชุมนุมสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถสมัครเข้าชุมนุมได้']);
    }
    exit();
}
?>
