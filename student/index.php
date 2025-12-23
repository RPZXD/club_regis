<?php 
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'นักเรียน') {
    header('Location: ../login.php');
    exit;
}
$user = $_SESSION['user'];

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

$term = isset($_SESSION['term']) ? $_SESSION['term'] : '-';
$pee = isset($_SESSION['pee']) ? $_SESSION['pee'] : '-';

$pageTitle = 'หน้าหลักนักเรียน';

ob_start();
include '../views/student/index.php';
$content = ob_get_clean();

include '../views/layouts/student_app.php';
?>
