<?php
namespace App;

use PDO;
use PDOException;

class DatabaseClub
{
    private $pdo;

    public function __construct(
        $host = 'localhost',
        $dbname = 'phichaia_club',
        $username = 'root',
        $password = ''
        // $username = 'phichaia_stdcare',
        // $password = '48dv_m64N'
    ) {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // Fallback for local XAMPP (root with no password)
            if (!($username === 'root' && $password === '')) {
                try {
                    $this->pdo = new PDO($dsn, 'root', '', [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    return; // success on fallback
                } catch (PDOException $e2) {
                    // ignore and rethrow original
                }
            }
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception('Database query error: ' . $e->getMessage());
        }
    }

    public function getCurrentMembers($club_id)
    {
        $sql = "SELECT * FROM club_members WHERE club_id = :club_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['club_id' => $club_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}
