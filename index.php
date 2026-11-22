<?php
// Cấu hình Session Cookie an toàn (HttpOnly, SameSite, Secure)
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

// Thiết lập HTTP Header bảo mật chống XSS, Clickjacking và MIME-sniffing
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com https://cdnjs.cloudflare.com https://img.vietqr.io data:; img-src 'self' data: https: https://img.vietqr.io blob:; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; connect-src 'self' https:;");
@header_remove('X-Powered-By');

// Tự động tải (autoload) các lớp MVC
spl_autoload_register(function ($class) {
    // Chuyển namespace thành đường dẫn tệp
    $path = str_replace('\\', '/', $class) . '.php';
    if (file_exists(__DIR__ . '/' . $path)) {
        require_once __DIR__ . '/' . $path;
    } else {
        $path_lower = strtolower(dirname($path)) . '/' . basename($path);
        if (file_exists(__DIR__ . '/' . $path_lower)) {
            require_once __DIR__ . '/' . $path_lower;
        }
    }
});

// Nạp biến môi trường từ tệp .env
$dotenv = new \Core\DotEnv(__DIR__ . '/.env');
$dotenv->load();

// Nạp helper bảo vệ chống CSRF
require_once __DIR__ . '/includes/csrf.php';

// Xác thực mã CSRF Token toàn cục cho mọi yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verify_csrf_token($token)) {
        http_response_code(403);
        die("Lỗi 403: Yêu cầu bị từ chối do thiếu hoặc sai mã bảo vệ (CSRF Token). Vui lòng tải lại trang và thử lại.");
    }
}

// Khởi tạo và điều hướng yêu cầu qua Router
$router = new \Core\Router();
$router->dispatch($_SERVER['REQUEST_URI']);