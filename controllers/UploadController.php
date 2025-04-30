<?php
// ลบ debug error reporting ออก
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../upload-documents.php');
    exit;
}

// ตรวจสอบ CSRF Token
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    header('Location: ../upload-documents.php?error=CSRF+token+ไม่ถูกต้อง');
    exit;
}

require_once __DIR__ . '/../classes/DatabaseDocuments.php';

// ตรวจสอบค่าที่จำเป็น
if (
    empty($_POST['title']) ||
    empty($_POST['doc_num']) ||
    empty($_POST['group_type']) ||
    empty($_POST['pee']) ||
    !isset($_FILES['file'])
) {
    header('Location: ../upload-documents.php?error=' . urlencode('กรุณากรอกข้อมูลให้ครบถ้วน'));
    exit;
}

$title = trim($_POST['title'] ?? '');
$doc_num = trim($_POST['doc_num'] ?? '');
$group_type = intval($_POST['group_type'] ?? 0);
$pee = intval($_POST['pee'] ?? 0);
$detail = trim($_POST['detail'] ?? '');
$user = $_SESSION['user'];
$upload_by = $user['Teach_id'];
$date_upload = date('Y-m-d H:i:s');

// ตรวจสอบไฟล์
if (!empty($_FILES['file']['name'])) {
    $file = $_FILES['file'];
    // ตรวจสอบข้อผิดพลาดของไฟล์
    if ($file['error'] !== UPLOAD_ERR_OK) {
        header('Location: ../upload-documents.php?error=' . urlencode('เกิดข้อผิดพลาดในการอัปโหลดไฟล์: รหัส ' . $file['error']));
        exit;
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $datePart = date('Ymd');
    $randPart = substr(bin2hex(random_bytes(3)), 0, 6);
    $fileName = "{$datePart}-{$group_type}-{$randPart}." . $ext;
    $targetDir = dirname(__DIR__) . '/uploads/files/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // insert DB
        $db = new \App\DatabaseDocuments();
        $sql = "INSERT INTO uploads (file_name, title, group_type, detail, doc_num, pee, upload_by, date_upload)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$fileName, $title, $group_type, $detail, $doc_num, $pee, $upload_by, $date_upload];
        try {
            $db->execute($sql, $params);
            header('Location: ../upload-documents.php?success=' . urlencode('อัปโหลดเอกสารสำเร็จ'));
            exit;
        } catch (Exception $e) {
            header('Location: ../upload-documents.php?error=' . urlencode('เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()));
            exit;
        }
    } else {
        header('Location: ../upload-documents.php?error=' . urlencode('อัปโหลดไฟล์ไม่สำเร็จ'));
        exit;
    }
} else {
    header('Location: ../upload-documents.php?error=' . urlencode('กรุณาเลือกไฟล์'));
    exit;
}
