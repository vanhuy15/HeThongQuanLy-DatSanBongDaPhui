<?php
namespace Config;

class Database {
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            // Tự động load .env nếu biến môi trường chưa được nạp
            if (getenv('DB_HOST') === false && file_exists(__DIR__ . '/../.env')) {
                require_once __DIR__ . '/../core/DotEnv.php';
                (new \Core\DotEnv(__DIR__ . '/../.env'))->load();
            }

            $host = getenv('DB_HOST') ?: '127.0.0.1';
            // Nếu host là localhost trên Linux, đổi sang 127.0.0.1 để tránh lỗi Unix socket
            if ($host === 'localhost' && DIRECTORY_SEPARATOR === '/') {
                $host = '127.0.0.1';
            }

            $port = (int)(getenv('DB_PORT') ?: 3306);
            $user = getenv('DB_USER') ?: 'root';
            $pass = (getenv('DB_PASS') !== false) ? getenv('DB_PASS') : '';
            $name = getenv('DB_NAME') ?: 'quanlysanbong';

            try {
                self::$conn = new \mysqli($host, $user, $pass, $name, $port);
            } catch (\mysqli_sql_exception $e) {
                // Fallback kết nối 127.0.0.1 nếu gặp lỗi socket
                if ($host !== '127.0.0.1') {
                    try {
                        self::$conn = new \mysqli('127.0.0.1', $user, $pass, $name, $port);
                    } catch (\mysqli_sql_exception $fallbackEx) {
                        if (class_exists('\\Core\\Logger')) {
                            \Core\Logger::error("Kết nối CSDL 127.0.0.1 fallback thất bại: " . $fallbackEx->getMessage());
                        }
                        http_response_code(500);
                        die("Hệ thống đang bảo trì kết nối cơ sở dữ liệu. Vui lòng thử lại sau ít phút.");
                    }
                } else {
                    if (class_exists('\\Core\\Logger')) {
                        \Core\Logger::error("Kết nối CSDL thất bại: " . $e->getMessage());
                    }
                    http_response_code(500);
                    die("Hệ thống đang bảo trì kết nối cơ sở dữ liệu. Vui lòng thử lại sau ít phút.");
                }
            }

            if (self::$conn->connect_error) {
                if (class_exists('\\Core\\Logger')) {
                    \Core\Logger::error("Kết nối CSDL thất bại: " . self::$conn->connect_error);
                }
                http_response_code(500);
                die("Hệ thống đang bảo trì kết nối cơ sở dữ liệu. Vui lòng thử lại sau ít phút.");
            }
            self::$conn->set_charset("utf8mb4");
            date_default_timezone_set('Asia/Ho_Chi_Minh');
        }
        return self::$conn;
    }

    /**
     * Dọn dẹp các đơn đặt sân quá hạn thanh toán
     * Ủy quyền sang Services\BookingCleanupService dành riêng cho Cron Worker
     */
    public static function cleanupExpiredBookings(): void {
        \Services\BookingCleanupService::runCleanup(self::getConnection());
    }
}
