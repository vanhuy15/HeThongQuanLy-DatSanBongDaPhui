<?php
namespace Core;

class Router {
    protected $routes = [];

    // Danh sách alias ánh xạ các đường dẫn ngắn / RESTful về Controller & Action tương ứng
    protected $routeAliases = [
        '' => ['Home', 'index'],
        'home' => ['Home', 'index'],
        'login' => ['Auth', 'login'],
        'logout' => ['Auth', 'logout'],
        'register' => ['Auth', 'register'],
        'profile' => ['User', 'profile'],
        'history' => ['User', 'history'],
        'schedule' => ['DatSan', 'schedule'],
        'payment' => ['Payment', 'index'],

        // RESTful API Aliases
        'api/auth/login' => ['Auth', 'login'],
        'api/auth/logout' => ['Auth', 'logout'],
        'api/auth/register' => ['Auth', 'register'],
        'api/pitches' => ['Home', 'search'],
        'api/schedule' => ['DatSan', 'getSchedule'],
        'api/slots/check' => ['DatSan', 'checkAvailability'],
        'api/bookings' => ['DatSan', 'bookSubmit'],
        'api/payment/confirm' => ['Payment', 'confirm'],
        'api/payment/status' => ['Payment', 'checkStatus'],
        'api/notifications/unread' => ['Notification', 'getUnread'],
        'api/reviews' => ['Review', 'submit'],
    ];

    // Bảng chuẩn hóa tên Controller chuẩn theo thư mục controllers/
    protected $controllerMap = [
        'admin' => 'Admin',
        'auth' => 'Auth',
        'datsan' => 'DatSan',
        'home' => 'Home',
        'notification' => 'Notification',
        'payment' => 'Payment',
        'review' => 'Review',
        'user' => 'User',
    ];

    public function add($route, $params = []) {
        $this->routes[$route] = $params;
    }

    public function dispatch($url) {
        $controllerName = 'Home';
        $actionName = 'index';

        // 1. Ưu tiên kiểm tra Query Parameters nếu có (Đảm bảo tương thích ngược 100% với code/test cũ)
        if (!empty($_GET['controller'])) {
            $controllerName = $_GET['controller'];
            $actionName = $_GET['action'] ?? 'index';
        } else {
            // 2. Phân tích Clean URL / RESTful Route từ $url (REQUEST_URI)
            $requestPath = parse_url($url, PHP_URL_PATH) ?? '';

            // Loại bỏ thư mục gốc của project (ví dụ /quanlysanbong/)
            $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
            if ($scriptDir !== '/' && $scriptDir !== '\\' && !empty($scriptDir)) {
                $pattern = '#^' . preg_quote($scriptDir, '#') . '#i';
                $requestPath = preg_replace($pattern, '', $requestPath);
            }

            // Loại bỏ dấu gạch chéo đầu cuối và index.php nếu có
            $requestPath = trim($requestPath, '/');
            if (strpos($requestPath, 'index.php') === 0) {
                $requestPath = trim(substr($requestPath, 9), '/');
            }

            $normalizedPath = strtolower($requestPath);

            // Kiểm tra trong danh sách Alias ngắn / RESTful API
            if (isset($this->routeAliases[$normalizedPath])) {
                [$controllerName, $actionName] = $this->routeAliases[$normalizedPath];
            } elseif (!empty($requestPath)) {
                // Định tuyến theo cấu trúc chuẩn: /{controller}/{action}
                $segments = explode('/', $requestPath);
                if (!empty($segments[0])) {
                    $rawCtrl = strtolower($segments[0]);
                    $controllerName = $this->controllerMap[$rawCtrl] ?? ucfirst($segments[0]);
                }
                if (!empty($segments[1])) {
                    $actionName = $segments[1];
                }
            }
        }

        // Chuẩn hóa tên Controller qua bảng map nếu chưa khớp
        $lookupKey = strtolower($controllerName);
        if (isset($this->controllerMap[$lookupKey])) {
            $controllerName = $this->controllerMap[$lookupKey];
        }

        // Phòng chống Path Traversal / Lỗ hổng bảo mật tên class
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $controllerName) || !preg_match('/^[a-zA-Z0-9_]+$/', $actionName)) {
            http_response_code(400);
            die("Lỗi 400: Tên controller hoặc action không hợp lệ.");
        }

        $controllerClass = 'Controllers\\' . $controllerName . 'Controller';

        if (class_exists($controllerClass)) {
            $controllerObject = new $controllerClass();

            // Tìm method thực tế (không phân biệt hoa thường để hỗ trợ checkAvailability vs checkavailability)
            $targetAction = $actionName;
            $classMethods = get_class_methods($controllerObject);
            foreach ($classMethods as $method) {
                if (strcasecmp($method, $actionName) === 0) {
                    $targetAction = $method;
                    break;
                }
            }

            if (is_callable([$controllerObject, $targetAction])) {
                $controllerObject->$targetAction();
            } else {
                http_response_code(404);
                $errorView = __DIR__ . '/../views/errors/404.php';
                if (file_exists($errorView)) {
                    require $errorView;
                } else {
                    $safeAction = htmlspecialchars($actionName, ENT_QUOTES, 'UTF-8');
                    $safeClass = htmlspecialchars($controllerClass, ENT_QUOTES, 'UTF-8');
                    echo "Lỗi 404: Phương thức {$safeAction} trong {$safeClass} không tồn tại.";
                }
            }
        } else {
            http_response_code(404);
            $errorView = __DIR__ . '/../views/errors/404.php';
            if (file_exists($errorView)) {
                require $errorView;
            } else {
                $safeClass = htmlspecialchars($controllerClass, ENT_QUOTES, 'UTF-8');
                echo "Lỗi 404: Controller {$safeClass} không tồn tại.";
            }
        }
    }
}
