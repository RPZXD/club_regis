<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'ครู') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../models/BestActivity.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\Models\BestActivity;

$dbClub = new DatabaseClub();
$pdoClub = $dbClub->getPDO();
$bestActivityModel = new BestActivity($pdoClub, false);

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
