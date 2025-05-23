<?php
namespace App\Models;

use PDO;
use PDOException;

class Club
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll($term = null, $year = null)
    {
        if ($term !== null && $year !== null) {
            $stmt = $this->pdo->prepare("SELECT * FROM clubs WHERE term = :term AND year = :year");
            $stmt->execute(['term' => $term, 'year' => $year]);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM clubs");
        }
        $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $clubs;
    }

    public function getById($club_id, $term = null, $year = null)
    {
        if ($term !== null && $year !== null) {
            $stmt = $this->pdo->prepare("SELECT * FROM clubs WHERE club_id = :club_id AND term = :term AND year = :year");
            $stmt->execute(['club_id' => $club_id, 'term' => $term, 'year' => $year]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM clubs WHERE club_id = :club_id");
            $stmt->execute(['club_id' => $club_id]);
        }
        $club = $stmt->fetch(PDO::FETCH_ASSOC);
        return $club;
    }

    public function create($data)
    {
        $sql = "INSERT INTO clubs (club_name, description, grade_levels, max_members, advisor_teacher, term, year, created_at, updated_at)
                VALUES (:club_name, :description, :grade_levels, :max_members, :advisor_teacher, :term, :year, NOW(), NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'club_name' => $data['club_name'],
            'description' => $data['description'],
            'grade_levels' => $data['grade_levels'],
            'max_members' => intval($data['max_members']),
            'advisor_teacher' => $data['advisor_teacher'],
            'term' => $data['term'],
            'year' => $data['year']
        ]);
    }

    public function update($club_id, $data, $term = null, $year = null)
    {
        if ($term !== null && $year !== null) {
            $sql = "UPDATE clubs SET club_name = :club_name, description = :description, grade_levels = :grade_levels, max_members = :max_members, updated_at = NOW() WHERE club_id = :club_id AND term = :term AND year = :year";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'club_id' => $club_id,
                'club_name' => $data['club_name'],
                'description' => $data['description'],
                'grade_levels' => $data['grade_levels'],
                'max_members' => intval($data['max_members']),
                'term' => $term,
                'year' => $year
            ]);
        } else {
            $sql = "UPDATE clubs SET club_name = :club_name, description = :description, grade_levels = :grade_levels, max_members = :max_members, updated_at = NOW() WHERE club_id = :club_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'club_id' => $club_id,
                'club_name' => $data['club_name'],
                'description' => $data['description'],
                'grade_levels' => $data['grade_levels'],
                'max_members' => intval($data['max_members'])
            ]);
        }
    }

    public function delete($club_id, $term = null, $year = null)
    {
        if ($term !== null && $year !== null) {
            $stmt = $this->pdo->prepare("DELETE FROM clubs WHERE club_id = :club_id AND term = :term AND year = :year");
            return $stmt->execute(['club_id' => $club_id, 'term' => $term, 'year' => $year]);
        } else {
            $stmt = $this->pdo->prepare("DELETE FROM clubs WHERE club_id = :club_id");
            return $stmt->execute(['club_id' => $club_id]);
        }
    }

    public function getCurrentMembers($club_id, $term = null, $year = null)
    {
        if ($term !== null && $year !== null) {
            $sql = "SELECT COUNT(*) as cnt FROM club_members WHERE club_id = :club_id AND term = :term AND year = :year";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['club_id' => $club_id, 'term' => $term, 'year' => $year]);
        } else {
            $sql = "SELECT COUNT(*) as cnt FROM club_members WHERE club_id = :club_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['club_id' => $club_id]);
        }
        $row = $stmt->fetch();
        return $row ? intval($row['cnt']) : 0;
    }

    public function getByAdvisor($advisor_teacher, $term = null, $year = null)
    {
        if ($term !== null && $year !== null) {
            $stmt = $this->pdo->prepare("SELECT * FROM clubs WHERE advisor_teacher = :advisor_teacher AND term = :term AND year = :year");
            $stmt->execute(['advisor_teacher' => $advisor_teacher, 'term' => $term, 'year' => $year]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM clubs WHERE advisor_teacher = :advisor_teacher");
            $stmt->execute(['advisor_teacher' => $advisor_teacher]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}
