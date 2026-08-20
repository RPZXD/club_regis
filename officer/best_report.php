<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../models/BestActivity.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\Models\BestActivity;

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'] ?? [];

$db = new DatabaseClub();
$pdo = $db->getPDO();
$bestModel = new BestActivity($pdo, true);

$term = \TermPee::getCurrent();
$current_year = intval($term ? $term->pee : (date('Y') + 543));

$available_years = $bestModel->getDistinctYears();
if (!in_array($current_year, $available_years)) {
    array_unshift($available_years, $current_year);
}
$available_years = array_values(array_unique($available_years));
rsort($available_years);

$pageTitle = 'รายงาน Best For Teen';

ob_start();
include '../views/officer/best_report.php';
$content = ob_get_clean();

include '../views/layouts/officer_app.php';
?>
