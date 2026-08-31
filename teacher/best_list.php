<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'ครู') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/BestActivity.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\BestActivity;

$dbClub = new DatabaseClub();
$pdoClub = $dbClub->getPDO();
$bestActivityModel = new BestActivity($pdoClub, false);

$dbUsers = new DatabaseUsers();
$teacherUsername = $_SESSION['username'] ?? '';
$teacherInfo = $dbUsers->getTeacherByUsername($teacherUsername);

$teach_class_raw = $teacherInfo['Teach_class'] ?? ($_SESSION['user']['Teach_class'] ?? '');
$teach_room_raw = $teacherInfo['Teach_room'] ?? ($_SESSION['user']['Teach_room'] ?? '');

// Parse teacher assigned classroom
$assigned_level = 1;
if ($teach_class_raw) {
    if (preg_match('/(\d+)/', $teach_class_raw, $m)) {
        $assigned_level = (int)$m[1];
        if ($assigned_level < 1 || $assigned_level > 6) $assigned_level = 1;
    }
}
$assigned_room = !empty($teach_room_raw) ? trim($teach_room_raw) : '1';
$has_assigned_room = (!empty($teach_class_raw) && !empty($teach_room_raw));

$termPee = TermPee::getCurrent();
$current_year = (int)($termPee ? $termPee->pee : (date('Y') + 543));

$available_years = $bestActivityModel->getDistinctYears();
if (empty($available_years)) {
    $available_years = [$current_year];
}
if (!in_array($current_year, $available_years)) {
    array_unshift($available_years, $current_year);
}
$available_years = array_values(array_unique(array_map('intval', $available_years)));
rsort($available_years);

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

$pageTitle = 'Best For Teen (นักเรียนห้องประจำชั้น)';

ob_start();
include '../views/teacher/best_list.php';
$content = ob_get_clean();

include '../views/layouts/teacher_app.php';
?>
