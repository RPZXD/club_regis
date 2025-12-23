<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

// Initialize tables for admin interface
require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../models/BestActivity.php';
use App\DatabaseClub;
use App\Models\BestActivity;

$db = new DatabaseClub();
$pdo = $db->getPDO();
$bestModel = new BestActivity($pdo, true);

$pageTitle = 'Best For Teen';

ob_start();
include '../views/officer/best_list.php';
$content = ob_get_clean();

include '../views/layouts/officer_app.php';
?>
