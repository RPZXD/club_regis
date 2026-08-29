<?php
// Read configuration from JSON file
require_once __DIR__ . '/classes/DatabaseClub.php';
require_once __DIR__ . '/models/BestActivity.php';
require_once __DIR__ . '/models/TermPee.php';

use App\DatabaseClub;
use App\Models\BestActivity;

$config = json_decode(file_get_contents('config.json'), true);
$global = $config['global'] ?? [];

$db = new DatabaseClub();
$pdo = $db->getPDO();
$bestModel = new BestActivity($pdo, false);

$termPee = \TermPee::getCurrent();
$current_year = intval($termPee ? $termPee->pee : (date('Y') + 543));

$available_years = $bestModel->getDistinctYears();
if (!in_array($current_year, $available_years)) {
    array_unshift($available_years, $current_year);
}
$available_years = array_values(array_unique($available_years));
rsort($available_years);

// Page title
$pageTitle = 'Best For Teen';

// Start output buffering for content
ob_start();

// Include the view
include 'views/home/best_list.php';

$content = ob_get_clean();

// Include the main layout
include 'views/layouts/app.php';
?>
