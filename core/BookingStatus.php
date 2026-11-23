<?php
namespace Core;

/**
 * Quản lý tập trung các hằng số Trạng thái Đơn Đặt Sân (Booking Status Constants)
 * Ngăn chặn lỗi gõ sai chính tả chuỗi (String Typo) và hỗ trợ kiểm tra kiểu chặt chẽ.
 */
class BookingStatus {
    public const CHO_THANH_TOAN = 'Chờ thanh toán';
    public const CHO_XAC_NHAN   = 'Chờ xác nhận';
    public const DA_XAC_NHAN    = 'Đã xác nhận';
    public const DA_TU_CHOI     = 'Đã từ chối';
    public const CHO_HOAN_TIEN  = 'Chờ hoàn tiền';
    public const DA_HOAN_TIEN   = 'Đã hoàn tiền';
    public const DA_HUY         = 'Đã hủy';

    /**
     * Danh sách toàn bộ các trạng thái hợp lệ
     */
    public static function all(): array {
        return [
            self::CHO_THANH_TOAN,
            self::CHO_XAC_NHAN,
            self::DA_XAC_NHAN,
            self::DA_TU_CHOI,
            self::CHO_HOAN_TIEN,
            self::DA_HOAN_TIEN,
            self::DA_HUY,
        ];
    }

    /**
     * Kiểm tra trạng thái có hợp lệ trong hệ thống hay không
     */
    public static function isValid(string $status): bool {
        return in_array($status, self::all(), true);
    }
}
