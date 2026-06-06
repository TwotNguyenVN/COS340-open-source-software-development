<?php
class ProductModel
{
    private $conn;
    private $table_name = "product";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getProducts()
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.sale_price, p.stock, p.image, p.category_id, p.is_featured, p.brand, p.slug, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    // Lấy sản phẩm với phân trang
    public function getProductsPaginated($page = 1, $limit = 8)
    {
        $offset = ($page - 1) * $limit;
        $query = "SELECT p.id, p.name, p.description, p.price, p.sale_price, p.stock, p.image, p.category_id, p.is_featured, p.brand, p.slug, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  ORDER BY p.id DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Đếm tổng số sản phẩm
    public function getTotalProducts()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total;
    }

    // Tìm kiếm sản phẩm theo từ khóa
    public function searchProducts($keyword, $page = 1, $limit = 8)
    {
        $offset = ($page - 1) * $limit;
        $searchTerm = '%' . $keyword . '%';
        $query = "SELECT p.id, p.name, p.description, p.price, p.sale_price, p.stock, p.image, p.category_id, p.is_featured, p.brand, p.slug, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.name LIKE :keyword OR p.description LIKE :keyword2 OR c.name LIKE :keyword3
                  ORDER BY p.id DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->bindParam(':keyword2', $searchTerm);
        $stmt->bindParam(':keyword3', $searchTerm);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Đếm tổng số sản phẩm tìm kiếm
    public function getTotalSearchProducts($keyword)
    {
        $searchTerm = '%' . $keyword . '%';
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.name LIKE :keyword OR p.description LIKE :keyword2 OR c.name LIKE :keyword3";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->bindParam(':keyword2', $searchTerm);
        $stmt->bindParam(':keyword3', $searchTerm);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total;
    }

    // Lấy sản phẩm theo danh mục
    public function getProductsByCategory($category_id, $page = 1, $limit = 8)
    {
        $offset = ($page - 1) * $limit;
        $query = "SELECT p.id, p.name, p.description, p.price, p.sale_price, p.stock, p.image, p.category_id, p.is_featured, p.brand, p.slug, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.category_id = :category_id
                  ORDER BY p.id DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Đếm tổng số sản phẩm theo danh mục
    public function getTotalProductsByCategory($category_id)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total;
    }

    public function getProductById($id)
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.sale_price, p.stock, p.image, p.category_id, p.is_featured, p.brand, p.slug, c.name as category_name,
                         (SELECT COUNT(*) FROM order_details od WHERE od.product_id = p.id) as sales_count
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function addProduct($name, $description, $price, $category_id, $image = "", $stock = 0)
    {
        $errors = [];
        if (empty($name)) {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        }
        if (empty($description)) {
            $errors['description'] = 'Mô tả không được để trống';
        }
        if (!is_numeric($price) || $price < 0) {
            $errors['price'] = 'Giá sản phẩm không hợp lệ';
        }
        if ($stock < 0) {
            $errors['stock'] = 'Số lượng sản phẩm không được là số âm';
        }
        if (empty($category_id)) {
            $errors['category_id'] = 'Vui lòng chọn danh mục sản phẩm';
        }
        if (count($errors) > 0) {
            return $errors;
        }

        $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id, image, stock) 
                  VALUES (:name, :description, :price, :category_id, :image, :stock)";
        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));
        $stock = (int)$stock;

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateProduct($id, $name, $description, $price, $category_id, $image = "", $stock = 0)
    {
        $errors = [];
        if (empty($name)) {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        }
        if (empty($description)) {
            $errors['description'] = 'Mô tả không được để trống';
        }
        if (!is_numeric($price) || $price < 0) {
            $errors['price'] = 'Giá sản phẩm không hợp lệ';
        }
        if ($stock < 0) {
            $errors['stock'] = 'Số lượng sản phẩm không được là số âm';
        }
        if (empty($category_id)) {
            $errors['category_id'] = 'Vui lòng chọn danh mục sản phẩm';
        }
        if (count($errors) > 0) {
            return $errors;
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, description=:description, price=:price, category_id=:category_id, image=:image, stock=:stock 
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $id = htmlspecialchars(strip_tags($id));
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));
        $stock = (int)$stock;

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function isProductSold($id)
    {
        $query = "SELECT COUNT(*) as total FROM order_details WHERE product_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return (int)$row->total > 0;
    }

    public function deleteProduct($id)
    {
        if ($this->isProductSold($id)) {
            return false;
        }
        // First, get the product to find the image file path to delete it
        $product = $this->getProductById($id);
        if ($product && !empty($product->image) && file_exists($product->image)) {
            unlink($product->image);
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getFeaturedProducts($limit = 4)
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.sale_price, p.stock, p.image, p.category_id, p.is_featured, p.brand, p.slug, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.is_featured = 1
                  ORDER BY p.id DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getNewArrivals($limit = 4)
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.sale_price, p.stock, p.image, p.category_id, p.is_featured, p.brand, p.slug, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  ORDER BY p.id DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDiscountedProducts($limit = 4)
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.sale_price, p.stock, p.image, p.category_id, p.is_featured, p.brand, p.slug, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.sale_price IS NOT NULL AND p.sale_price < p.price
                  ORDER BY (p.price - p.sale_price) DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy sản phẩm theo các bộ lọc kết hợp (Tìm kiếm, Danh mục, Giá, Sắp xếp, Phân trang)
    public function getProductsFiltered($filters = [], $page = 1, $limit = 8)
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT p.id, p.name, p.description, p.price, p.sale_price, p.stock, p.image, p.category_id, p.is_featured, p.brand, p.slug, c.name as category_name,
                       (SELECT COUNT(*) FROM order_details od WHERE od.product_id = p.id) as sales_count
                FROM " . $this->table_name . " p
                LEFT JOIN category c ON p.category_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search2 OR c.name LIKE :search3)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[':search'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = (int)$filters['category_id'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND COALESCE(p.sale_price, p.price) >= :min_price";
            $params[':min_price'] = (float)$filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND COALESCE(p.sale_price, p.price) <= :max_price";
            $params[':max_price'] = (float)$filters['max_price'];
        }
        
        // Sorting
        $sort = $filters['sort_by'] ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY COALESCE(p.sale_price, p.price) ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY COALESCE(p.sale_price, p.price) DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY p.name ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY p.name DESC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY p.id DESC";
                break;
        }
        
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Đếm tổng số sản phẩm sau khi lọc
    public function getTotalProductsFiltered($filters = [])
    {
        $sql = "SELECT COUNT(*) as total 
                FROM " . $this->table_name . " p
                LEFT JOIN category c ON p.category_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search2 OR c.name LIKE :search3)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[':search'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = (int)$filters['category_id'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND COALESCE(p.sale_price, p.price) >= :min_price";
            $params[':min_price'] = (float)$filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND COALESCE(p.sale_price, p.price) <= :max_price";
            $params[':max_price'] = (float)$filters['max_price'];
        }
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total;
    }
}
?>
