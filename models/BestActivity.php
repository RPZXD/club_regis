<?php
namespace App\Models;

use PDO;
use PDOException;

class BestActivity
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->initTables();
    }

    // Ensure required tables exist
    private function initTables()
    {
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS best_activities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                grade_levels VARCHAR(255) NOT NULL,
                max_members INT NOT NULL DEFAULT 0,
                year INT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $this->pdo->exec("CREATE TABLE IF NOT EXISTS best_members (
                id INT AUTO_INCREMENT PRIMARY KEY,
                activity_id INT NOT NULL,
                student_id VARCHAR(50) NOT NULL,
                year INT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_student_year (student_id, year),
                INDEX idx_activity_year (activity_id, year),
                CONSTRAINT fk_best_members_activity FOREIGN KEY (activity_id) REFERENCES best_activities(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            // Ignore table creation errors in runtime; deployment may create tables via migrations
        }
    }

    public function getAll($year)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM best_activities WHERE year = :year ORDER BY id ASC");
        $stmt->execute(['year' => $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM best_activities WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create($data)
    {
        $sql = "INSERT INTO best_activities (name, description, grade_levels, max_members, year, created_at, updated_at)
                VALUES (:name, :description, :grade_levels, :max_members, :year, NOW(), NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'grade_levels' => $data['grade_levels'],
            'max_members' => intval($data['max_members']),
            'year' => intval($data['year'])
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE best_activities 
                SET name = :name, description = :description, grade_levels = :grade_levels, max_members = :max_members, updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'grade_levels' => $data['grade_levels'],
            'max_members' => intval($data['max_members'])
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM best_activities WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function countMembers($activity_id, $year)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS cnt FROM best_members WHERE activity_id = :activity_id AND year = :year");
        $stmt->execute(['activity_id' => $activity_id, 'year' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? intval($row['cnt']) : 0;
    }

    public function listMembers($activity_id, $year)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM best_members WHERE activity_id = :activity_id AND year = :year ORDER BY created_at ASC");
        $stmt->execute(['activity_id' => $activity_id, 'year' => $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
