<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../view-documents.php');
    exit;
}

// ตรวจสอบสิทธิ์
if (empty($_SESSION['role']) || !in_array($_SESSION['role'], ['เจ้าหน้าที่', 'admin'])) {
    header('Location: ../view-documents.php?error=ไม่มีสิทธิ์ลบ');
    exit;
}

// ตรวจสอบ CSRF Token
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    header('Location: ../view-documents.php?error=CSRF+token+ไม่ถูกต้อง');
    exit;
}

require_once __DIR__ . '/../classes/DatabaseDocuments.php';

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: ../view-documents.php?error=ข้อมูลไม่ครบถ้วน');
    exit;
}

try {
    $db = new \App\DatabaseDocuments();
    // ดึงชื่อไฟล์ก่อนลบ (ถ้าต้องการลบไฟล์จริง)
    $sql = "SELECT file_name FROM uploads WHERE id = ?";
    $file = $db->fetch($sql, [$id]);
    // ลบ record
    $sql = "DELETE FROM uploads WHERE id = ?";
    $db->execute($sql, [$id]);
    // ลบไฟล์จริง (ถ้ามี)
    if ($file && !empty($file['file_name'])) {
        $filePath = realpath(__DIR__ . '/../uploads/files/' . $file['file_name']);
        if ($filePath && strpos($filePath, realpath(__DIR__ . '/../uploads/files/')) === 0 && file_exists($filePath)) {
            @unlink($filePath);
        }
    }
    header('Location: ../view-documents.php?success=' . urlencode('ลบข้อมูลสำเร็จ'));
    exit;
} catch (Exception $e) {
    header('Location: ../view-documents.php?error=' . urlencode('เกิดข้อผิดพลาด: ' . $e->getMessage()));
    exit;
}
