<?php
require_once 'app/config/database.php';

try {
    $db = (new Database())->getConnection();
    echo "Successfully connected to database.\n";

    // Helper to check if a column exists
    function columnExists($db, $table, $column) {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $stmt->rowCount() > 0;
    }

    // 1. Add slug column if it doesn't exist
    if (!columnExists($db, 'product', 'slug')) {
        $db->exec("ALTER TABLE product ADD COLUMN slug VARCHAR(255) UNIQUE AFTER name");
        echo "Added column 'slug' to product table.\n";
    }

    // 2. Add sale_price column if it doesn't exist
    if (!columnExists($db, 'product', 'sale_price')) {
        $db->exec("ALTER TABLE product ADD COLUMN sale_price DECIMAL(10, 2) DEFAULT NULL AFTER price");
        echo "Added column 'sale_price' to product table.\n";
    }

    // 3. Add stock column if it doesn't exist
    if (!columnExists($db, 'product', 'stock')) {
        $db->exec("ALTER TABLE product ADD COLUMN stock INT DEFAULT 0 AFTER sale_price");
        echo "Added column 'stock' to product table.\n";
    }

    // 4. Add is_featured column if it doesn't exist
    if (!columnExists($db, 'product', 'is_featured')) {
        $db->exec("ALTER TABLE product ADD COLUMN is_featured TINYINT(1) DEFAULT 0 AFTER category_id");
        echo "Added column 'is_featured' to product table.\n";
    }

    // 5. Add brand column if it doesn't exist
    if (!columnExists($db, 'product', 'brand')) {
        $db->exec("ALTER TABLE product ADD COLUMN brand VARCHAR(50) DEFAULT NULL AFTER is_featured");
        echo "Added column 'brand' to product table.\n";
    }

    // 6. Seed/Update data for existing products (IDs 1, 2, 3, 4)
    $productsData = [
        1 => [
            'slug' => 'iphone-15-pro-max-256gb-titanium',
            'sale_price' => 32990000.00,
            'stock' => 15,
            'is_featured' => 1,
            'brand' => 'Apple'
        ],
        2 => [
            'slug' => 'macbook-air-m2-13-6-inch-8gb-256gb',
            'sale_price' => 24990000.00,
            'stock' => 10,
            'is_featured' => 1,
            'brand' => 'Apple'
        ],
        3 => [
            'slug' => 'tai-nghe-airpods-pro-gen-2-usb-c',
            'sale_price' => 5490000.00,
            'stock' => 50,
            'is_featured' => 0,
            'brand' => 'Apple'
        ],
        4 => [
            'slug' => 'ipad-air-5-m1-wifi-64gb',
            'sale_price' => 14290000.00,
            'stock' => 25,
            'is_featured' => 0,
            'brand' => 'Apple'
        ]
    ];

    $stmt = $db->prepare("UPDATE product SET slug = :slug, sale_price = :sale_price, stock = :stock, is_featured = :is_featured, brand = :brand WHERE id = :id");
    
    foreach ($productsData as $id => $data) {
        $stmt->execute([
            ':slug' => $data['slug'],
            ':sale_price' => $data['sale_price'],
            ':stock' => $data['stock'],
            ':is_featured' => $data['is_featured'],
            ':brand' => $data['brand'],
            ':id' => $id
        ]);
        echo "Seeded product ID $id successfully.\n";
    }

    // 7. Add coupon_code column if it doesn't exist
    if (!columnExists($db, 'orders', 'coupon_code')) {
        $db->exec("ALTER TABLE orders ADD COLUMN coupon_code VARCHAR(50) DEFAULT NULL AFTER address");
        echo "Added column 'coupon_code' to orders table.\n";
    }

    // 8. Add discount_amount column if it doesn't exist
    if (!columnExists($db, 'orders', 'discount_amount')) {
        $db->exec("ALTER TABLE orders ADD COLUMN discount_amount DECIMAL(10, 2) DEFAULT 0.00 AFTER coupon_code");
        echo "Added column 'discount_amount' to orders table.\n";
    }

    // 9. Add total_amount column if it doesn't exist
    if (!columnExists($db, 'orders', 'total_amount')) {
        $db->exec("ALTER TABLE orders ADD COLUMN total_amount DECIMAL(10, 2) DEFAULT 0.00 AFTER discount_amount");
        echo "Added column 'total_amount' to orders table.\n";
    }

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
