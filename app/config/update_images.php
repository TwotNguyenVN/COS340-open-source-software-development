<?php
require_once 'app/config/database.php';
try {
    $db = (new Database())->getConnection();
    
    $updates = [
        1 => 'public/uploads/iphone_15_pro_max.png',
        2 => 'public/uploads/macbook_air_m2.png',
        3 => 'public/uploads/airpods_pro_2.png',
        4 => 'public/uploads/ipad_air_5.png'
    ];
    
    $stmt = $db->prepare("UPDATE product SET image = :image WHERE id = :id");
    foreach ($updates as $id => $path) {
        $stmt->execute([':image' => $path, ':id' => $id]);
        echo "Updated image for product ID $id.\n";
    }
    echo "All image paths updated successfully!\n";
} catch (Exception $e) {
    echo "Error updating images: " . $e->getMessage() . "\n";
}
?>
