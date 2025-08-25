<?php
// Temporary test file for API debugging
session_start();
header('Content-Type: application/json');

// Set up session for testing
$_SESSION['username'] = 'test';
$_SESSION['role'] = 'เจ้าหน้าที่';

echo "Testing Level Report API:\n";
ob_start();
include 'best_fetch_level_report.php';
$levelResult = ob_get_clean();
echo "Level API Result: " . $levelResult . "\n\n";

echo "Testing Room Report API:\n";
ob_start();
include 'best_fetch_room_report.php';
$roomResult = ob_get_clean();
echo "Room API Result: " . $roomResult . "\n\n";

echo "Testing Level Activity API:\n";
$_GET['level'] = 1;
ob_start();
include 'best_fetch_level_activity.php';
$levelActivityResult = ob_get_clean();
echo "Level Activity API Result: " . $levelActivityResult . "\n\n";
?>
