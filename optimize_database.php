<?php
/**
 * Database Optimization Script
 * This script adds indexes and optimizes the database structure for better performance
 */

require_once __DIR__ . '/classes/DatabaseClub.php';
require_once __DIR__ . '/classes/DatabaseUsers.php';

use App\DatabaseClub;
use App\DatabaseUsers;

echo "Starting database optimization...\n";

try {
    $clubDb = new DatabaseClub();
    $pdo = $clubDb->getPDO();
    
    // Add indexes to improve query performance
    $indexes = [
        // Best Activities table indexes
        "ALTER TABLE best_activities ADD INDEX IF NOT EXISTS idx_year (year)",
        "ALTER TABLE best_activities ADD INDEX IF NOT EXISTS idx_name_year (name, year)",
        "ALTER TABLE best_activities ADD INDEX IF NOT EXISTS idx_grade_levels (grade_levels(50))",
        
        // Best Members table indexes
        "ALTER TABLE best_members ADD INDEX IF NOT EXISTS idx_student_id (student_id)",
        "ALTER TABLE best_members ADD INDEX IF NOT EXISTS idx_year (year)",
        "ALTER TABLE best_members ADD INDEX IF NOT EXISTS idx_created_at (created_at)",
        
        // Club related indexes (if not already present)
        "ALTER TABLE clubs ADD INDEX IF NOT EXISTS idx_term_year (term, year)",
        "ALTER TABLE clubs ADD INDEX IF NOT EXISTS idx_advisor (advisor_teacher)",
        
        "ALTER TABLE club_members ADD INDEX IF NOT EXISTS idx_student_id (student_id)",
        "ALTER TABLE club_members ADD INDEX IF NOT EXISTS idx_club_term_year (club_id, term, year)",
    ];
    
    foreach ($indexes as $indexSql) {
        try {
            $pdo->exec($indexSql);
            echo "✓ Added index: " . substr($indexSql, 0, 50) . "...\n";
        } catch (Exception $e) {
            // Index might already exist, which is fine
            if (strpos($e->getMessage(), 'Duplicate key name') === false) {
                echo "⚠ Warning for index: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Optimize table storage
    $optimizeTables = [
        "OPTIMIZE TABLE best_activities",
        "OPTIMIZE TABLE best_members",
        "OPTIMIZE TABLE clubs",
        "OPTIMIZE TABLE club_members"
    ];
    
    foreach ($optimizeTables as $optimizeSql) {
        try {
            $pdo->exec($optimizeSql);
            echo "✓ Optimized: " . $optimizeSql . "\n";
        } catch (Exception $e) {
            echo "⚠ Warning optimizing table: " . $e->getMessage() . "\n";
        }
    }
    
    // Analyze tables for better query planning
    $analyzeTables = [
        "ANALYZE TABLE best_activities",
        "ANALYZE TABLE best_members", 
        "ANALYZE TABLE clubs",
        "ANALYZE TABLE club_members"
    ];
    
    foreach ($analyzeTables as $analyzeSql) {
        try {
            $pdo->exec($analyzeSql);
            echo "✓ Analyzed: " . $analyzeSql . "\n";
        } catch (Exception $e) {
            echo "⚠ Warning analyzing table: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n✅ Database optimization completed successfully!\n";
    echo "📊 Performance improvements applied:\n";
    echo "   - Added optimized indexes for faster queries\n";
    echo "   - Optimized table storage\n";
    echo "   - Updated table statistics\n";
    echo "   - Enhanced query performance for Best activities\n";
    
} catch (Exception $e) {
    echo "❌ Error during optimization: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🚀 You can now enjoy improved performance!\n";
?>
