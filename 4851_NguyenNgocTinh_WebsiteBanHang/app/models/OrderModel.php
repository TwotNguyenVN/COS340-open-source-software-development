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

    public function getRevenueByDate()
    {
        $query = "SELECT DATE(created_at) as date, COUNT(id) as total_orders, SUM(total_amount) as daily_revenue 
                  FROM " . $this->table_name . " 
                  WHERE status IN ('Đã giao hàng', 'Hoàn thành')
                  GROUP BY DATE(created_at) 
                  ORDER BY date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>
