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

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->initTables();
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

    // Ensure required tables exist
    private function initTables()
    {
        try {
            // Add optimized indexes for better performance
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS best_activities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                grade_levels VARCHAR(255) NOT NULL,
                max_members INT NOT NULL DEFAULT 0,
                year INT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_year (year),
                INDEX idx_name_year (name, year)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $this->pdo->exec("CREATE TABLE IF NOT EXISTS best_members (
                id INT AUTO_INCREMENT PRIMARY KEY,
                activity_id INT NOT NULL,
                student_id VARCHAR(50) NOT NULL,
                year INT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_student_year (student_id, year),
                INDEX idx_activity_year (activity_id, year),
                INDEX idx_student_id (student_id),
                INDEX idx_year (year),
                CONSTRAINT fk_best_members_activity FOREIGN KEY (activity_id) REFERENCES best_activities(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            // Ignore table creation errors in runtime; deployment may create tables via migrations
        }
    }

    public function getAll($year)
    {
        $cacheKey = ['activities_all', ['year' => $year]];
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM best_activities WHERE year = :year ORDER BY id ASC");
        $stmt->execute(['year' => $year]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->setCache($cacheKey, $result);
        return $result;
    }

    // Optimized method to get activities with member counts in single query
    public function getAllWithMemberCounts($year)
    {
        $cacheKey = ['activities_with_counts', ['year' => $year]];
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $sql = "SELECT ba.*, 
                       COALESCE(member_counts.current_members_count, 0) as current_members_count
                FROM best_activities ba
                LEFT JOIN (
                    SELECT activity_id, COUNT(*) as current_members_count 
                    FROM best_members 
                    WHERE year = :year 
                    GROUP BY activity_id
                ) member_counts ON ba.id = member_counts.activity_id
                WHERE ba.year = :year
                ORDER BY ba.id ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['year' => $year]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->setCache($cacheKey, $result);
        return $result;
    }

    public function getById($id)
    {
        $cacheKey = ['activity_by_id', ['id' => $id]];
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM best_activities WHERE id = :id");
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

    public function countMembers($activity_id, $year)
    {
        $cacheKey = ['member_count', ['activity_id' => $activity_id, 'year' => $year]];
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS cnt FROM best_members WHERE activity_id = :activity_id AND year = :year");
        $stmt->execute(['activity_id' => $activity_id, 'year' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $result = $row ? intval($row['cnt']) : 0;
        
        $this->setCache($cacheKey, $result);
        return $result;
    }

    public function listMembers($activity_id, $year)
    {
        $cacheKey = ['members_list', ['activity_id' => $activity_id, 'year' => $year]];
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM best_members WHERE activity_id = :activity_id AND year = :year ORDER BY created_at ASC");
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

    // Check if student is already registered for any Best activity this year
    public function getStudentRegistration($student_id, $year)
    {
        $cacheKey = ['student_registration', ['student_id' => $student_id, 'year' => $year]];
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $stmt = $this->pdo->prepare("SELECT ba.name, ba.id, bm.created_at 
                                     FROM best_members bm 
                                     JOIN best_activities ba ON ba.id = bm.activity_id 
                                     WHERE bm.student_id = :student_id AND bm.year = :year");
        $stmt->execute(['student_id' => $student_id, 'year' => $year]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->setCache($cacheKey, $result);
        return $result;
    }
}
