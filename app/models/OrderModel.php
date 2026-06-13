<?php
class OrderModel
{
    private $conn;
    private $table_name = "orders";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllOrders()
    {
        $query = "SELECT o.*, a.username, a.fullname 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN account a ON o.account_id = a.id 
                  ORDER BY o.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function searchOrders($filters)
    {
        $query = "SELECT o.*, a.username, a.fullname 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN account a ON o.account_id = a.id 
                  WHERE 1=1";
        
        $params = [];

        if (!empty($filters['search'])) {
            $query .= " AND (o.id LIKE :search OR o.name LIKE :search OR o.phone LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status']) && $filters['status'] !== 'Tất cả') {
            $query .= " AND o.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['start_date'])) {
            $query .= " AND DATE(o.created_at) >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $query .= " AND DATE(o.created_at) <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $query .= " ORDER BY o.id DESC";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getOrdersByAccountId($accountId)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE account_id = :account_id ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':account_id', $accountId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getOrderById($orderId)
    {
        $query = "SELECT o.*, a.username, a.fullname 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN account a ON o.account_id = a.id 
                  WHERE o.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getOrderDetails($orderId)
    {
        $query = "SELECT od.*, p.name as product_name, p.image as product_image 
                  FROM order_details od 
                  JOIN product p ON od.product_id = p.id 
                  WHERE od.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateOrderStatus($orderId, $status)
    {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateOrderNotes($orderId, $notes)
    {
        $query = "UPDATE " . $this->table_name . " SET notes = :notes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':notes', $notes, PDO::PARAM_STR);
        $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getRevenueByDate($startDate = null, $endDate = null)
    {
        $query = "SELECT DATE(created_at) as date, COUNT(id) as total_orders, SUM(total_amount) as daily_revenue 
                  FROM " . $this->table_name . " 
                  WHERE status IN ('Đã giao hàng', 'Hoàn thành', 'Từ chối hoàn trả')";
                  
        if ($startDate && $endDate) {
            $query .= " AND DATE(created_at) BETWEEN :start_date AND :end_date";
        }
        
        $query .= " GROUP BY DATE(created_at) 
                    ORDER BY date DESC";
                    
        $stmt = $this->conn->prepare($query);
        
        if ($startDate && $endDate) {
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getCompletedOrdersByDateRange($startDate = null, $endDate = null)
    {
        $query = "SELECT *, DATE(created_at) as date_only 
                  FROM " . $this->table_name . " 
                  WHERE status IN ('Đã giao hàng', 'Hoàn thành', 'Từ chối hoàn trả')";
                  
        if ($startDate && $endDate) {
            $query .= " AND DATE(created_at) BETWEEN :start_date AND :end_date";
        }
        
        $query .= " ORDER BY created_at DESC";
                    
        $stmt = $this->conn->prepare($query);
        
        if ($startDate && $endDate) {
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateReturnRequest($orderId, $productsJson, $reason)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'Yêu cầu hoàn trả', return_products = :products, return_reason = :reason 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':products', $productsJson, PDO::PARAM_STR);
        $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
        $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function processReturnRequest($orderId, $status, $adminReply)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status, return_admin_reply = :reply 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':reply', $adminReply, PDO::PARAM_STR);
        $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function createOrderWithDetails($accountId, $orderData, $productsArray, $productModel)
    {
        try {
            $this->conn->beginTransaction();

            $totalAmount = 0;
            // 1. Kiểm tra tồn kho và tính tổng tiền
            foreach ($productsArray as &$item) {
                $product = $productModel->getProductById($item['product_id']);
                if (!$product) {
                    throw new Exception("Sản phẩm có ID " . $item['product_id'] . " không tồn tại.");
                }
                if ($item['quantity'] <= 0) {
                    throw new Exception("Số lượng sản phẩm không hợp lệ.");
                }
                if ($product->stock < $item['quantity']) {
                    throw new Exception("Sản phẩm '" . $product->name . "' chỉ còn " . $product->stock . " trong kho.");
                }
                // Tính giá (lấy giá khuyến mãi nếu có)
                $price = $product->sale_price !== null ? $product->sale_price : $product->price;
                $item['price'] = $price;
                $totalAmount += $price * $item['quantity'];
            }

            // Tính phí ship giả định (như code cũ: >= 50tr thì free ship, còn lại 100k)
            $shipping_fee = $totalAmount >= 50000000 ? 0 : 100000;
            $totalAmount += $shipping_fee;

            // 2. Insert vào bảng orders
            $query = "INSERT INTO " . $this->table_name . " 
                      (account_id, name, phone, address, total_amount, status) 
                      VALUES (:account_id, :name, :phone, :address, :total_amount, 'Chờ xác nhận')";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':account_id', $accountId);
            $stmt->bindParam(':name', $orderData['shipping_name']);
            $stmt->bindParam(':phone', $orderData['shipping_phone']);
            $stmt->bindParam(':address', $orderData['shipping_address']);
            $stmt->bindParam(':total_amount', $totalAmount);
            $stmt->execute();

            $orderId = $this->conn->lastInsertId();

            // 3. Insert vào order_details và cập nhật stock
            foreach ($productsArray as $item) {
                // Insert detail
                $qDetail = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                            VALUES (:order_id, :product_id, :quantity, :price)";
                $stmtDetail = $this->conn->prepare($qDetail);
                $stmtDetail->bindParam(':order_id', $orderId);
                $stmtDetail->bindParam(':product_id', $item['product_id']);
                $stmtDetail->bindParam(':quantity', $item['quantity']);
                $stmtDetail->bindParam(':price', $item['price']);
                $stmtDetail->execute();

                // Update stock
                $qStock = "UPDATE product SET stock = stock - :quantity WHERE id = :product_id";
                $stmtStock = $this->conn->prepare($qStock);
                $stmtStock->bindParam(':quantity', $item['quantity']);
                $stmtStock->bindParam(':product_id', $item['product_id']);
                $stmtStock->execute();
            }

            $this->conn->commit();
            return ['success' => true, 'order_id' => $orderId];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
