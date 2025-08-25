<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: public, max-age=60'); // Cache for 1 minute

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
    $best = new BestActivity($pdo);
    $term = \TermPee::getCurrent();
    $year = (int)$term->pee;

    // Start transaction for better performance
    $pdo->beginTransaction();
    
    // Get all activities with member counts in a single optimized query
    $stmt = $pdo->prepare("
        SELECT 
            ba.*,
            COALESCE(member_counts.member_count, 0) as current_members_count,
            CASE 
                WHEN ba.max_members > 0 
                THEN ROUND(COALESCE(member_counts.member_count, 0) * 100.0 / ba.max_members)
                ELSE 0 
            END as fill_percent
        FROM best_activities ba 
        LEFT JOIN (
            SELECT 
                activity_id,
                COUNT(*) as member_count
            FROM best_regis 
            WHERE year = :year 
            GROUP BY activity_id
        ) member_counts ON ba.id = member_counts.activity_id
        WHERE ba.year = :year2
        ORDER BY ba.id ASC
    ");
    
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->bindParam(':year2', $year, PDO::PARAM_INT);
    $stmt->execute();
    
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
            'loadTime' => round($loadTime * 1000, 2) . 'ms'
        ]
    ], JSON_NUMERIC_CHECK);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    error_log("Best Overview API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล', 
        'data' => [],
        'debug' => $_ENV['APP_DEBUG'] ?? false ? $e->getMessage() : null
    ]);
}
?>
