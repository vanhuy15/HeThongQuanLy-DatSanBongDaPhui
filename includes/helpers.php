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

if (!function_exists('get_pagination_range')) {
    /**
     * Thuật toán phân trang cửa sổ trượt (Sliding Window Pagination)
     * Tự động thu gọn dải trang dài bằng dấu '...' (ví dụ: 1, 2, ... 6 hoặc 1, 2, 3, ... 14)
     */
    function get_pagination_range(int $current_page, int $total_pages, int $delta = 1): array {
        if ($total_pages <= 1) return [];
        // Nếu số trang <= 4 thì hiển thị tất cả (1, 2, 3, 4)
        if ($total_pages <= 4) {
            return range(1, $total_pages);
        }
        $range = [];
        $range[] = 1;
        $left = max(2, $current_page - $delta);
        $right = min($total_pages - 1, $current_page + $delta);

        if ($left > 2) {
            $range[] = '...';
        }
        for ($i = $left; $i <= $right; $i++) {
            $range[] = $i;
        }
        if ($right < $total_pages - 1) {
            $range[] = '...';
        }
        $range[] = $total_pages;
        return $range;
    }
}
