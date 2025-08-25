<?php
// Debug script to check level counting logic
session_start();
$_SESSION['username'] = 'test';
$_SESSION['role'] = 'เจ้าหน้าที่';

require_once __DIR__.'/../../classes/DatabaseClub.php';
require_once __DIR__.'/../../models/TermPee.php';

use App\DatabaseClub;

try {
    $club = new DatabaseClub();
    $pdo = $club->getPDO();
    $term = \TermPee::getCurrent();
    $year = (int)$term->pee;
    
    echo "=== Debug Level Counting ===\n";
    echo "Current year: $year\n\n";
    
    // Check which table exists
    $checkBestRegis = $pdo->query("SHOW TABLES LIKE 'best_regis'")->rowCount();
    $checkBestMembers = $pdo->query("SHOW TABLES LIKE 'best_members'")->rowCount();
    
    echo "Tables found:\n";
    echo "- best_regis: " . ($checkBestRegis > 0 ? 'YES' : 'NO') . "\n";
    echo "- best_members: " . ($checkBestMembers > 0 ? 'YES' : 'NO') . "\n\n";
    
    // Use appropriate table
    if ($checkBestMembers > 0) {
        $tableName = 'best_members';
        echo "Using table: $tableName\n\n";
        
        // Show all student_ids in the table
        $stmt = $pdo->prepare("SELECT student_id, activity_id FROM $tableName WHERE year = ? ORDER BY student_id");
        $stmt->execute([$year]);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "All registration records:\n";
        foreach ($records as $record) {
            $firstDigit = substr($record['student_id'], 0, 1);
            echo "- student_id: {$record['student_id']}, activity_id: {$record['activity_id']}, first_digit: $firstDigit\n";
        }
        echo "\n";
        
        // Count by level
        echo "Count by level (based on first digit):\n";
        for ($level = 1; $level <= 6; $level++) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count 
                FROM $tableName 
                WHERE year = ? 
                AND SUBSTRING(student_id, 1, 1) = ?
            ");
            $stmt->execute([$year, (string)$level]);
            $count = $stmt->fetchColumn();
            echo "- Level $level: $count records\n";
        }
        echo "\n";
        
        // Show activities
        $stmt = $pdo->prepare("SELECT id, name FROM best_activities WHERE year = ?");
        $stmt->execute([$year]);
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Activities for year $year:\n";
        foreach ($activities as $activity) {
            echo "- ID: {$activity['id']}, Name: {$activity['name']}\n";
            
            // Count registrations by level for this activity
            for ($level = 1; $level <= 6; $level++) {
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) as count 
                    FROM $tableName 
                    WHERE activity_id = ? 
                    AND year = ? 
                    AND SUBSTRING(student_id, 1, 1) = ?
                ");
                $stmt->execute([$activity['id'], $year, (string)$level]);
                $count = $stmt->fetchColumn();
                if ($count > 0) {
                    echo "  * Level $level: $count registrations\n";
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
