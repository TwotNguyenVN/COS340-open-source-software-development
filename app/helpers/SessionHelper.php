<?php

class SessionHelper {
    public static function isLoggedIn() {
        $loggedIn = isset($_SESSION['username']);
        if ($loggedIn && !isset($_SESSION['user_id'])) {
            require_once 'app/config/database.php';
            require_once 'app/models/AccountModel.php';
            $db = (new Database())->getConnection();
            $accountModel = new AccountModel($db);
            $user = $accountModel->getAccountByUsername($_SESSION['username']);
            if ($user) {
                $_SESSION['user_id'] = $user->id;
            }
        }
        return $loggedIn;
    }

    public static function isAdmin() {
        return isset($_SESSION['username']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public static function getCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

