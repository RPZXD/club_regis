<?php
require_once('../models/ClubMember.php');
require_once('../models/Club.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'list') {
        $club_id = $_GET['club_id'] ?? '';
        if ($club_id) {
            $members = ClubMember::getMembersByClub($club_id);
            header('Content-Type: application/json');
            echo json_encode($members);
            exit();
        }
        echo json_encode([]);
        exit();
    }
    if (isset($_GET['action']) && $_GET['action'] === 'clubs') {
        // ดึง club ที่ครูเป็นที่ปรึกษา
        $advisor = $_SESSION['user'] ?? '';
        $clubs = [];
        if ($advisor) {
            $clubModel = new ClubModel();
            $clubs = $clubModel->getClubsByAdvisor($advisor);
        }
        header('Content-Type: application/json');
        echo json_encode($clubs);
        exit();
    }
    if (isset($_GET['action']) && $_GET['action'] === 'clubinfo') {
        // สำหรับข้อมูลหัวกระดาษตอนพิมพ์
        $club_id = $_GET['club_id'] ?? '';
        if ($club_id) {
            $clubModel = new ClubModel();
            $club = $clubModel->getClubDetail($club_id);
            header('Content-Type: application/json');
            echo json_encode($club ?: []);
            exit();
        }
        echo json_encode([]);
        exit();
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $member_id = $_POST['member_id'] ?? '';
    if ($member_id) {
        $success = ClubMember::deleteMember($member_id);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'ลบสำเร็จ' : 'ไม่สามารถลบได้'
        ]);
        exit();
    }
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit();
}
