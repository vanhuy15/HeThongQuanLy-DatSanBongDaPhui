<?php
/**
 * Global Helper Functions (PitchManager)
 * Cung cấp các hàm trợ giúp toàn cục tương thích ngược cho Admin & Views
 */

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

if (!function_exists('syncCompletedMatchesReputation')) {
    function syncCompletedMatchesReputation($conn = null, $userId = null): void {
        if (class_exists('\Models\User')) {
            \Models\User::syncCompletedMatches($conn, $userId);
        }
    }
}
