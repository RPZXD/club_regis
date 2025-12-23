<?php
// Read configuration from JSON file
$config = json_decode(file_get_contents('config.json'), true);
$global = $config['global'];

// Page title
$pageTitle = 'Best For Teen 2025';

// Start output buffering for content
ob_start();

// Include the view
include 'views/home/best_list.php';

$content = ob_get_clean();

// Include the main layout
include 'views/layouts/app.php';
?>
