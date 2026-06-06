<?php
require_once 'app/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "ALTER TABLE orders 
              ADD COLUMN return_reason TEXT NULL,
              ADD COLUMN return_products TEXT NULL,
              ADD COLUMN return_admin_reply TEXT NULL";
    
    $conn->exec($query);
    echo "Thêm các cột thành công!";
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
