<?php 
// Read configuration from JSON file
$config = json_decode(file_get_contents('config.json'), true);
$global = $config['global'];

// Page title
$pageTitle = 'รายชื่อชุมนุม';

// Start output buffering for content
ob_start();

// Include the view
include 'views/home/club_list.php';

$content = ob_get_clean();

// Include the main layout
include 'views/layouts/app.php';
?>
