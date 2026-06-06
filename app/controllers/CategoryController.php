<?php
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');
require_once('app/helpers/SessionHelper.php');

class CategoryController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);

        // Bảo vệ toàn bộ CategoryController — chỉ Admin mới có quyền truy cập
        if (!SessionHelper::isAdmin()) {
            $_SESSION['error_msg'] = "Quyền truy cập bị từ chối. Chỉ Admin mới được quản lý danh mục.";
            header('Location: ' . BASE_URL . '/Product');
            exit();
        }
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $categories = $this->categoryModel->getCategories();
        include 'app/views/category/list.php';
    }

    public function add()
    {
        include 'app/views/category/add.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($csrfToken)) {
                $_SESSION['error_msg'] = "Yêu cầu không hợp lệ (CSRF Token không chính xác).";
                header('Location: ' . BASE_URL . '/Category/list');
                exit();
            }
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            $result = $this->categoryModel->addCategory($name, $description);

            if (is_array($result)) {
                $errors = $result;
                include 'app/views/category/add.php';
            } else {
                $_SESSION['success_msg'] = "Đã thêm danh mục thành công!";
                header('Location: ' . BASE_URL . '/Category/list');
                exit();
            }
        }
    }

    public function edit($id = null)
    {
        if (empty($id)) {
            $_SESSION['error_msg'] = "ID danh mục không hợp lệ.";
            header('Location: ' . BASE_URL . '/Category/list');
            exit();
        }

        $category = $this->categoryModel->getCategoryById($id);

        if ($category && is_object($category)) {
            include 'app/views/category/edit.php';
        } else {
            $_SESSION['error_msg'] = "Không tìm thấy danh mục.";
            header('Location: ' . BASE_URL . '/Category/list');
            exit();
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($csrfToken)) {
                $_SESSION['error_msg'] = "Yêu cầu không hợp lệ (CSRF Token không chính xác).";
                header('Location: ' . BASE_URL . '/Category/list');
                exit();
            }
            $id = $_POST['id'] ?? '';
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            $result = $this->categoryModel->updateCategory($id, $name, $description);

            if (is_array($result)) {
                $errors = $result;
                // Reconstruct the category object from submitted form data
                // to preserve user input and guarantee a valid object structure
                $category = (object)[
                    'id' => $id,
                    'name' => $name,
                    'description' => $description
                ];
                include 'app/views/category/edit.php';
            } else {
                $_SESSION['success_msg'] = "Cập nhật danh mục thành công!";
                header('Location: ' . BASE_URL . '/Category/list');
                exit();
            }
        }
    }

    public function delete($id)
    {
        $csrfToken = $_GET['csrf_token'] ?? '';
        if (!SessionHelper::verifyCSRFToken($csrfToken)) {
            $_SESSION['error_msg'] = "Yêu cầu không hợp lệ (CSRF Token không chính xác).";
            header('Location: ' . BASE_URL . '/Category/list');
            exit();
        }

        $result = $this->categoryModel->deleteCategory($id);

        if ($result === true) {
            $_SESSION['success_msg'] = "Đã xóa danh mục thành công!";
        } else {
            $_SESSION['error_msg'] = is_string($result) ? $result : "Đã xảy ra lỗi khi xóa danh mục.";
        }

        header('Location: ' . BASE_URL . '/Category/list');
        exit();
    }
}
?>
