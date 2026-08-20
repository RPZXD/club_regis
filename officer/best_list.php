<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../models/BestActivity.php';
require_once __DIR__ . '/../models/TermPee.php';
use App\DatabaseClub;
use App\Models\BestActivity;

$db = new DatabaseClub();
$pdo = $db->getPDO();
$bestModel = new BestActivity($pdo, true);

$termPee = \TermPee::getCurrent();
$current_year = intval($termPee->pee ?: (date('Y') + 543));
$available_years = $bestModel->getDistinctYears();
if (!in_array($current_year, $available_years)) {
    array_unshift($available_years, $current_year);
}
$available_years = array_values(array_unique($available_years));
rsort($available_years);

$pageTitle = 'Best For Teen';

ob_start();
include '../views/officer/best_list.php';
$content = ob_get_clean();

include '../views/layouts/officer_app.php';
?>
