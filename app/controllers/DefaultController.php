<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';

class DefaultController
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
        // Lấy 4 sản phẩm nổi bật
        $featuredProducts = $this->productModel->getFeaturedProducts(4);

        // Lấy 4 sản phẩm mới nhất
        $newArrivals = $this->productModel->getNewArrivals(4);

        // Lấy 1 sản phẩm đang giảm giá
        $discountedProducts = $this->productModel->getDiscountedProducts(1);

        // Lấy danh sách danh mục
        $categories = $this->categoryModel->getCategories();

        include 'app/views/home/index.php';
    }
}
?>
