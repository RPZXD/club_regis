<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'ครู') {
    header('Location: ../login.php');
    exit;
}

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

$pageTitle = 'จัดการสมาชิกชุมนุม';

ob_start();

// Include the view
include '../views/teacher/club_members.php';

$content = ob_get_clean();

// Include the layout
include '../views/layouts/teacher_app.php';
?>
