<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'status' => 'testing',
    'session_check' => [
        'username' => $_SESSION['username'] ?? 'not_set',
        'role' => $_SESSION['role'] ?? 'not_set',
        'logged_in' => $_SESSION['logged_in'] ?? false
    ],
    'file_checks' => [
        'DatabaseClub' => file_exists(__DIR__.'/../../classes/DatabaseClub.php'),
        'BestActivity' => file_exists(__DIR__.'/../../models/BestActivity.php'),
        'TermPee' => file_exists(__DIR__.'/../../models/TermPee.php')
    ],
    'php_version' => PHP_VERSION,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
