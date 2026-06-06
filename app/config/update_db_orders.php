<?php
require_once __DIR__ . '/database.php';
try {
    $db = (new Database())->getConnection();
    echo "Connected to database successfully.<br>";

    // Function to check if a column exists
    function colExists($db, $table, $column) {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $stmt->rowCount() > 0;
    }

    // Add account_id
    if (!colExists($db, 'orders', 'account_id')) {
        $db->exec("ALTER TABLE orders ADD COLUMN account_id INT DEFAULT NULL AFTER id");
        $db->exec("ALTER TABLE orders ADD CONSTRAINT fk_orders_account FOREIGN KEY (account_id) REFERENCES account(id) ON DELETE SET NULL");
        echo "Added column 'account_id' to 'orders' table.<br>";
    } else {
        echo "Column 'account_id' already exists.<br>";
    }

    // Add status
    if (!colExists($db, 'orders', 'status')) {
        $db->exec("ALTER TABLE orders ADD COLUMN status VARCHAR(50) DEFAULT 'Chờ xác nhận' AFTER total_amount");
        echo "Added column 'status' to 'orders' table.<br>";
    } else {
        echo "Column 'status' already exists.<br>";
    }

    echo "Database schema updated successfully!";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage();
}
?>
