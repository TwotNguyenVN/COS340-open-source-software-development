<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/utils/JWTHandler.php');

class ProductApiController
{
    private $productModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    private function authenticate()
    {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            }
        }

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $arr = explode(" ", $authHeader);
            $jwt = $arr[1] ?? null;
            if ($jwt) {
                $decoded = $this->jwtHandler->decode($jwt);
                return $decoded ? (array)$decoded : false;
            }
        }
        return false;
    }

    // Lấy danh sách sản phẩm — Public access (không yêu cầu JWT)
    public function index()
    {
        header('Content-Type: application/json');
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 8;
        
        $filters = [
            'search' => isset($_GET['search']) ? trim($_GET['search']) : '',
            'category_id' => isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null,
            'min_price' => isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null,
            'max_price' => isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null,
            'sort_by' => isset($_GET['sort_by']) ? trim($_GET['sort_by']) : 'newest'
        ];

        $products = $this->productModel->getProductsFiltered($filters, $page, $limit);
        $totalProducts = $this->productModel->getTotalProductsFiltered($filters);
        $totalPages = ceil($totalProducts / $limit);

        echo json_encode([
            'products' => $products,
            'totalProducts' => $totalProducts,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }

    // Lấy thông tin sản phẩm theo ID — Public access
    public function show($id)
    {
        header('Content-Type: application/json');
        $product = $this->productModel->getProductById($id);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
        }
    }

    // Thêm sản phẩm mới — Yêu cầu JWT của Admin
    public function store()
    {
        header('Content-Type: application/json');
        
        $decoded = $this->authenticate();
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized - Vui lòng đăng nhập để thực hiện']);
            return;
        }

        if (!isset($decoded['role']) || $decoded['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden - Chỉ tài khoản Admin mới có quyền thực hiện']);
            return;
        }

        // Hỗ trợ cả JSON và FormData
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if (strpos($contentType, 'application/json') !== false) {
            $raw_input = file_get_contents("php://input");
            $data = json_decode($raw_input, true);
            
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['message' => 'Định dạng JSON không hợp lệ (Syntax Error). Vui lòng kiểm tra lại Body request.']);
                return;
            }
        } else {
            $data = $_POST;
        }

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $sale_price = $data['sale_price'] ?? null;
        $category_id = $data['category_id'] ?? null;
        $stock = isset($data['stock']) ? (int)$data['stock'] : 0;
        
        $image = "";
        $errors = [];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            try {
                $image = $this->uploadImage($_FILES['image']);
            } catch (Exception $e) {
                $errors['image'] = $e->getMessage();
            }
        } else if (isset($data['image'])) {
            $image = $data['image'];
        }

        if ($sale_price !== null && $sale_price !== '' && (float)$sale_price > (float)$price) {
            $errors['sale_price'] = 'Giá khuyến mãi không được lớn hơn giá gốc';
        }

        if (count($errors) > 0) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            return;
        }

        $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image, $stock, $sale_price);

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } elseif ($result !== false) {
            http_response_code(201);
            echo json_encode(['message' => 'Product created successfully', 'id' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Lỗi máy chủ']);
        }
    }

    // Cập nhật sản phẩm theo ID — Yêu cầu JWT của Admin
    public function update($id)
    {
        header('Content-Type: application/json');

        $decoded = $this->authenticate();
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized - Vui lòng đăng nhập để thực hiện']);
            return;
        }

        if (!isset($decoded['role']) || $decoded['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden - Chỉ tài khoản Admin mới có quyền thực hiện']);
            return;
        }

        // Hỗ trợ cả JSON và FormData
        // Note: PUT method typically doesn't populate $_POST in PHP if it's multipart/form-data.
        // Therefore, we recommend using POST method with ?_method=PUT in frontend, or just read php://input.
        // To make it easy, we will check $_POST first.
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if (strpos($contentType, 'application/json') !== false) {
            $raw_input = file_get_contents("php://input");
            $data = json_decode($raw_input, true);
            
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['message' => 'Định dạng JSON không hợp lệ (Syntax Error). Vui lòng kiểm tra lại Body request.']);
                return;
            }
        } else {
            // PHP doesnt parse multipart/form-data for PUT requests out of the box
            // So we handle it via _POST if they used POST method with _method=PUT hack
            $data = $_POST;
        }

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;
        $stock = isset($data['stock']) ? (int)$data['stock'] : 0;

        $existingProduct = $this->productModel->getProductById($id);
        if (!$existingProduct) {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
            return;
        }
        
        // If client sent existing_image, use it (allows clearing the image if empty string)
        if (isset($data['existing_image'])) {
            $image = $data['existing_image'];
        } else {
            $image = $existingProduct ? $existingProduct->image : '';
        }
        
        $errors = [];

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            try {
                $image = $this->uploadImage($_FILES['image']);
                if ($existingProduct && !empty($existingProduct->image) && file_exists($existingProduct->image)) {
                    unlink($existingProduct->image);
                }
            } catch (Exception $e) {
                $errors['image'] = $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            return;
        }

        $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image, $stock);

        if ($result === true) {
            echo json_encode(['message' => 'Product updated successfully']);
        } else if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product update failed']);
        }
    }
    
    private function uploadImage($file)
    {
        $target_dir = "public/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = time() . "_" . basename($file["name"]);
        $target_file = $target_dir . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }

        if ($file["size"] > 5 * 1024 * 1024) {
            throw new Exception("Hình ảnh quá lớn (Tối đa 5MB).");
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($imageFileType, $allowed)) {
            throw new Exception("Chỉ hỗ trợ định dạng JPG, JPEG, PNG, GIF, WEBP.");
        }

        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }

        return $target_file;
    }

    // Xóa sản phẩm theo ID — Yêu cầu JWT của Admin
    public function destroy($id)
    {
        header('Content-Type: application/json');

        $decoded = $this->authenticate();
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized - Vui lòng đăng nhập để thực hiện']);
            return;
        }

        if (!isset($decoded['role']) || $decoded['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden - Chỉ tài khoản Admin mới có quyền thực hiện']);
            return;
        }

        if ($this->productModel->isProductSold($id)) {
            http_response_code(400);
            echo json_encode(['message' => 'Cannot delete product: product has already been sold (referenced in orders)']);
            return;
        }

        $result = $this->productModel->deleteProduct($id);

        if ($result) {
            echo json_encode(['message' => 'Product deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product deletion failed']);
        }
    }
}
?>
