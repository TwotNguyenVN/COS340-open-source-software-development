<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/helpers/SessionHelper.php');

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    public function index()
    {
        $categories = $this->categoryModel->getCategories();

        // Định nghĩa các biến filter trống cho view form ban đầu
        $keyword = '';
        $selected_category_id = null;
        $min_price = null;
        $max_price = null;
        $sort_by = 'newest';

        $totalProducts = $this->productModel->getTotalProducts();
        $products = [];

        include 'app/views/product/list.php';
    }

    public function show($id)
    {
        $product = $this->productModel->getProductById($id);

        if ($product) {
            $isSold = isset($product->sales_count) && (int)$product->sales_count > 0;
            include 'app/views/product/show.php';
        } else {
            $_SESSION['error_msg'] = "Không tìm thấy sản phẩm.";
            header('Location: ' . BASE_URL . '/Product');
            exit();
        }
    }

    // Thêm sản phẩm — YÊU CẦU QUYỀN ADMIN
    public function add()
    {
        if (!SessionHelper::isAdmin()) {
            $_SESSION['error_msg'] = "Quyền truy cập bị từ chối. Chỉ Admin mới được thực hiện chức năng này.";
            header('Location: ' . BASE_URL . '/Product');
            exit();
        }
        include_once 'app/views/product/add.php';
    }

    // Sửa sản phẩm — YÊU CẦU QUYỀN ADMIN
    public function edit($id)
    {
        if (!SessionHelper::isAdmin()) {
            $_SESSION['error_msg'] = "Quyền truy cập bị từ chối. Chỉ Admin mới được thực hiện chức năng này.";
            header('Location: ' . BASE_URL . '/Product');
            exit();
        }

        $editId = $id;
        include 'app/views/product/edit.php';
    }

    private function uploadImage($file)
    {
        $target_dir = "public/uploads/";

        // Create directory if not exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = time() . "_" . basename($file["name"]);
        $target_file = $target_dir . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is image
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }

        // Limit file size (5MB)
        if ($file["size"] > 5 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn (Tối đa 5MB).");
        }

        // Allow certain formats
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($imageFileType, $allowed)) {
            throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG, GIF và WEBP.");
        }

        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }

        return $target_file;
    }

    public function addToCart($id)
    {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_GET['ajax']);

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm.']);
                exit();
            }
            $_SESSION['error_msg'] = "Không tìm thấy sản phẩm.";
            header('Location: ' . BASE_URL . '/Product');
            exit();
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $currentQtyInCart = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id]['quantity'] : 0;
        $newQty = $currentQtyInCart + 1;

        if ($newQty > $product->stock) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => "Không thể thêm sản phẩm. Số lượng trong kho chỉ còn " . $product->stock . " sản phẩm."]);
                exit();
            }
            $_SESSION['error_msg'] = "Không thể thêm sản phẩm. Số lượng trong kho chỉ còn " . $product->stock . " sản phẩm.";
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/Product')));
            exit();
        }

        $price = $product->sale_price !== null ? $product->sale_price : $product->price;

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product->name,
                'price' => $price,
                'quantity' => 1,
                'image' => $product->image
            ];
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                'cart_count' => $this->getCartCount()
            ]);
            exit();
        }

        $_SESSION['success_msg'] = "Đã thêm sản phẩm vào giỏ hàng!";
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/Product')));
        exit();
    }

    public function cart()
    {
        $categories = $this->categoryModel->getCategories();
        include 'app/views/product/cart.php';
    }

    public function clearCart()
    {
        unset($_SESSION['cart']);
        unset($_SESSION['coupon']);
        $_SESSION['success_msg'] = "Đã xóa toàn bộ giỏ hàng!";
        header('Location: ' . BASE_URL . '/Product/cart');
        exit();
    }

    public function updateCart()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $id => $quantity) {
                $product = $this->productModel->getProductById($id);
                if (!$product) continue;

                $quantity = (int)$quantity;
                if ($quantity <= 0) {
                    unset($_SESSION['cart'][$id]);
                } else {
                    if ($quantity > $product->stock) {
                        $quantity = $product->stock;
                        $_SESSION['warning_msg'] = "Một số sản phẩm đã được giới hạn số lượng theo kho hàng hiện tại.";
                    }
                    $_SESSION['cart'][$id]['quantity'] = $quantity;
                }
            }
            $_SESSION['success_msg'] = "Cập nhật giỏ hàng thành công!";
        }
        header('Location: ' . BASE_URL . '/Product/cart');
        exit();
    }

    public function removeFromCart($id)
    {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_GET['ajax']);

        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            $_SESSION['success_msg'] = "Đã xóa sản phẩm khỏi giỏ hàng!";
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            $grand_total = 0;
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $grand_total += $item['price'] * $item['quantity'];
                }
            }
            $discount_amount = $this->calculateDiscount($grand_total);
            $shipping_fee = $grand_total >= 50000000 ? 0 : 100000;
            $final_total = max(0, $grand_total - $discount_amount) + $shipping_fee;

            echo json_encode([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                'grand_total' => number_format($grand_total, 0, ',', '.'),
                'discount_amount' => number_format($discount_amount, 0, ',', '.'),
                'shipping_fee' => $shipping_fee > 0 ? number_format($shipping_fee, 0, ',', '.') . ' VND' : 'Miễn phí',
                'final_total' => number_format($final_total, 0, ',', '.'),
                'cart_count' => $this->getCartCount()
            ]);
            exit();
        }

        header('Location: ' . BASE_URL . '/Product/cart');
        exit();
    }

    public function updateCartAjax()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ.']);
            exit();
        }

        $id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm.']);
            exit();
        }

        $warning = false;
        $msg = '';

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$id]);
            $item_subtotal = 0;
        } else {
            if ($quantity > $product->stock) {
                $quantity = $product->stock;
                $warning = true;
                $msg = "Số lượng sản phẩm trong kho chỉ còn " . $product->stock . ".";
            }
            $_SESSION['cart'][$id]['quantity'] = $quantity;
            $price = $_SESSION['cart'][$id]['price'];
            $item_subtotal = $price * $quantity;
        }

        $grand_total = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $grand_total += $item['price'] * $item['quantity'];
            }
        }

        $discount_amount = $this->calculateDiscount($grand_total);
        $shipping_fee = $grand_total >= 50000000 ? 0 : 100000;
        $final_total = max(0, $grand_total - $discount_amount) + $shipping_fee;

        echo json_encode([
            'success' => true,
            'warning' => $warning,
            'message' => $msg,
            'quantity' => $quantity,
            'item_subtotal' => number_format($item_subtotal, 0, ',', '.'),
            'grand_total' => number_format($grand_total, 0, ',', '.'),
            'discount_amount' => number_format($discount_amount, 0, ',', '.'),
            'shipping_fee' => $shipping_fee > 0 ? number_format($shipping_fee, 0, ',', '.') . ' VND' : 'Miễn phí',
            'final_total' => number_format($final_total, 0, ',', '.'),
            'cart_count' => $this->getCartCount()
        ]);
        exit();
    }

    public function applyCouponAjax()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ.']);
            exit();
        }

        $code = isset($_POST['coupon_code']) ? strtoupper(trim($_POST['coupon_code'])) : '';
        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá.']);
            exit();
        }

        $valid_coupons = [
            'NTECH10' => ['type' => 'percentage', 'value' => 10, 'description' => 'Giảm 10% tổng hóa đơn'],
            'GIARE' => ['type' => 'flat', 'value' => 50000, 'description' => 'Giảm 50.000đ cho đơn hàng']
        ];

        if (!array_key_exists($code, $valid_coupons)) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.']);
            exit();
        }

        $coupon = $valid_coupons[$code];
        $_SESSION['coupon'] = [
            'code' => $code,
            'type' => $coupon['type'],
            'value' => $coupon['value'],
            'description' => $coupon['description']
        ];

        $grand_total = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $grand_total += $item['price'] * $item['quantity'];
            }
        }

        $discount_amount = $this->calculateDiscount($grand_total);
        $shipping_fee = $grand_total >= 50000000 ? 0 : 100000;
        $final_total = max(0, $grand_total - $discount_amount) + $shipping_fee;

        echo json_encode([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'coupon_code' => $code,
            'description' => $coupon['description'],
            'discount_amount' => number_format($discount_amount, 0, ',', '.'),
            'shipping_fee' => $shipping_fee > 0 ? number_format($shipping_fee, 0, ',', '.') . ' VND' : 'Miễn phí',
            'final_total' => number_format($final_total, 0, ',', '.')
        ]);
        exit();
    }

    public function removeCouponAjax()
    {
        header('Content-Type: application/json');
        unset($_SESSION['coupon']);

        $grand_total = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $grand_total += $item['price'] * $item['quantity'];
            }
        }

        $shipping_fee = $grand_total >= 50000000 ? 0 : 100000;
        $final_total = $grand_total + $shipping_fee;

        echo json_encode([
            'success' => true,
            'message' => 'Đã xóa mã giảm giá.',
            'grand_total' => number_format($grand_total, 0, ',', '.'),
            'shipping_fee' => $shipping_fee > 0 ? number_format($shipping_fee, 0, ',', '.') . ' VND' : 'Miễn phí',
            'final_total' => number_format($final_total, 0, ',', '.')
        ]);
        exit();
    }

    private function calculateDiscount($grand_total)
    {
        if (!isset($_SESSION['coupon'])) {
            return 0;
        }

        $coupon = $_SESSION['coupon'];
        $type = $coupon['type'];
        $value = $coupon['value'];

        if ($type === 'percentage') {
            return $grand_total * ($value / 100);
        } elseif ($type === 'flat') {
            return min($grand_total, $value);
        }
        return 0;
    }

    private function getCartCount()
    {
        if (!isset($_SESSION['cart'])) {
            return 0;
        }
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    public function checkout()
    {
        include 'app/views/product/checkout.php';
    }

    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if (empty($name) || empty($phone) || empty($address)) {
                $_SESSION['error_msg'] = "Vui lòng điền đầy đủ họ tên, số điện thoại và địa chỉ nhận hàng.";
                header('Location: ' . BASE_URL . '/Product/checkout');
                exit();
            }

            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                $_SESSION['error_msg'] = "Giỏ hàng của bạn đang trống.";
                header('Location: ' . BASE_URL . '/Product/cart');
                exit();
            }

            $this->db->beginTransaction();

            try {
                $grand_total = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $grand_total += $item['price'] * $item['quantity'];
                }

                $coupon_code = isset($_SESSION['coupon']) ? $_SESSION['coupon']['code'] : null;
                $discount_amount = $this->calculateDiscount($grand_total);
                $shipping_fee = $grand_total >= 50000000 ? 0 : 100000;
                $total_amount = max(0, $grand_total - $discount_amount) + $shipping_fee;

                $accountId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

                $query = "INSERT INTO orders (account_id, name, phone, address, coupon_code, discount_amount, total_amount) 
                          VALUES (:account_id, :name, :phone, :address, :coupon_code, :discount_amount, :total_amount)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':account_id', $accountId);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':coupon_code', $coupon_code);
                $stmt->bindParam(':discount_amount', $discount_amount);
                $stmt->bindParam(':total_amount', $total_amount);
                $stmt->execute();
                $order_id = $this->db->lastInsertId();

                $cart = $_SESSION['cart'];
                foreach ($cart as $product_id => $item) {
                    $query = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':order_id', $order_id);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':price', $item['price']);
                    $stmt->execute();

                    $deductQuery = "UPDATE product SET stock = stock - :quantity WHERE id = :id";
                    $deductStmt = $this->db->prepare($deductQuery);
                    $deductStmt->bindParam(':quantity', $item['quantity']);
                    $deductStmt->bindParam(':id', $product_id);
                    $deductStmt->execute();
                }

                $_SESSION['last_order_id'] = $order_id;
                $_SESSION['last_order_details'] = [
                    'name' => $name,
                    'phone' => $phone,
                    'address' => $address,
                    'coupon_code' => $coupon_code,
                    'discount_amount' => $discount_amount,
                    'total_amount' => $total_amount,
                    'items' => $cart
                ];

                unset($_SESSION['cart']);
                unset($_SESSION['coupon']);

                $this->db->commit();

                header('Location: ' . BASE_URL . '/Product/orderConfirmation');
                exit();
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['error_msg'] = "Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage();
                header('Location: ' . BASE_URL . '/Product/checkout');
                exit();
            }
        }
    }

    public function orderConfirmation()
    {
        include 'app/views/product/orderConfirmation.php';
    }
}
?>
