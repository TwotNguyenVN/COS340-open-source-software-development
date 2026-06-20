<?php
require_once('app/utils/JWTHandler.php');
require_once('app/config/database.php');

$baseUrl = 'http://localhost:88/4851_NguyenNgocTinh_WebsiteBanHang';

function makeRequest($method, $url, $data = null, $token = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $decoded = json_decode($response, true);
    return ['code' => $httpCode, 'body' => $decoded !== null ? $decoded : $response];
}

echo "BẮT ĐẦU TEST TOÀN BỘ API THEO POSTMAN...\n";
echo "----------------------------------------\n";

// 0. Tạo Token ảo cho Admin (role 'admin') và User (role 'user') để bỏ qua bước Login
$jwtHandler = new JWTHandler();
$adminToken = $jwtHandler->encode(['id' => 1, 'username' => 'admin_test', 'role' => 'admin']);
$userToken = $jwtHandler->encode(['id' => 2, 'username' => 'user_test', 'role' => 'user']);
$otherUserToken = $jwtHandler->encode(['id' => 999, 'username' => 'other_user', 'role' => 'user']);

echo "✅ Đã tự động tạo JWT Token thành công.\n";

// --- CATEGORY API ---
echo "\n--- TEST CATEGORY API ---\n";
echo "1. Lấy danh sách danh mục (GET /api/category)...\n";
$catRes = makeRequest('GET', $baseUrl . '/api/category');
if ($catRes['code'] == 200) echo "✅ PASS\n"; else echo "❌ FAIL ({$catRes['code']})\n";

echo "2. Thêm mới danh mục (POST /api/category) - Yêu cầu Admin...\n";
$newCat = makeRequest('POST', $baseUrl . '/api/category', ['name' => 'Danh mục Test', 'status' => 1], $adminToken);
if ($newCat['code'] == 201) {
    echo "✅ PASS (ID: {$newCat['body']['id']})\n";
    $testCatId = $newCat['body']['id'];
} else {
    echo "❌ FAIL ({$newCat['code']})\n";
    $testCatId = 1; // Fallback
}

// --- PRODUCT API ---
echo "\n--- TEST PRODUCT API ---\n";
echo "3. Lấy danh sách sản phẩm (GET /api/product)...\n";
$prodRes = makeRequest('GET', $baseUrl . '/api/product');
if ($prodRes['code'] == 200) echo "✅ PASS\n"; else echo "❌ FAIL ({$prodRes['code']})\n";

echo "4. Thêm sản phẩm mới (POST /api/product)...\n";
$newProdData = [
    'name' => 'Sản phẩm Test', 'price' => 100000, 'sale_price' => 90000,
    'description' => 'Mô tả test', 'category_id' => $testCatId, 'status' => 1, 'stock' => 100
];
$newProd = makeRequest('POST', $baseUrl . '/api/product', $newProdData, $adminToken);
if ($newProd['code'] == 201) {
    echo "✅ PASS (ID: {$newProd['body']['id']})\n";
    $testProdId = $newProd['body']['id'];
} else {
    echo "❌ FAIL ({$newProd['code']})\n"; print_r($newProd['body']);
    $testProdId = 1;
}

echo "5. Thêm sản phẩm LỖI (Giá sale > Giá gốc)...\n";
$errProdData = $newProdData; $errProdData['sale_price'] = 200000;
$errProd = makeRequest('POST', $baseUrl . '/api/product', $errProdData, $adminToken);
if ($errProd['code'] == 400 && isset($errProd['body']['errors']['sale_price']) && strpos($errProd['body']['errors']['sale_price'], 'lớn hơn giá gốc') !== false) {
    echo "✅ PASS (Bắt lỗi chuẩn).\n";
} else {
    echo "❌ FAIL ({$errProd['code']})\n";
}

// --- ORDER API ---
echo "\n--- TEST ORDER API ---\n";
echo "6. Tạo đơn hàng với SP không tồn tại...\n";
$errOrder = makeRequest('POST', $baseUrl . '/api/order', [
    'shipping_name' => 'Nguyễn Văn A', 'shipping_address' => 'HN', 'shipping_phone' => '123456',
    'products' => [['product_id' => 99999, 'quantity' => 1]]
], $userToken);
if ($errOrder['code'] == 400 && strpos($errOrder['body']['message'], 'không tồn tại') !== false) {
    echo "✅ PASS (Mã 400, bắt lỗi chuẩn).\n";
} else echo "❌ FAIL\n";

echo "7. Tạo đơn hàng hợp lệ...\n";
$successOrder = makeRequest('POST', $baseUrl . '/api/order', [
    'shipping_name' => 'Nguyễn Văn A', 'shipping_address' => 'HN', 'shipping_phone' => '123456',
    'products' => [['product_id' => $testProdId, 'quantity' => 1]]
], $userToken);
if ($successOrder['code'] == 201) {
    $orderId = $successOrder['body']['order_id'];
    echo "✅ PASS (Tạo thành công Order ID: $orderId).\n";
} else {
    echo "❌ FAIL ({$successOrder['code']})\n"; print_r($successOrder['body']);
}

if (isset($orderId)) {
    echo "8. Lấy chi tiết đơn hàng $orderId...\n";
    $detailRes = makeRequest('GET', $baseUrl . '/api/order/' . $orderId, null, $adminToken);
    if ($detailRes['code'] == 200) echo "✅ PASS\n"; else echo "❌ FAIL\n";

    echo "9. Admin chuyển trạng thái (Chờ xác nhận -> Đang giao hàng)...\n";
    $updateRes = makeRequest('PUT', $baseUrl . '/api/order/' . $orderId, ['status' => 'Đang giao hàng'], $adminToken);
    if ($updateRes['code'] == 200) echo "✅ PASS\n"; else echo "❌ FAIL\n";

    echo "10. Admin chuyển lùi trạng thái LỖI (Đang giao hàng -> Chờ xác nhận)...\n";
    $updateErrRes = makeRequest('PUT', $baseUrl . '/api/order/' . $orderId, ['status' => 'Chờ xác nhận'], $adminToken);
    if ($updateErrRes['code'] == 400) echo "✅ PASS (Bắt lỗi chuẩn).\n"; else echo "❌ FAIL\n";

    echo "11. Lấy danh sách đơn hàng (GET /api/order) - Role Admin...\n";
    $listAdminRes = makeRequest('GET', $baseUrl . '/api/order', null, $adminToken);
    if ($listAdminRes['code'] == 200 && is_array($listAdminRes['body'])) {
        echo "✅ PASS (Số lượng đơn hàng tìm thấy: " . count($listAdminRes['body']) . ")\n";
    } else {
        echo "❌ FAIL ({$listAdminRes['code']})\n";
    }

    echo "12. Lấy danh sách đơn hàng (GET /api/order) - Role User...\n";
    $listUserRes = makeRequest('GET', $baseUrl . '/api/order', null, $userToken);
    if ($listUserRes['code'] == 200 && is_array($listUserRes['body'])) {
        echo "✅ PASS (Số lượng đơn hàng của User: " . count($listUserRes['body']) . ")\n";
    } else {
        echo "❌ FAIL ({$listUserRes['code']})\n";
    }

    echo "12b. Lấy danh sách đơn hàng (GET /api/order) - Role User khác (không có đơn hàng)...\n";
    $listOtherUserRes = makeRequest('GET', $baseUrl . '/api/order', null, $otherUserToken);
    if ($listOtherUserRes['code'] == 200 && is_array($listOtherUserRes['body']) && count($listOtherUserRes['body']) === 0) {
        echo "✅ PASS (Nhận về 0 đơn hàng như mong đợi).\n";
    } else {
        echo "❌ FAIL ({$listOtherUserRes['code']}, Count: " . (is_array($listOtherUserRes['body']) ? count($listOtherUserRes['body']) : 'not array') . ")\n";
    }

    echo "13. Lấy danh sách đơn hàng không gửi token...\n";
    $listNoTokenRes = makeRequest('GET', $baseUrl . '/api/order');
    if ($listNoTokenRes['code'] == 401) {
        echo "✅ PASS (Bắt lỗi 401 không có token chuẩn).\n";
    } else {
        echo "❌ FAIL ({$listNoTokenRes['code']})\n";
    }
}

echo "\n----------------------------------------\n";
echo "HOÀN TẤT KIỂM THỬ TOÀN BỘ CHỨC NĂNG API.\n";
?>
