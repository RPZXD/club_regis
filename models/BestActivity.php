<?php
namespace App\Models;

use PDO;
use PDOException;

class BestActivity
{
    protected $pdo;
    protected $cache = [];
    protected $cacheExpiry = [];
    protected $cacheTimeout = 300; // 5 minutes
    protected $autoInit = false;
    protected $preparedStatements = []; // Cache prepared statements

    public function __construct(PDO $pdo, $autoInitTables = false)
    {
        $this->pdo = $pdo;
        $this->autoInit = $autoInitTables;
        
        // Optimize connection settings
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Try to enable query cache only if supported (MySQL 5.7 and below)
        try {
            $this->pdo->exec("SET SESSION query_cache_type = ON");
        } catch (PDOException $e) {
            // Query cache not supported in MySQL 8.0+, ignore silently
            // Modern MySQL uses better internal optimizations
        }
        
        // Only initialize tables when explicitly requested
        if ($this->autoInit) {
            $this->initTables();
        }
    }

    // Simplified prepared statement method (disable caching to avoid parameter conflicts)
    protected function getPreparedStatement($key, $sql) {
        // Temporarily disable caching to avoid parameter number conflicts
        return $this->pdo->prepare($sql);
    }

    // Enhanced caching system
    protected function getCacheKey($key, $params = []) {
        return md5($key . serialize($params));
    }
    
    protected function setCache($key, $data) {
        $cacheKey = is_array($key) ? $this->getCacheKey($key[0], $key[1]) : $key;
        $this->cache[$cacheKey] = $data;
        $this->cacheExpiry[$cacheKey] = time() + $this->cacheTimeout;
    }
    
    protected function getCache($key) {
        $cacheKey = is_array($key) ? $this->getCacheKey($key[0], $key[1]) : $key;
        if (isset($this->cache[$cacheKey]) && time() < $this->cacheExpiry[$cacheKey]) {
            return $this->cache[$cacheKey];
        }
        return null;
    }
    
    protected function clearCache($pattern = null) {
        if ($pattern === null) {
            $this->cache = [];
            $this->cacheExpiry = [];
        } else {
            foreach ($this->cache as $key => $value) {
                if (strpos($key, $pattern) !== false) {
                    unset($this->cache[$key]);
                    unset($this->cacheExpiry[$key]);
                }
            }
        }
    }

    // Clear specific cache for activity
    public function clearActivityCache($activity_id, $year) {
        $this->clearCache('activities_with_counts');
        $this->clearCache('member_count_' . $activity_id . '_' . $year);
        $this->clearCache('members_list_' . $activity_id . '_' . $year);
        $this->clearCache('activities_all_' . $year);
    }

    // Ensure required tables exist with optimized indexes
    public function initTables()
    {
        try {
            // Optimized table structure with better indexes
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS best_activities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                grade_levels VARCHAR(255) NOT NULL,
                max_members INT NOT NULL DEFAULT 0,
                year INT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_year_name (year, name),
                INDEX idx_year_id (year, id),
                FULLTEXT idx_name_desc (name, description)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $this->pdo->exec("CREATE TABLE IF NOT EXISTS best_members (
                id INT AUTO_INCREMENT PRIMARY KEY,
                activity_id INT NOT NULL,
                student_id VARCHAR(50) NOT NULL,
                year INT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_student_year (student_id, year),
                INDEX idx_activity_year_created (activity_id, year, created_at),
                INDEX idx_student_year (student_id, year),
                INDEX idx_year_activity (year, activity_id),
                CONSTRAINT fk_best_members_activity FOREIGN KEY (activity_id) REFERENCES best_activities(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            
            // Add query optimization settings
            $this->pdo->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
            
        } catch (PDOException $e) {
            // Ignore table creation errors in runtime
        }
    }

    // Optimized getAll with better caching
    public function getAll($year)
    {
        $cacheKey = 'activities_all_' . $year;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $stmt = $this->getPreparedStatement('getAll', "SELECT * FROM best_activities WHERE year = :year ORDER BY id ASC");
        $stmt->execute(['year' => $year]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->setCache($cacheKey, $result);
        return $result;
    }

    // Highly optimized method to get activities with member counts in single query
    public function getAllWithMemberCounts($year)
    {
        $cacheKey = 'activities_with_counts_' . $year;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $sql = "SELECT ba.id, ba.name, ba.description, ba.grade_levels, ba.max_members, ba.year,
                       ba.created_at, ba.updated_at,
                       COALESCE(mc.current_members_count, 0) as current_members_count
                FROM best_activities ba
                LEFT JOIN (
                    SELECT activity_id, COUNT(*) as current_members_count 
                    FROM best_members 
                    WHERE year = :year1 
                    GROUP BY activity_id
                ) mc ON ba.id = mc.activity_id
                WHERE ba.year = :year2
                ORDER BY ba.id ASC";
        
        $stmt = $this->getPreparedStatement('getAllWithMemberCounts', $sql);
        $stmt->execute(['year1' => $year, 'year2' => $year]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->setCache($cacheKey, $result);
        return $result;
    }

    // Optimized getById with prepared statement caching
    public function getById($id)
    {
        $cacheKey = 'activity_by_id_' . $id;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $stmt = $this->getPreparedStatement('getById', "SELECT * FROM best_activities WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $result = $row ?: null;
        
        $this->setCache($cacheKey, $result);
        return $result;
    }

    public function create($data)
    {
        $sql = "INSERT INTO best_activities (name, description, grade_levels, max_members, year, created_at, updated_at)
                VALUES (:name, :description, :grade_levels, :max_members, :year, NOW(), NOW())";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'grade_levels' => $data['grade_levels'],
            'max_members' => intval($data['max_members']),
            'year' => intval($data['year'])
        ]);
        
        if ($result) {
            $this->clearCache('activities_');
        }
        
        return $result;
    }

    public function update($id, $data)
    {
        $sql = "UPDATE best_activities 
                SET name = :name, description = :description, grade_levels = :grade_levels, max_members = :max_members, updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'grade_levels' => $data['grade_levels'],
            'max_members' => intval($data['max_members'])
        ]);
        
        if ($result) {
            $this->clearCache('activities_');
            $this->clearCache(['activity_by_id', ['id' => $id]]);
        }
        
        return $result;
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM best_activities WHERE id = :id");
        $result = $stmt->execute(['id' => $id]);
        
        if ($result) {
            $this->clearCache('activities_');
            $this->clearCache(['activity_by_id', ['id' => $id]]);
        }
        
        return $result;
    }

    // Optimized countMembers with better caching
    public function countMembers($activity_id, $year)
    {
        $cacheKey = 'member_count_' . $activity_id . '_' . $year;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $stmt = $this->getPreparedStatement('countMembers', "SELECT COUNT(*) AS cnt FROM best_members WHERE activity_id = :activity_id AND year = :year");
        $stmt->execute(['activity_id' => $activity_id, 'year' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $result = $row ? intval($row['cnt']) : 0;
        
        $this->setCache($cacheKey, $result);
        return $result;
    }

    // Get members with student data using separate queries (fallback method)
    public function listMembersWithStudentData($activity_id, $year)
    {
        $cacheKey = 'members_with_student_' . $activity_id . '_' . $year;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            // First get the members
            $members = $this->listMembers($activity_id, $year);
            
            // Then get student data using existing DatabaseUsers class
            require_once __DIR__ . '/../classes/DatabaseUsers.php';
            $dbUsers = new \App\DatabaseUsers();
            
            $result = [];
            foreach ($members as $m) {
                try {
                    $stu = $dbUsers->getStudentByUsername($m['student_id']);
                    $result[] = [
                        'student_id' => $m['student_id'],
                        'name' => $stu ? ($stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur']) : $m['student_id'],
                        'class_name' => $stu ? ('ม.'.$stu['Stu_major'].'/'.$stu['Stu_room']) : '',
                        'created_at' => $m['created_at']
                    ];
                } catch (Exception $e) {
                    // If can't get student data, just use student ID
                    $result[] = [
                        'student_id' => $m['student_id'],
                        'name' => $m['student_id'],
                        'class_name' => '',
                        'created_at' => $m['created_at']
                    ];
                }
            }
            
            $this->setCache($cacheKey, $result);
            return $result;
            
        } catch (Exception $e) {
            // Fallback: return basic member data without student names
            $members = $this->listMembers($activity_id, $year);
            $result = array_map(function($m) {
                return [
                    'student_id' => $m['student_id'],
                    'name' => $m['student_id'],
                    'class_name' => '',
                    'created_at' => $m['created_at']
                ];
            }, $members);
            
            return $result;
        }
    }

    // Keep legacy method for backward compatibility
    public function listMembers($activity_id, $year)
    {
        $cacheKey = 'members_list_' . $activity_id . '_' . $year;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $stmt = $this->getPreparedStatement('listMembers', "SELECT * FROM best_members WHERE activity_id = :activity_id AND year = :year ORDER BY created_at ASC");
        $stmt->execute(['activity_id' => $activity_id, 'year' => $year]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->setCache($cacheKey, $result);
        return $result;
    }

    // Batch operations for better performance
    public function getMemberCountsForActivities($activity_ids, $year)
    {
        if (empty($activity_ids)) {
            return [];
        }

        $placeholders = str_repeat('?,', count($activity_ids) - 1) . '?';
        $sql = "SELECT activity_id, COUNT(*) as count 
                FROM best_members 
                WHERE activity_id IN ($placeholders) AND year = ? 
                GROUP BY activity_id";
        
        $params = array_merge($activity_ids, [$year]);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        $counts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $counts[$row['activity_id']] = intval($row['count']);
        }
        
        // Fill missing activity IDs with 0
        foreach ($activity_ids as $id) {
            if (!isset($counts[$id])) {
                $counts[$id] = 0;
            }
        }
        
        return $counts;
    }

    // Add member with optimized cache clearing
    public function addMember($activity_id, $student_id, $year)
    {
        $stmt = $this->pdo->prepare("INSERT INTO best_members (activity_id, student_id, year, created_at) VALUES (:activity_id, :student_id, :year, NOW())");
        $result = $stmt->execute([
            'activity_id' => $activity_id,
            'student_id' => $student_id,
            'year' => $year
        ]);
        
        if ($result) {
            $this->clearCache('member_count');
            $this->clearCache('members_list');
            $this->clearCache('activities_with_counts');
        }
        
        return $result;
    }

    // Remove member with optimized cache clearing
    public function removeMember($activity_id, $student_id, $year)
    {
        $stmt = $this->pdo->prepare("DELETE FROM best_members WHERE activity_id = :activity_id AND student_id = :student_id AND year = :year");
        $result = $stmt->execute([
            'activity_id' => $activity_id,
            'student_id' => $student_id,
            'year' => $year
        ]);
        
        if ($result) {
            $this->clearCache('member_count');
            $this->clearCache('members_list');
            $this->clearCache('activities_with_counts');
        }
        
        return $result;
    }

    // Optimized getStudentRegistration with caching
    public function getStudentRegistration($student_id, $year)
    {
        $cacheKey = 'student_registration_' . $student_id . '_' . $year;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $stmt = $this->getPreparedStatement('getStudentRegistration', 
            "SELECT ba.name, ba.id, bm.created_at 
             FROM best_members bm 
             JOIN best_activities ba ON ba.id = bm.activity_id 
             WHERE bm.student_id = :student_id AND bm.year = :year");
        $stmt->execute(['student_id' => $student_id, 'year' => $year]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->setCache($cacheKey, $result);
        return $result;
    }
}
