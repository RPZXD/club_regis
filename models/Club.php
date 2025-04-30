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

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM clubs");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($club_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM clubs WHERE club_id = :club_id");
        $stmt->execute(['club_id' => $club_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO clubs (club_name, description, grade_levels, max_members, current_members, advisor_teacher, status)
                VALUES (:club_name, :description, :grade_levels, :max_members, 0, :advisor_teacher, 'active')";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'club_name' => $data['club_name'],
            'description' => $data['description'],
            'grade_levels' => $data['grade_levels'],
            'max_members' => intval($data['max_members']),
            'advisor_teacher' => $data['advisor_teacher']
        ]);
    }

    public function update($club_id, $data)
    {
        $sql = "UPDATE clubs SET club_name = :club_name, description = :description, grade_levels = :grade_levels, max_members = :max_members WHERE club_id = :club_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'club_id' => $club_id,
            'club_name' => $data['club_name'],
            'description' => $data['description'],
            'grade_levels' => $data['grade_levels'],
            'max_members' => intval($data['max_members'])
        ]);
    }

    public function delete($club_id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM clubs WHERE club_id = :club_id");
        return $stmt->execute(['club_id' => $club_id]);
    }
}
