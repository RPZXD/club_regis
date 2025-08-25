<?php
// Test file for room students API
session_start();
header('Content-Type: application/json');

// Set up session for testing
$_SESSION['username'] = 'test';
$_SESSION['role'] = 'เจ้าหน้าที่';

echo "Testing Room Students API:\n";

// Test 1: Get all rooms
echo "\n1. Testing all rooms API:\n";
ob_start();
include 'best_fetch_all_rooms.php';
$allRoomsResult = ob_get_clean();
echo "All Rooms API Result: " . $allRoomsResult . "\n";

// Test 2: Get room students with level 2
echo "\n2. Testing room students API (level 2):\n";
$_GET['level'] = 2;
$_GET['room'] = '';
ob_start();
include 'best_fetch_room_students.php';
$roomStudentsResult = ob_get_clean();
echo "Room Students API Result: " . $roomStudentsResult . "\n";

// Test 3: Get room students with specific room
echo "\n3. Testing room students API (level 2, room 64):\n";
$_GET['level'] = 2;
$_GET['room'] = '64';
ob_start();
include 'best_fetch_room_students.php';
$roomStudentsSpecificResult = ob_get_clean();
echo "Room Students Specific API Result: " . $roomStudentsSpecificResult . "\n";
?>
