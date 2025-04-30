<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../view-documents.php');
    exit;
}

// ตรวจสอบสิทธิ์
if (empty($_SESSION['role']) || !in_array($_SESSION['role'], ['เจ้าหน้าที่', 'admin'])) {
    header('Location: ../view-documents.php?error=ไม่มีสิทธิ์แก้ไข');
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
$title = trim($_POST['title'] ?? '');
$doc_num = trim($_POST['doc_num'] ?? '');
$detail = trim($_POST['detail'] ?? '');

if ($id <= 0 || $title === '' || $doc_num === '') {
    header('Location: ../view-documents.php?error=ข้อมูลไม่ครบถ้วน');
    exit;
}

try {
    $db = new \App\DatabaseDocuments();
    $sql = "UPDATE uploads SET title = ?, doc_num = ?, detail = ? WHERE id = ?";
    $params = [$title, $doc_num, $detail, $id];
    $db->execute($sql, $params);
    header('Location: ../view-documents.php?success=' . urlencode('แก้ไขข้อมูลสำเร็จ'));
    exit;
} catch (Exception $e) {
    header('Location: ../view-documents.php?error=' . urlencode('เกิดข้อผิดพลาด: ' . $e->getMessage()));
    exit;
}
