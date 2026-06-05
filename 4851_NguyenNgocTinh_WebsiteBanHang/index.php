<?php
// Start session for Shopping Cart, Flash messages, etc.
session_start();
require_once 'app/helpers/SessionHelper.php';

// Dynamic base URL detection for Laragon/XAMPP subfolders (Windows case-insensitive and slash robust)
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
$base_dir = str_ireplace($doc_root, '', $script_dir);
$base_dir = rtrim($base_dir, '/');
define('BASE_URL', $base_dir);

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Determine controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'DefaultController';

// Determine action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// Định tuyến các yêu cầu API
if ($controllerName === 'ApiController' && isset($url[1])) {
    $apiControllerName = ucfirst($url[1]) . 'ApiController';
    if (file_exists('app/controllers/' . $apiControllerName . '.php')) {
        require_once 'app/controllers/' . $apiControllerName . '.php';
        $controller = new $apiControllerName();

        $method = $_SERVER['REQUEST_METHOD'];
        
        // Handle method override for PUT/DELETE via POST
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
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

// Check if controller file exists
if (!file_exists('app/controllers/' . $controllerName . '.php')) {
    header("HTTP/1.0 404 Not Found");
    die('Controller not found: ' . $controllerName);
}

require_once 'app/controllers/' . $controllerName . '.php';

$controller = new $controllerName();

// Check if action method exists
if (!method_exists($controller, $action)) {
    header("HTTP/1.0 404 Not Found");
    die('Action not found: ' . $action);
}

// Call action with remaining parameters
call_user_func_array([$controller, $action], array_slice($url, 2));
?>
