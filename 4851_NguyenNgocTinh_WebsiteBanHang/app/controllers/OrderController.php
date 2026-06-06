<?php
require_once('app/config/database.php');
require_once('app/models/OrderModel.php');
require_once('app/helpers/SessionHelper.php');

class OrderController
{
    private $orderModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->orderModel = new OrderModel($this->db);
    }

    private function requireLogin()
    {
        if (!SessionHelper::isLoggedIn()) {
            $_SESSION['error_msg'] = "Vui lòng đăng nhập để truy cập chức năng này.";
            header('Location: ' . BASE_URL . '/account/login');
            exit();
        }

        // Populate user_id if missing (for users logged in before this update)
        if (!isset($_SESSION['user_id'])) {
            require_once('app/models/AccountModel.php');
            $accountModel = new AccountModel($this->db);
            $user = $accountModel->getAccountByUsername($_SESSION['username']);
            if ($user) {
                $_SESSION['user_id'] = $user->id;
            }
        }
    }

    public function index()
    {
        $this->requireLogin();

        if (SessionHelper::isAdmin()) {
            $filters = [
                'search' => $_GET['search'] ?? '',
                'start_date' => $_GET['start_date'] ?? '',
                'end_date' => $_GET['end_date'] ?? '',
                'status' => $_GET['status'] ?? 'Tất cả'
            ];
            $orders = $this->orderModel->searchOrders($filters);
            include 'app/views/order/admin_orders.php';
        } else {
            $accountId = $_SESSION['user_id'];
            $orders = $this->orderModel->getOrdersByAccountId($accountId);
            include 'app/views/order/user_orders.php';
        }
    }

    public function show($id)
    {
        $this->requireLogin();

        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            $_SESSION['error_msg'] = "Không tìm thấy đơn hàng.";
            header('Location: ' . BASE_URL . '/Order');
            exit();
        }

        // Access control: only admin or the order owner can view details
        if (!SessionHelper::isAdmin() && $order->account_id != $_SESSION['user_id']) {
            $_SESSION['error_msg'] = "Quyền truy cập bị từ chối.";
            header('Location: ' . BASE_URL . '/Order');
            exit();
        }

        $details = $this->orderModel->getOrderDetails($id);
        include 'app/views/order/detail.php';
    }

    public function updateStatus()
    {
        $this->requireLogin();

        if (!SessionHelper::isAdmin()) {
            $_SESSION['error_msg'] = "Quyền truy cập bị từ chối. Chỉ Admin mới được thực hiện chức năng này.";
            header('Location: ' . BASE_URL . '/Order');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($csrfToken)) {
                $_SESSION['error_msg'] = "Yêu cầu không hợp lệ (CSRF Token không chính xác).";
                header('Location: ' . BASE_URL . '/Order');
                exit();
            }

            $orderId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $status = trim($_POST['status'] ?? '');

            $validStatuses = ['Chờ xác nhận', 'Đang chuẩn bị hàng', 'Đang giao hàng', 'Đã giao hàng', 'Đã thu hồi'];
            if (!in_array($status, $validStatuses)) {
                $_SESSION['error_msg'] = "Trạng thái đơn hàng không hợp lệ.";
                header('Location: ' . BASE_URL . '/Order/show/' . $orderId);
                exit();
            }

            // Get current order to validate forward-only transition
            $currentOrder = $this->orderModel->getOrderById($orderId);
            if (!$currentOrder) {
                $_SESSION['error_msg'] = "Không tìm thấy đơn hàng.";
                header('Location: ' . BASE_URL . '/Order');
                exit();
            }

            if ($status === 'Đã thu hồi') {
                if ($currentOrder->status !== 'Đã duyệt hoàn trả') {
                    $_SESSION['error_msg'] = "Chỉ có thể thu hồi đơn hàng khi đã duyệt hoàn trả!";
                    header('Location: ' . BASE_URL . '/Order/show/' . $orderId);
                    exit();
                }
            } else {
                $coreStatuses = ['Chờ xác nhận', 'Đang chuẩn bị hàng', 'Đang giao hàng', 'Đã giao hàng'];
                $currentIdx = array_search($currentOrder->status, $coreStatuses);
                $newIdx = array_search($status, $coreStatuses);

                // Only allow advancing to next step, never going back
                if ($currentIdx === false || $newIdx !== $currentIdx + 1) {
                    $_SESSION['error_msg'] = "Chỉ được phép chuyển sang trạng thái tiếp theo, không thể quay về trạng thái trước!";
                    header('Location: ' . BASE_URL . '/Order/show/' . $orderId);
                    exit();
                }
            }

            $result = $this->orderModel->updateOrderStatus($orderId, $status);
            if ($result) {
                $_SESSION['success_msg'] = "Đã chuyển trạng thái đơn hàng sang: " . $status;
            } else {
                $_SESSION['error_msg'] = "Có lỗi xảy ra khi cập nhật trạng thái.";
            }

            header('Location: ' . BASE_URL . '/Order/show/' . $orderId);
            exit();
        } else {
            header('Location: ' . BASE_URL . '/Order');
            exit();
        }
    }

    public function cancel()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $order = $this->orderModel->getOrderById($orderId);

            if (!$order) {
                $_SESSION['error_msg'] = "Không tìm thấy đơn hàng.";
                header('Location: ' . BASE_URL . '/Order');
                exit();
            }

            if (!SessionHelper::isAdmin() && $order->account_id != $_SESSION['user_id']) {
                $_SESSION['error_msg'] = "Quyền truy cập bị từ chối.";
                header('Location: ' . BASE_URL . '/Order');
                exit();
            }

            if ($order->status !== 'Chờ xác nhận') {
                $_SESSION['error_msg'] = "Đơn hàng đã được xử lý, không thể hủy.";
                header('Location: ' . BASE_URL . '/Order/show/' . $orderId);
                exit();
            }

            $result = $this->orderModel->updateOrderStatus($orderId, 'Đã hủy');
            if ($result) {
                $_SESSION['success_msg'] = "Hủy đơn hàng thành công.";
            } else {
                $_SESSION['error_msg'] = "Có lỗi xảy ra khi hủy đơn hàng.";
            }
            
            // Redirect back to detail or list depending on where they clicked it
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referer, '/Order/show/') !== false) {
                header('Location: ' . BASE_URL . '/Order/show/' . $orderId);
            } else {
                header('Location: ' . BASE_URL . '/Order');
            }
            exit();
        }
        
        header('Location: ' . BASE_URL . '/Order');
        exit();
    }

    public function completeOrder()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $orderId = (int)$_POST['id'];
            $order = $this->orderModel->getOrderById($orderId);

            if ($order && $order->account_id === $_SESSION['user_id'] && $order->status === 'Đã giao hàng') {
                if ($this->orderModel->updateOrderStatus($orderId, 'Hoàn thành')) {
                    $_SESSION['success_msg'] = "Xác nhận nhận hàng thành công!";
                } else {
                    $_SESSION['error_msg'] = "Có lỗi xảy ra, vui lòng thử lại.";
                }
            } else {
                $_SESSION['error_msg'] = "Không thể xác nhận đơn hàng này.";
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/Order';
        header('Location: ' . $referer);
        exit();
    }

    public function returnOrder()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $orderId = (int)$_POST['id'];
            $reason = isset($_POST['return_reason']) ? trim($_POST['return_reason']) : '';
            $productIds = isset($_POST['return_products']) ? $_POST['return_products'] : [];
            
            $order = $this->orderModel->getOrderById($orderId);

            if ($order && $order->account_id === $_SESSION['user_id'] && $order->status === 'Đã giao hàng') {
                if (empty($productIds) || empty($reason)) {
                    $_SESSION['error_msg'] = "Vui lòng chọn sản phẩm và nhập lý do hoàn trả.";
                } else {
                    $productsJson = json_encode($productIds);
                    if ($this->orderModel->updateReturnRequest($orderId, $productsJson, $reason)) {
                        $_SESSION['success_msg'] = "Gửi yêu cầu hoàn trả thành công!";
                    } else {
                        $_SESSION['error_msg'] = "Có lỗi xảy ra, vui lòng thử lại.";
                    }
                }
            } else {
                $_SESSION['error_msg'] = "Không thể hoàn trả đơn hàng này.";
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/Order';
        header('Location: ' . $referer);
        exit();
    }

    public function processReturn()
    {
        $this->requireLogin();

        if (!SessionHelper::isAdmin()) {
            $_SESSION['error_msg'] = "Quyền truy cập bị từ chối.";
            header('Location: ' . BASE_URL . '/Order');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $orderId = (int)$_POST['id'];
            $action = $_POST['action'] ?? '';
            $adminReply = trim($_POST['admin_reply'] ?? '');

            $order = $this->orderModel->getOrderById($orderId);

            if ($order && $order->status === 'Yêu cầu hoàn trả') {
                if ($action === 'approve') {
                    $status = 'Đã duyệt hoàn trả';
                    $msg = "Đã duyệt yêu cầu hoàn trả.";
                } elseif ($action === 'reject') {
                    $status = 'Từ chối hoàn trả';
                    $msg = "Đã từ chối yêu cầu hoàn trả.";
                } else {
                    $_SESSION['error_msg'] = "Hành động không hợp lệ.";
                    header('Location: ' . BASE_URL . '/Order/show/' . $orderId);
                    exit();
                }

                if ($this->orderModel->processReturnRequest($orderId, $status, $adminReply)) {
                    $_SESSION['success_msg'] = $msg;
                } else {
                    $_SESSION['error_msg'] = "Có lỗi xảy ra, vui lòng thử lại.";
                }
            } else {
                $_SESSION['error_msg'] = "Không thể xử lý yêu cầu hoàn trả này.";
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/Order';
        header('Location: ' . $referer);
        exit();
    }

    public function revenue()
    {
        $this->requireLogin();

        if (!SessionHelper::isAdmin()) {
            $_SESSION['error_msg'] = "Quyền truy cập bị từ chối. Chỉ Admin mới được thực hiện chức năng này.";
            header('Location: ' . BASE_URL . '/Order');
            exit();
        }

        $revenues = $this->orderModel->getRevenueByDate();
        
        $totalRevenue = 0;
        $totalCompletedOrders = 0;
        foreach ($revenues as $r) {
            $totalRevenue += $r->daily_revenue;
            $totalCompletedOrders += $r->total_orders;
        }

        include 'app/views/order/revenue.php';
    }
}
?>
