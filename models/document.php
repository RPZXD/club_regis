<?php
namespace App\Models;

require_once __DIR__ . '/../classes/DatabaseUsers.php';

use App\DatabaseDocuments;
use App\DatabaseUsers;

class Document
{
    private $db;
    private $dbUsers;

    public function __construct()
    {
        $this->db = new DatabaseDocuments();
        $this->dbUsers = new \App\DatabaseUsers(); // เพิ่ม backslash เพื่อเรียก class จาก global namespace
    }

    // ดึงเอกสารทั้งหมด พร้อมชื่อผู้ upload (Teach_name)
    public function getAll()
    {
        $uploads = $this->db->fetchAll("SELECT * FROM uploads ORDER BY date_upload DESC");
        // รวบรวม upload_by ทั้งหมด
        $teachIds = [];
        foreach ($uploads as $row) {
            if (!empty($row['upload_by'])) {
                $teachIds[$row['upload_by']] = null;
            }
        }
        // ดึงชื่อครูทั้งหมดในครั้งเดียว
        $teachNames = [];
        if (!empty($teachIds)) {
            $placeholders = implode(',', array_fill(0, count($teachIds), '?'));
            $sql = "SELECT Teach_id, Teach_name FROM teacher WHERE Teach_id IN ($placeholders)";
            $stmt = $this->dbUsers->query($sql, array_keys($teachIds));
            foreach ($stmt->fetchAll() as $row) {
                $teachNames[$row['Teach_id']] = $row['Teach_name'];
            }
        }
        // map Teach_name กลับเข้า uploads
        foreach ($uploads as &$row) {
            $row['Teach_name'] = isset($teachNames[$row['upload_by']]) ? $teachNames[$row['upload_by']] : null;
        }
        unset($row);
        return $uploads;
    }

    // ดึงเอกสารตาม id
    public function getById($id)
    {
        $sql = "SELECT * FROM uploads WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
}