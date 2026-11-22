<?php
namespace Core;

abstract class Controller {
    
    /**
     * Render giao diện (View) kèm dữ liệu
     */
    protected function render($view, $data = []) {
        extract($data);
        $file = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($file)) {
            require $file;
        } else {
            echo "View $view not found";
        }
    }

    /**
     * Render một phần giao diện và trả về chuỗi HTML
     */
    protected function renderPartial($view, $data = []) {
        extract($data);
        $file = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($file)) {
            ob_start();
            require $file;
            return ob_get_clean();
        }
        return '';
    }
    
    /**
     * Chuyển hướng trang an toàn hỗ trợ tiền tố thư mục gốc
     */
    protected function redirect($url) {
        if (!preg_match('#^https?://#i', $url) && strpos($url, '/') !== 0) {
            $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
            $base = ($scriptDir !== '/' && !empty($scriptDir)) ? rtrim($scriptDir, '/') : '';
            $url = $base . '/' . ltrim($url, '/');
        }
        header("Location: " . $url);
        exit();
    }
    
    /**
     * Trả về phản hồi dạng JSON
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
