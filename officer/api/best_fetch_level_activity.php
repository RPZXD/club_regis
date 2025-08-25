<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: public, max-age=120'); // Cache for 2 minutes

if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['ok'=>false,'data'=>[],'message'=>'Unauthorized']);
    exit;
}

require_once __DIR__.'/../../classes/DatabaseClub.php';
require_once __DIR__.'/../../classes/DatabaseUsers.php';
require_once __DIR__.'/../../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;

$startTime = microtime(true);

try {
    $club = new DatabaseClub();
    $pdo = $club->getPDO();
    $users = new DatabaseUsers();
    $term = \TermPee::getCurrent();
    $year = (int)$term->pee;
    $level = isset($_GET['level']) ? (int)$_GET['level'] : 1; // 1..6

    // Validate level input
    if ($level < 1 || $level > 6) {
        throw new InvalidArgumentException('Invalid level specified');
    }

    $pdo->beginTransaction();

    // Optimized query to get activity counts by level using best_regis table
    $stmt = $pdo->prepare("
        SELECT 
            ba.id,
            ba.name,
            COUNT(br.student_id) as count
        FROM best_activities ba
        LEFT JOIN best_regis br ON ba.id = br.activity_id AND br.year = :year
        WHERE ba.year = :year2
        AND (br.student_id IS NULL OR SUBSTRING(br.student_id, 1, 1) = :level_str)
        GROUP BY ba.id, ba.name
        ORDER BY count DESC, ba.name ASC
    ");
    
    $levelStr = (string)$level;
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->bindParam(':year2', $year, PDO::PARAM_INT);  
    $stmt->bindParam(':level_str', $levelStr, PDO::PARAM_STR);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert to proper format
    $out = [];
    foreach ($results as $row) {
        $out[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'count' => (int)$row['count']
        ];
    }

    $pdo->commit();
    
    $loadTime = (microtime(true) - $startTime) * 1000;
    
    echo json_encode([
        'ok' => true,
        'data' => $out,
        'meta' => [
            'level' => $level,
            'year' => $year,
            'count' => count($out),
            'loadTime' => round($loadTime, 2) . 'ms'
        ]
    ], JSON_NUMERIC_CHECK);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    error_log("Best Level Activity API Error: " . $e->getMessage());
    
    echo json_encode([
        'ok' => false,
        'data' => [],
        'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล',
        'debug' => ($_ENV['APP_DEBUG'] ?? false) ? $e->getMessage() : null
    ]);
}
?>
