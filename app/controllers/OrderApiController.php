<?php
require_once('app/config/database.php');
require_once('app/models/OrderModel.php');
require_once('app/models/ProductModel.php');
require_once('app/models/AccountModel.php');
require_once('app/utils/JWTHandler.php');

class OrderApiController
{
    private $orderModel;
    private $productModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->orderModel = new OrderModel($this->db);
        $this->productModel = new ProductModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    private function authenticate()
    {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? '';

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['message' => 'Missing or invalid token']);
            exit;
        }

        $token = $matches[1];
        $decoded = $this->jwtHandler->decode($token);

        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['message' => 'Token expired or invalid']);
            exit;
        }

        return $decoded;
    }

    // GET /api/order
    public function index()
    {
        header('Content-Type: application/json');
        $user = $this->authenticate();

        if ($user['role'] === 'admin') {
            $orders = $this->orderModel->getAllOrders();
        } else {
            $orders = $this->orderModel->getOrdersByAccountId($user['id']);
        }

        echo json_encode($orders);
    }

    // GET /api/order/1
    public function show($id)
    {
        header('Content-Type: application/json');
        $user = $this->authenticate();

        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy đơn hàng']);
            return;
        }

        // Only Admin or the order owner can view it
        if ($user['role'] !== 'admin' && $order->account_id != $user['id']) {
            http_response_code(403);
            echo json_encode(['message' => 'Quyền truy cập bị từ chối']);
            return;
        }

        $details = $this->orderModel->getOrderDetails($id);
        
        echo json_encode([
            'order' => $order,
            'details' => $details
        ]);
    }

    // POST /api/order
    public function store()
    {
        header('Content-Type: application/json');
        $user = $this->authenticate();

        $data = json_decode(file_get_contents("php://input"), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['message' => 'Malformed JSON string']);
            return;
        }

        $shipping_name = trim($data['shipping_name'] ?? '');
        $shipping_address = trim($data['shipping_address'] ?? '');
        $shipping_phone = trim($data['shipping_phone'] ?? '');
        $products = $data['products'] ?? [];

        if (empty($shipping_name) || empty($shipping_address) || empty($shipping_phone)) {
            http_response_code(400);
            echo json_encode(['message' => 'Vui lòng cung cấp đầy đủ thông tin giao hàng']);
            return;
        }

        if (!is_array($products) || empty($products)) {
            http_response_code(400);
            echo json_encode(['message' => 'Giỏ hàng trống']);
            return;
        }

        $orderData = [
            'shipping_name' => $shipping_name,
            'shipping_address' => $shipping_address,
            'shipping_phone' => $shipping_phone
        ];

        // Process Transaction
        $result = $this->orderModel->createOrderWithDetails($user['id'], $orderData, $products, $this->productModel);

        if ($result['success']) {
            http_response_code(201);
            echo json_encode(['message' => 'Tạo đơn hàng thành công', 'order_id' => $result['order_id']]);
        } else {
            http_response_code(400);
            echo json_encode(['message' => $result['message']]);
        }
    }

    // PUT /api/order/1
    public function update($id)
    {
        header('Content-Type: application/json');
        $user = $this->authenticate();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Chỉ Admin mới có quyền cập nhật trạng thái đơn hàng']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['message' => 'Malformed JSON string']);
            return;
        }

        $status = trim($data['status'] ?? '');
        $validStatuses = ['Chờ xác nhận', 'Đang chuẩn bị hàng', 'Đang giao hàng', 'Đã giao hàng', 'Đã thu hồi', 'Đã hủy'];
        
        if (!in_array($status, $validStatuses)) {
            http_response_code(400);
            echo json_encode(['message' => 'Trạng thái đơn hàng không hợp lệ']);
            return;
        }

        $currentOrder = $this->orderModel->getOrderById($id);
        if (!$currentOrder) {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy đơn hàng']);
            return;
        }

        // Validation for forward-only transition
        $coreStatuses = ['Chờ xác nhận', 'Đang chuẩn bị hàng', 'Đang giao hàng', 'Đã giao hàng'];
        $currentIdx = array_search($currentOrder->status, $coreStatuses);
        $newIdx = array_search($status, $coreStatuses);

        if ($currentIdx !== false && $newIdx !== false) {
            if ($newIdx < $currentIdx) {
                http_response_code(400);
                echo json_encode(['message' => 'Không thể quay về trạng thái trước! (Từ ' . $currentOrder->status . ' sang ' . $status . ')']);
                return;
            }
        }

        $result = $this->orderModel->updateOrderStatus($id, $status);
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => 'Cập nhật trạng thái thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Lỗi máy chủ khi cập nhật']);
        }
    }
}
?>
