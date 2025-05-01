<?php
namespace App\Models;

use PDO;

class RegisClub
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function register($student_id, $club_id, $year, $term)
    {
        $sql = "INSERT INTO club_members (student_id, club_id, year, term, created_at) 
                VALUES (:student_id, :club_id, :year, :term, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'student_id' => $student_id,
            'club_id' => $club_id,
            'year' => $year,
            'term' => $term
        ]);
    }

    public function hasRegistered($student_id, $year, $term)
    {
        $sql = "SELECT * FROM club_members WHERE student_id = :student_id AND year = :year AND term = :term";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'student_id' => $student_id,
            'year' => $year,
            'term' => $term
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
