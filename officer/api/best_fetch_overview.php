<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: public, max-age=60'); // Cache for 1 minute

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly, capture them

if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'data' => []]);
    exit;
}

require_once __DIR__.'/../../classes/DatabaseClub.php';
require_once __DIR__.'/../../models/BestActivity.php';
require_once __DIR__.'/../../models/TermPee.php';

use App\DatabaseClub;
use App\Models\BestActivity;

try {
    $db = new DatabaseClub();
    $pdo = $db->getPDO();
    
    // Test connection first
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    $term = \TermPee::getCurrent();
    if (!$term) {
        throw new Exception('Cannot get current term');
    }
    
    $year = (int)$term->pee;
    if (!$year || $year < 2020) {
        throw new Exception('Invalid year: ' . $year);
    }
    
    // Start transaction for better performance
    $pdo->beginTransaction();
    
    // Check if tables exist first
    $checkTables = $pdo->query("SHOW TABLES LIKE 'best_activities'")->rowCount();
    if ($checkTables === 0) {
        throw new Exception('Table best_activities does not exist');
    }
    
    $checkRegis = $pdo->query("SHOW TABLES LIKE 'best_regis'")->rowCount();
    if ($checkRegis === 0) {
        // Fallback to best_members table if best_regis doesn't exist
        $memberTable = 'best_members';
        $memberColumn = 'activity_id';
    } else {
        $memberTable = 'best_regis';
        $memberColumn = 'activity_id';
    }
    
    // Simplified query with better error handling
    $stmt = $pdo->prepare("
        SELECT 
            ba.id,
            ba.name,
            ba.max_members,
            ba.grade_levels,
            ba.year,
            COALESCE(member_counts.member_count, 0) as current_members_count,
            CASE 
                WHEN ba.max_members > 0 
                THEN ROUND(COALESCE(member_counts.member_count, 0) * 100.0 / ba.max_members)
                ELSE 0 
            END as fill_percent
        FROM best_activities ba 
        LEFT JOIN (
            SELECT 
                {$memberColumn},
                COUNT(*) as member_count
            FROM {$memberTable}
            WHERE year = :year 
            GROUP BY {$memberColumn}
        ) member_counts ON ba.id = member_counts.{$memberColumn}
        WHERE ba.year = :year2
        ORDER BY ba.id ASC
    ");
    
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->bindParam(':year2', $year, PDO::PARAM_INT);
    
    if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . implode(' ', $stmt->errorInfo()));
    }
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert numeric strings to proper types for better frontend handling
    foreach ($rows as &$row) {
        $row['id'] = (int)$row['id'];
        $row['max_members'] = (int)$row['max_members'];
        $row['current_members_count'] = (int)$row['current_members_count'];
        $row['fill_percent'] = (int)$row['fill_percent'];
        $row['year'] = (int)$row['year'];
    }
    
    $pdo->commit();
    
    // Add performance metrics
    $loadTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
    
    echo json_encode([
        'success' => true, 
        'data' => $rows, 
        'year' => $year,
        'meta' => [
            'count' => count($rows),
            'loadTime' => round($loadTime * 1000, 2) . 'ms',
            'memberTable' => $memberTable
        ]
    ], JSON_NUMERIC_CHECK);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    $errorMessage = $e->getMessage();
    $errorFile = $e->getFile();
    $errorLine = $e->getLine();
    
    error_log("Best Overview API Error: " . $errorMessage . " in " . $errorFile . " on line " . $errorLine);
    
    // More detailed error response for debugging
    echo json_encode([
        'success' => false, 
        'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล', 
        'data' => [],
        'debug' => [
            'error' => $errorMessage,
            'file' => basename($errorFile),
            'line' => $errorLine,
            'year' => isset($year) ? $year : 'unknown',
            'session_role' => $_SESSION['role'] ?? 'none',
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
}
?>
