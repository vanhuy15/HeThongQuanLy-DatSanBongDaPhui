<?php
/**
 * Admin Panel Bootstrap
 * --------------------------------------------------------------------
 * Khởi tạo môi trường Quản trị viên nhất quán với kiến trúc MVC:
 * - Dùng chung Config\Database singleton
 * - Kiểm tra phiên đăng nhập & quyền admin qua Core\Auth
 * - Tự động xác thực CSRF Token cho toàn bộ POST request
 * --------------------------------------------------------------------
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/core/Auth.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

// Tải autoloader cho Models & Services nếu cần
spl_autoload_register(function ($class) {
    $projectRoot = dirname(__DIR__);
    $prefixes = [
        'Models\\' => $projectRoot . '/models/',
        'Services\\' => $projectRoot . '/services/',
        'Core\\' => $projectRoot . '/core/',
        'Config\\' => $projectRoot . '/config/',
    ];
    foreach ($prefixes as $prefix => $dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $file = $dir . str_replace('\\', '/', substr($class, $len)) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// Singleton Database Connection nhất quán toàn dự án
$conn = \Config\Database::getConnection();

// Xác thực bảo mật CSRF cho mọi request POST phía Admin
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verify_csrf_token($token)) {
        http_response_code(403);
        die("Lỗi 403: Yêu cầu bị từ chối do thiếu hoặc sai mã bảo vệ (CSRF Token). Vui lòng tải lại trang và thử lại.");
    }
}
