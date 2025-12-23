<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

$pageTitle = 'รายงานชุมนุม';

ob_start();
include '../views/officer/club_report.php';
$content = ob_get_clean();

include '../views/layouts/officer_app.php';
?>
