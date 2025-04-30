<?php
class TermPeeModel {
    private $conn;

    public function __construct() {
        $db = new Database('phichaia_student');
        $this->conn = $db->getConnection();
    }

    public function getTermPee() {
        $sql = "SELECT 
                    term, pee
                FROM termpee
                WHERE id = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Return as ['term' => ..., 'pee' => ...]
        return [
            'term' => $row['term'] ?? null,
            'pee' => $row['pee'] ?? null
        ];
    }


}


