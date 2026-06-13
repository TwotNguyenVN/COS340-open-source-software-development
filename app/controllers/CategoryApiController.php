<?php
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');
require_once('app/utils/JWTHandler.php');

class CategoryApiController
{
    private $categoryModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    private function authenticate()
    {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        if (!isset($headers['Authorization']) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers['Authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
        }
        
        $authHeader = $headers['Authorization'] ?? '';
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $decoded = $this->jwtHandler->decode($matches[1]);
            return $decoded ? (array)$decoded : false;
        }
        return false;
    }

    private function checkAdmin()
    {
        $user = $this->authenticate();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            exit;
        }
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden - Yêu cầu quyền Admin']);
            exit;
        }
    }

    // Lấy danh sách danh mục (Public)
    public function index()
    {
        header('Content-Type: application/json');
        $categories = $this->categoryModel->getCategories();
        echo json_encode($categories);
    }

    // POST /api/category
    public function store()
    {
        header('Content-Type: application/json');
        $this->checkAdmin();

        $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');

        $result = $this->categoryModel->addCategory($name, $description);

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } elseif ($result) {
            http_response_code(201);
            $stmt = $this->db->query("SELECT MAX(id) as id FROM category");
            $lastId = $stmt->fetch(PDO::FETCH_OBJ)->id;
            echo json_encode(['message' => 'Tạo danh mục thành công', 'id' => $lastId]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Lỗi máy chủ']);
        }
    }

    // PUT /api/category/{id}
    public function update($id)
    {
        header('Content-Type: application/json');
        $this->checkAdmin();

        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) parse_str(file_get_contents("php://input"), $data);

        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');

        $result = $this->categoryModel->updateCategory($id, $name, $description);

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } elseif ($result) {
            http_response_code(200);
            echo json_encode(['message' => 'Cập nhật danh mục thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Lỗi máy chủ']);
        }
    }

    // DELETE /api/category/{id}
    public function destroy($id)
    {
        header('Content-Type: application/json');
        $this->checkAdmin();

        $result = $this->categoryModel->deleteCategory($id);

        if ($result === true) {
            http_response_code(200);
            echo json_encode(['message' => 'Xóa danh mục thành công']);
        } elseif (is_string($result)) {
            http_response_code(400);
            echo json_encode(['message' => $result]); // Error string returned from Model
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Lỗi máy chủ']);
        }
    }
}
?>
