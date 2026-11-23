<?php
namespace Core;

use Config\Database;

class Auth {

    public static function isLoggedIn(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['nguoi_dung_id']);
    }

    public static function isAdmin(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'admin';
    }

    public static function getAppBaseUrl(): string {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        if (preg_match('#^(.*?quanlysanbong)#i', $script, $m)) {
            return $m[1];
        }
        $dir = rtrim(dirname($script), '/\\');
        if (strpos($dir, '/admin') !== false) {
            $dir = substr($dir, 0, strpos($dir, '/admin'));
        }
        return $dir ?: '';
    }

    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            $base = self::getAppBaseUrl();
            $target = $base . '/index.php?controller=Auth&action=login';
            if (!headers_sent()) {
                header("Location: " . $target);
            } else {
                echo "<script>window.location.href='" . htmlspecialchars($target, ENT_QUOTES) . "';</script>";
            }
            exit();
        }
    }

    public static function requireAdmin(): void {
        if (!self::isAdmin()) {
            $base = self::getAppBaseUrl();
            $target = $base . '/index.php';
            if (!headers_sent()) {
                header("Location: " . $target);
            } else {
                echo "<script>window.location.href='" . htmlspecialchars($target, ENT_QUOTES) . "';</script>";
            }
            exit();
        }
    }

    public static function cleanInput(string $data): string {
        $data = trim($data);
        $data = stripslashes($data);
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

// Global functions for backward compatibility across legacy admin views
if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool {
        return \Core\Auth::isLoggedIn();
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool {
        return \Core\Auth::isAdmin();
    }
}

if (!function_exists('require_login')) {
    function require_login(): void {
        \Core\Auth::requireLogin();
    }
}

if (!function_exists('require_admin')) {
    function require_admin(): void {
        \Core\Auth::requireAdmin();
    }
}

if (!function_exists('get_app_base_url')) {
    function get_app_base_url(): string {
        return \Core\Auth::getAppBaseUrl();
    }
}

if (!function_exists('clean_input')) {
    function clean_input($data): string {
        return \Core\Auth::cleanInput((string)$data);
    }
}
