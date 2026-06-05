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
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $fullName = trim($_POST['fullname'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmpassword'] ?? '';

            $errors =[];
            if(empty($username)){
                $errors['username'] = "Vui lòng nhập userName!";
            }
            if(empty($email)){
                $errors['email'] = "Vui lòng nhập email!";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Email không hợp lệ!";
            }
            if(empty($fullName)){
                $errors['fullname'] = "Vui lòng nhập fullName!";
            }
            if(empty($password)){
                $errors['password'] = "Vui lòng nhập password!";
            }
            if($password != $confirmPassword){
                $errors['confirmPass'] = "Mật khẩu và xác nhận chưa đúng";
            }
            
            // Kiểm tra username đã được đăng ký chưa (cấm trùng username HOẶC email)
            $account = $this->accountModel->getAccountByUsernameOrEmail($username);
            if($account){
                $errors['account'] = "Tên đăng nhập này đã tồn tại hoặc trùng với email của người khác!";
            }
            $accountEmail = $this->accountModel->getAccountByUsernameOrEmail($email);
            if($accountEmail){
                $errors['email'] = "Email này đã được sử dụng!";
            }

            if(count($errors) > 0){
                include_once 'app/views/account/register.php';
            }else{
                $password_hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $result = $this->accountModel->save($username, $email, $fullName, $password_hashed);
                if($result){
                    $_SESSION['success_msg'] = "Đăng ký tài khoản thành công! Vui lòng đăng nhập.";
                    header('Location: ' . BASE_URL . '/account/login');
                    exit();
                } else {
                    $errors['account'] = "Có lỗi xảy ra khi đăng ký!";
                    include_once 'app/views/account/register.php';
                }
            }
        }
    }

    function logout(){
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['user_role']);
        unset($_SESSION['user_fullname']);
        unset($_SESSION['cart']);
        unset($_SESSION['coupon']);
        $_SESSION['success_msg'] = "Bạn đã đăng xuất thành công!";
        header('Location: ' . BASE_URL . '/');
        exit();
    }

    // Traditional form POST login fallback (Bài 4) — for non-AJAX requests
    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/account/login');
            exit();
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->accountModel->getAccountByUsernameOrEmail($username);
        if ($user && password_verify($password, $user->password)) {
            // Clean up session cart to isolate user carts
            unset($_SESSION['cart']);
            unset($_SESSION['coupon']);

            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['user_role'] = $user->role;
            $_SESSION['user_fullname'] = $user->fullname;
            $_SESSION['success_msg'] = "Đăng nhập thành công! Chào mừng " . htmlspecialchars($user->fullname, ENT_QUOTES, 'UTF-8');
            header('Location: ' . BASE_URL . '/');
            exit();
        } else {
            $_SESSION['error_msg'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
            header('Location: ' . BASE_URL . '/account/login');
            exit();
        }
    }

    // JWT-based API login (Bài 6) — returns JSON token
    public function checkLogin()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        $user = $this->accountModel->getAccountByUsernameOrEmail($username);
        if ($user && password_verify($password, $user->password)) {
            // Clean up session cart to isolate user carts
            unset($_SESSION['cart']);
            unset($_SESSION['coupon']);

            // Also set session for traditional web pages
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['user_role'] = $user->role;
            $_SESSION['user_fullname'] = $user->fullname;

            $token = $this->jwtHandler->encode([
                'id' => $user->id, 
                'username' => $user->username,
                'role' => $user->role
            ]);

            echo json_encode(['token' => $token]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
        }
    }

    public function forgotPassword() {
        include_once 'app/views/account/forgot_password.php';
    }

    public function apiSendOtp() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $email = trim($data['email'] ?? '');
        
        $user = $this->accountModel->getAccountByUsernameOrEmail($email);
        if (!$user || $user->email !== $email) {
            echo json_encode(['success' => false, 'message' => 'Email không tồn tại trong hệ thống.']);
            return;
        }
        
        $otp = rand(100000, 999999);
        $this->accountModel->createPasswordReset($email, $otp);
        
        // Đặt lại số lần nhập sai OTP
        $_SESSION['otp_attempts'] = 0;
        
        require_once 'app/utils/MailHelper.php';
        MailHelper::sendOtp($email, $otp);
        
        echo json_encode(['success' => true]);
    }

    public function apiVerifyOtp() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $email = trim($data['email'] ?? '');
        $otp = trim($data['otp'] ?? '');
        
        if (!isset($_SESSION['otp_attempts'])) {
            $_SESSION['otp_attempts'] = 0;
        }
        
        if ($_SESSION['otp_attempts'] >= 5) {
            echo json_encode(['success' => false, 'message' => 'Bạn đã nhập sai quá 5 lần. Vui lòng gửi lại mã OTP mới.']);
            return;
        }
        
        $record = $this->accountModel->verifyOtp($email, $otp);
        if ($record) {
            // Reset attempts on success
            $_SESSION['otp_attempts'] = 0;
            echo json_encode(['success' => true]);
        } else {
            $_SESSION['otp_attempts']++;
            echo json_encode(['success' => false, 'message' => 'Mã OTP không đúng hoặc đã hết hạn.']);
        }
    }

    public function apiResetPassword() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $email = trim($data['email'] ?? '');
        $otp = trim($data['otp'] ?? '');
        $password = $data['password'] ?? '';
        
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự.']);
            return;
        }
        
        $record = $this->accountModel->verifyOtp($email, $otp);
        if (!$record) {
            echo json_encode(['success' => false, 'message' => 'Xác thực thất bại hoặc mã OTP đã hết hạn.']);
            return;
        }
        
        $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->accountModel->updatePassword($email, $hashed);
        echo json_encode(['success' => true]);
    }
}
?>
