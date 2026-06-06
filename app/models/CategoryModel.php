<?php
class CategoryModel
{
    private $conn;
    private $table_name = "category";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getCategories()
    {
        $query = "SELECT c.id, c.name, c.description, COUNT(p.id) AS product_count 
                  FROM " . $this->table_name . " c 
                  LEFT JOIN product p ON c.id = p.category_id 
                  GROUP BY c.id, c.name, c.description 
                  ORDER BY c.id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function getCategoryById($id)
    {
        $query = "SELECT id, name, description FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function addCategory($name, $description)
    {
        $errors = [];
        if (empty($name)) {
            $errors['name'] = 'Tên danh mục không được để trống';
        }
        if (count($errors) > 0) {
            return $errors;
        }

        // Find the lowest available unused ID (gap filling)
        $query_id = "SELECT id FROM " . $this->table_name . " WHERE id = 1";
        $stmt_id = $this->conn->prepare($query_id);
        $stmt_id->execute();
        if ($stmt_id->rowCount() == 0) {
            $next_id = 1;
        } else {
            $query_gap = "SELECT c1.id + 1 AS next_id
                          FROM " . $this->table_name . " c1
                          LEFT JOIN " . $this->table_name . " c2 ON c1.id + 1 = c2.id
                          WHERE c2.id IS NULL
                          ORDER BY next_id ASC
                          LIMIT 1";
            $stmt_gap = $this->conn->prepare($query_gap);
            $stmt_gap->execute();
            $row_gap = $stmt_gap->fetch(PDO::FETCH_ASSOC);
            $next_id = $row_gap['next_id'];
        }

        $query = "INSERT INTO " . $this->table_name . " (id, name, description) VALUES (:id, :name, :description)";
        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));

        $stmt->bindParam(':id', $next_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateCategory($id, $name, $description)
    {
        $errors = [];
        if (empty($name)) {
            $errors['name'] = 'Tên danh mục không được để trống';
        }
        if (count($errors) > 0) {
            return $errors;
        }

        $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deleteCategory($id)
    {
        // Check if there are products associated with this category
        $query_check = "SELECT COUNT(*) as count FROM product WHERE category_id = :id";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(':id', $id);
        $stmt_check->execute();
        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            // Cannot delete if there are products in it
            return "Không thể xóa danh mục này vì đang có sản phẩm thuộc danh mục!";
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
