BÀI 6Bảo mật RESTful API với JWT

BÀI 6. Bảo mật RESTful API với JWT

Sau khi học xong bài này, sinh viên có thể nắm được:

 Khái niệm về JWT (JSON Web Token): Hiểu cơ bản về JWT, cách thức hoạt

động và ứng dụng của nó trong bảo mật API.

 Cài đặt thư viện JWT: Biết cách cài đặt và cấu hình thư viện JWT trong dự

án PHP.

 Tạo và xác thực JWT: Học cách tạo token JWT khi người dùng đăng nhập và

xác thực token này trong các yêu cầu API.

 Bảo vệ các endpoints API: Triển khai bảo vệ các endpoints quan trọng bằng

cách yêu cầu JWT hợp lệ cho mỗi yêu cầu.

 Thực hành bảo mật API: Hiểu và triển khai các biện pháp bảo mật nâng cao

cho RESTful API sử dụng JWT.

6.1 Cài đặt thư viện JWT

JSON Web Tokens (JWT) là một cách phổ biến để bảo mật API và xác thực người dùng.

Trong hướng dẫn này, chúng ta sẽ sử dụng PHP để bảo mật RESTful API bằng JWT.

Chúng ta sẽ sử dụng thư viện firebase/php-jwt để tạo và xác thực JWT.

Truy cập trang web: https://getcomposer.org/download/ để tiến hành tải bản cài đặt

Các bước cài đặt tiến hành cài mặc định.

BÀI 6 Bảo mật RESTful API với JWT 97

98

BÀI 6Bảo mật RESTful API với JWT

Vào terminal gõ composer để kiểm tra cài đặt thành công:

Cài đặt composer vào dự án:

Trong cửa sổ visual code studio của dự án chọn thẻ Terminal và click New Terminal:

Nhập câu lệnh sau: composer require firebase/php-jwt

BÀI 6 Bảo mật RESTful API với JWT 99

6.2 Tạo lớp xử lý JWT

Tạo một lớp JWTHandler để tạo và xác thực JWT.

Trong thư mục app tạo thêm thư mục utils để chứa file JWTHandler.php

app/utils/JWTHandler.php

<?php

require_once 'vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class JWTHandler
{
    private $secret_key;

    public function __construct()
    {
        $this->secret_key = "HUTECH"; // Thay thế bằng khóa bí mật của bạn
    }

    // Tạo JWT
    public function encode($data)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;  // jwt valid for 1 hour from the issued
time

100  BÀI 6Bảo mật RESTful API với JWT

        $payload = array(
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => $data
        );

        return JWT::encode($payload, $this->secret_key, 'HS256');
    }

    // Giải mã JWT
    public function decode($jwt)
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->secret_key, 'HS256'));
            return (array) $decoded->data;
        } catch (Exception $e) {
            return null;
        }
    }
}
?>

6.3 Cập nhật API để sử dụng JWT

Cập nhật ProductApiController để bảo vệ các endpoint bằng JWT.

<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');

require_once('app/utils/JWTHandler.php'); //
class ProductApiController
{
    private $productModel;
    private $db;
    private $jwtHandler; //
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);

        $this->jwtHandler = new JWTHandler(); //
    }

    private function authenticate()
    {
        $headers = apache_request_headers();

BÀI 6 Bảo mật RESTful API với JWT

10
1

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $arr = explode(" ", $authHeader);
            $jwt = $arr[1] ?? null;
            if ($jwt) {
                $decoded = $this->jwtHandler->decode($jwt);
                return $decoded ? true : false;
            }
        }
        return false;
    }

    // Lấy danh sách sản phẩm
    public function index()
    {
        if ($this->authenticate()) {
            header('Content-Type: application/json');
            $products = $this->productModel->getProducts();
            echo json_encode($products);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
        }
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

102  BÀI 6Bảo mật RESTful API với JWT

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;

        $result = $this->productModel->addProduct($name, $description, $price,
$category_id, null);

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

BÀI 6 Bảo mật RESTful API với JWT

10
3

        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product deletion failed']);
        }
    }
}
?>

Cập nhật AccountController.php

<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
require_once('app/utils/JWTHandler.php');
class AccountController {
    private $accountModel;
    private $db;

    private $jwtHandler;
    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    function register(){
        include_once 'app/views/account/register.php';
    }
    public function login() {
        include_once 'app/views/account/login.php';
    }

    function save(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $fullName = $_POST['fullname'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmpassword'] ?? '';

            $errors =[];
            if(empty($username)){
                $errors['username'] = "Vui long nhap userName!";
            }
            if(empty($fullName)){
                $errors['fullname'] = "Vui long nhap fullName!";

104  BÀI 6Bảo mật RESTful API với JWT

            }
            if(empty($password)){
                $errors['password'] = "Vui long nhap password!";
            }
            if($password != $confirmPassword){
                $errors['confirmPass'] = "Mat khau va xac nhan chua dung";
            }
            //kiểm tra username đã được đăng ký chưa?
            $account = $this->accountModel->getAccountByUsername($username);

            if($account){
                $errors['account'] = "Tai khoan nay da co nguoi dang ky!";
            }

            if(count($errors) > 0){
                include_once 'app/views/account/register.php';
            }else{
                $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                $result = $this->accountModel->save($username, $fullName, $password);

                if($result){
                    header('Location: /webbanhang/account/login');
                }
            }
        }

    }
    function logout(){

        unset($_SESSION['username']);
        unset($_SESSION['role']);

        header('Location: /webbanhang/product');
    }
    public function checkLogin()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->accountModel->getAccountByUserName($username);
        if ($user && password_verify($password, $user->password)) {
            $token = $this->jwtHandler->encode(['id' => $user->id, 'username' =>
$user->username]);

BÀI 6 Bảo mật RESTful API với JWT

10
5

            echo json_encode(['token' => $token]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
        }
    }
}

6.4   Cập nhật các trang hiển thị

Cập nhật views  app/views/account/login.php

<?php include 'app/views/shares/header.php'; ?>

<section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-dark text-white" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center">

          <form id="login-form">
            <div class="mb-md-5 mt-md-4 pb-5">

              <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
              <p class="text-white-50 mb-5">Please enter your login and password!</p>

              <div class="form-outline form-white mb-4">
                <input type="text" name="username" class="form-control form-control-

lg" />
<label class="form-label" for="typeEmailX">UserName</label>

</div>

              <div class="form-outline form-white mb-4">
                <input type="password" name="password" class="form-control form-

control-lg" />
<label class="form-label" for="typePasswordX">Password</label>

</div>

              <p class="small mb-5 pb-lg-2"><a class="text-white-50" href="#!">Forgot

password?</a></p>

              <button class="btn btn-outline-light btn-lg px-5"

type="submit">Login</button>

106 BÀI 6Bảo mật RESTful API với JWT

              <div class="d-flex justify-content-center text-center mt-4 pt-1">
                <a href="#!" class="text-white"><i class="fab fa-facebook-f fa-

lg"></i></a>
<a href="#!" class="text-white"><i class="fab fa-twitter fa-lg mx-4
px-2"></i></a>
<a href="#!" class="text-white"><i class="fab fa-google fa-
lg"></i></a>

</div>

            </div>

            <div>
              <p class="mb-0">Don't have an account? <a href="#!" class="text-white-50

fw-bold">Sign Up</a>

</p>
</div>
</form>

          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php include 'app/views/shares/footer.php'; ?>
<script>
document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const jsonData = {};
    formData.forEach((value, key) => {
        jsonData[key] = value;
    });

    fetch('/webbanhang/account/checkLogin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(jsonData)
    })
    .then(response => response.json())
    .then(data => {

        if (data.token) {

BÀI 6 Bảo mật RESTful API với JWT

10
7

            localStorage.setItem('jwtToken', data.token);
            location.href = '/webbanhang/Product';
        } else {
            alert('Đăng nhập thất bại');
        }
    });
});
</script>

Cập nhật file header.php

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link
href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
rel="stylesheet">
    <style>
    .product-image {
        max-width: 100px;
        height: auto;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Quản lý sản phẩm</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-
target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle
navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/webbanhang/Product/">Danh sách sản
phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/webbanhang/Product/add">Thêm sản
phẩm</a>

108 BÀI 6Bảo mật RESTful API với JWT

                </li>
                <li class="nav-item" id="nav-login">
                    <a class="nav-link" href="/webbanhang/account/login">Login</a>
                </li>
                <li class="nav-item" id="nav-logout" style="display: none;">
                    <a class="nav-link" href="#" onclick="logout()">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <script>
    function logout() {
        localStorage.removeItem('jwtToken');
        location.href = '/webbanhang/account/login';
    }

    document.addEventListener("DOMContentLoaded", function() {
        const token = localStorage.getItem('jwtToken');
        if (token) {
            document.getElementById('nav-login').style.display = 'none';
            document.getElementById('nav-logout').style.display = 'block';
        } else {
            document.getElementById('nav-login').style.display = 'block';
            document.getElementById('nav-logout').style.display = 'none';
        }
    });
    </script>
    <div class="container mt-4">

Cập nhật trang list.php

<?php include 'app/views/shares/header.php'; ?>

<h1>Danh sách sản phẩm</h1>
<a href="/webbanhang/Product/add" class="btn btn-success mb-2">Thêm sản phẩm mới</a>
<ul class="list-group" id="product-list">
    <!-- Danh sách sản phẩm sẽ được tải từ API và hiển thị tại đây -->
</ul>

<?php include 'app/views/shares/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem('jwtToken');
    if (!token) {
        alert('Vui lòng đăng nhập');
        location.href = '/webbanhang/account/login'; // Điều hướng đến trang đăng nhập

BÀI 6 Bảo mật RESTful API với JWT

10
9

        return;
    }
    fetch('/webbanhang/api/product', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    })
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

110 BÀI 6Bảo mật RESTful API với JWT

BÀI 6 Bảo mật RESTful API với JWT

11
1

6.5 Tiến hành khởi chạy dự án và thực nghiệm

Yêu cầu đăng nhập mới xem được trang danh sách sản phẩm:

Kiểm thử trong Postman:

API đã bị chặn khi không đăng nhập tài khoản

112

TÀI LIỆU THAM KHẢO

TÀI LIỆU THAM KHẢO

1.  Clean Code: A Handbook of Agile Software Craftsmanship, Robert C. Martin, 2008

2.  Eloquent JavaScript: A Modern Introduction to Programming, Marijn Haverbeke,

2018 (3rd edition)

3.  You Don't Know JS (series), Kyle Simpson, 2015

4.  MDN Web Docs (https://developer.mozilla.org)

5.  Stack Overflow (https://stackoverflow.com)

6.  GitHub (https://github.com)

7.  W3Schools (https://www.w3schools.com)

8.  Smashing Magazine (https://www.smashingmagazine.com)
