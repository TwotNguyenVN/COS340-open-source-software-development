BÀI 5 XÂY DỰNG RESTful API  79

BÀI 5.  XÂY DỰNG RESTful API

Sau khi học xong bài này, sinh viên có thể nắm được:

• Khái niệm về RESTful API: Hiểu các nguyên tắc cơ bản của REST và cách thức

hoạt động của RESTful API.

• Cấu hình API trong PHP: Biết cách cấu hình và triển khai API sử dụng PHP.

• Xây dựng các phương thức CRUD cho API: Tạo các phương thức GET, POST,

PUT, DELETE để quản lý tài nguyên.

• Tạo và quản lý endpoints: Thiết kế và triển khai các endpoints API cho các chức

năng khác nhau.

• Kiểm thử API: Sử dụng Postman hoặc công cụ tương tự để kiểm thử các API đã

triển khai.

5.1  Cập nhật ProductModel

Bỏ thuộc tính image của product

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
        $query = "SELECT p.id, p.name, p.description, p.price, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function getProductById($id)

80

BÀI 5XÂY DỰNG RESTful API

    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function addProduct($name, $description, $price, $category_id)
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
        if (count($errors) > 0) {
            return $errors;
        }

        $query = "INSERT INTO " . $this->table_name . " (name, description, price,
category_id) VALUES (:name, :description, :price, :category_id)";
        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

BÀI 5 XÂY DỰNG RESTful API  81

    public function updateProduct($id, $name, $description, $price, $category_id)
    {
        $query = "UPDATE " . $this->table_name . " SET name=:name,
description=:description, price=:price, category_id=:category_id WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>

5.2  Xây dựng các Controller tương ứng

Xây dựng file ProductApiController.php

<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');

82

BÀI 5XÂY DỰNG RESTful API

class ProductApiController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    // Lấy danh sách sản phẩm
    public function index()
    {
        header('Content-Type: application/json');
        $products = $this->productModel->getProducts();
        echo json_encode($products);
    }

    // Lấy thông tin sản phẩm theo ID
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

    // Thêm sản phẩm mới
    public function store()
    {

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;

        $result = $this->productModel->addProduct($name, $description, $price,
$category_id, null);

BÀI 5 XÂY DỰNG RESTful API  83

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } else {
            http_response_code(201);
            echo json_encode(['message' => 'Product created successfully']);
        }
    }

    // Cập nhật sản phẩm theo ID
    public function update($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;

        $result = $this->productModel->updateProduct($id, $name, $description, $price,
$category_id, null);

        if ($result) {
            echo json_encode(['message' => 'Product updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product update failed']);
        }
    }

    // Xóa sản phẩm theo ID
    public function destroy($id)
    {
        header('Content-Type: application/json');
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

84

BÀI 5XÂY DỰNG RESTful API

Xây dựng file CategoryApiController.php
<?php
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');

class CategoryApiController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // Lấy danh sách danh mục
    public function index()
    {
        header('Content-Type: application/json');
        $categories = $this->categoryModel->getCategories();
        echo json_encode($categories);
    }
}
?>

5.3  Cấu hình router để định tuyến các yêu cầu

API

<?php
session_start();
require_once 'app/models/ProductModel.php';
require_once 'app/helpers/SessionHelper.php';

require_once 'app/controllers/ProductApiController.php';
require_once 'app/controllers/CategoryApiController.php';
// Start session

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Kiểm tra phần đầu tiên của URL để xác định controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' :
'DefaultController';

BÀI 5 XÂY DỰNG RESTful API  85

// Kiểm tra phần thứ hai của URL để xác định action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// Định tuyến các yêu cầu API
if ($controllerName === 'ApiController' && isset($url[1])) {
    $apiControllerName = ucfirst($url[1]) . 'ApiController';
    if (file_exists('app/controllers/' . $apiControllerName . '.php')) {
        require_once 'app/controllers/' . $apiControllerName . '.php';
        $controller = new $apiControllerName();

        $method = $_SERVER['REQUEST_METHOD'];
        $id = $url[2] ?? null;

        switch ($method) {
            case 'GET':
                if ($id) {
                    $action = 'show';
                } else {
                    $action = 'index';
                }
                break;
            case 'POST':
                $action = 'store';
                break;
            case 'PUT':
                if ($id) {
                    $action = 'update';
                }
                break;
            case 'DELETE':
                if ($id) {
                    $action = 'destroy';
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['message' => 'Method Not Allowed']);
                exit;
        }

        if (method_exists($controller, $action)) {
            if ($id) {
                call_user_func_array([$controller, $action], [$id]);

86

BÀI 5XÂY DỰNG RESTful API

            } else {
                call_user_func_array([$controller, $action], []);
            }
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Action not found']);
        }
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Controller not found']);
        exit;
    }
}

// Tạo đối tượng controller tương ứng cho các yêu cầu không phải API
if (file_exists('app/controllers/' . $controllerName . '.php')) {
    require_once 'app/controllers/' . $controllerName . '.php';
    $controller = new $controllerName();
} else {
    die('Controller not found');
}

// Kiểm tra và gọi action
if (method_exists($controller, $action)) {
    call_user_func_array([$controller, $action], array_slice($url, 2));
} else {
    die('Action not found');
}
?>

5.4  Cập nhật views để quản lý sản phẩm

app/views/product/list.php

<?php include 'app/views/shares/header.php'; ?>

<h1>Danh sách sản phẩm</h1>
<a href="/webbanhang/Product/add" class="btn btn-success mb-2">Thêm sản phẩm mới</a>
<ul class="list-group" id="product-list">
    <!-- Danh sách sản phẩm sẽ được tải từ API và hiển thị tại đây -->
</ul>

<?php include 'app/views/shares/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('/webbanhang/api/product')

BÀI 5 XÂY DỰNG RESTful API  87

        .then(response => response.json())
        .then(data => {
            const productList = document.getElementById('product-list');
            data.forEach(product => {
                const productItem = document.createElement('li');
                productItem.className = 'list-group-item';
                productItem.innerHTML = `
                    <h2><a
href="/webbanhang/Product/show/${product.id}">${product.name}</a></h2>
                    <p>${product.description}</p>
                    <p>Giá: ${product.price} VND</p>
                    <p>Danh mục: ${product.category_name}</p>
                    <a href="/webbanhang/Product/edit/${product.id}" class="btn btn-
warning">Sửa</a>
                    <button class="btn btn-danger"
onclick="deleteProduct(${product.id})">Xóa</button>
                `;
                productList.appendChild(productItem);
            });
        });
});

function deleteProduct(id) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        fetch(`/webbanhang/api/product/${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'Product deleted successfully') {
                location.reload();
            } else {
                alert('Xóa sản phẩm thất bại');
            }
        });
    }
}
</script>

app/views/product/add.php

<?php include 'app/views/shares/header.php'; ?>

<h1>Thêm sản phẩm mới</h1>
<form id="add-product-form">
    <div class="form-group">
        <label for="name">Tên sản phẩm:</label>

88

BÀI 5XÂY DỰNG RESTful API

        <input type="text" id="name" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="description">Mô tả:</label>
        <textarea id="description" name="description" class="form-control"
required></textarea>
    </div>
    <div class="form-group">
        <label for="price">Giá:</label>
        <input type="number" id="price" name="price" class="form-control" step="0.01"
required>
    </div>
    <div class="form-group">
        <label for="category_id">Danh mục:</label>
        <select id="category_id" name="category_id" class="form-control" required>
            <!-- Các danh mục sẽ được tải từ API và hiển thị tại đây -->
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
</form>

<a href="/webbanhang/Product/list" class="btn btn-secondary mt-2">Quay lại danh sách
sản phẩm</a>

<?php include 'app/views/shares/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('/webbanhang/api/category')
        .then(response => response.json())
        .then(data => {
            const categorySelect = document.getElementById('category_id');
            data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        });

    document.getElementById('add-product-form').addEventListener('submit',
function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const jsonData = {};
        formData.forEach((value, key) => {

            jsonData[key] = value;
        });

BÀI 5 XÂY DỰNG RESTful API  89

        fetch('/webbanhang/api/product', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(text => {
        console.log('Raw response:', text); // Log the raw response text
        try {
            const data = text;
            if (data.message === 'Product created successfully') {
                location.href = '/webbanhang/Product';
            } else {
                alert('Thêm sản phẩm thất bại');
            }
        } catch (error) {
            console.error('Error parsing JSON:', error);
            alert('Lỗi: Không thể phân tích JSON từ phản hồi của máy chủ.');
        }
    });
    });
});
</script>

app/views/product/edit.php

<?php include 'app/views/shares/header.php'; ?>

<h1>Sửa sản phẩm</h1>
<form id="edit-product-form">
    <input type="hidden" id="id" name="id">
    <div class="form-group">
        <label for="name">Tên sản phẩm:</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="description">Mô tả:</label>
        <textarea id="description" name="description" class="form-control"
required></textarea>
    </div>

90

BÀI 5XÂY DỰNG RESTful API

    <div class="form-group">
        <label for="price">Giá:</label>
        <input type="number" id="price" name="price" class="form-control" step="0.01"
required>
    </div>
    <div class="form-group">
        <label for="category_id">Danh mục:</label>
        <select id="category_id" name="category_id" class="form-control" required>
            <!-- Các danh mục sẽ được tải từ API và hiển thị tại đây -->
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
</form>

<a href="/webbanhang/Product/list" class="btn btn-secondary mt-2">Quay lại danh sách
sản phẩm</a>

<?php include 'app/views/shares/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // const urlParams = new URLSearchParams(window.location.search);
    const productId = <?= $editId ?>;

    fetch(`/webbanhang/api/product/${productId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('id').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description;
            document.getElementById('price').value = data.price;
            document.getElementById('category_id').value = data.category_id;
        });

    fetch('/webbanhang/api/category')
        .then(response => response.json())
        .then(data => {
            const categorySelect = document.getElementById('category_id');
            data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        });

    document.getElementById('edit-product-form').addEventListener('submit',
function(event) {
        event.preventDefault();

BÀI 5 XÂY DỰNG RESTful API  91

        const formData = new FormData(this);
        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        fetch(`/webbanhang/api/product/${jsonData.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'Product updated successfully') {
                location.href = '/webbanhang/Product';
            } else {
                alert('Cập nhật sản phẩm thất bại');
            }
        });
    });
});
</script>

92

BÀI 5XÂY DỰNG RESTful API

5.5  Tiến hành khởi chạy dự án và sử dụng

postman để kiểm thử API

Phương thức GET

Phương thức POST:

BÀI 5 XÂY DỰNG RESTful API  93

94

BÀI 5XÂY DỰNG RESTful API

Phương thức PUT:

Phương thức DELETE:

BÀI 5 XÂY DỰNG RESTful API  95

5.6  Yêu cầu bổ sung

Xây dựng trang front-end cho API bằng jquery.

96