<?php
namespace Services;

use Config\Database;

class BookingCleanupService {

    /**
     * Chạy quy trình dọn dẹp các đơn đặt sân quá hạn, hoàn tiền tự động và đồng bộ điểm uy tín.
     * Thiết kế riêng cho CRON / Background Worker định kỳ, không chạy trong request web thông thường.
     */
    public static function runCleanup(?\mysqli $conn = null): array {
        if ($conn === null) {
            $conn = Database::getConnection();
        }

        $stats = [
            'cancelled_unpaid' => 0,
            'refund_pending_qr' => 0,
            'cancelled_cash' => 0,
            'released_slots' => 0,
            'reputation_awarded' => 0
        ];

        // 1. Tự động hủy các đơn quá 10 phút chưa hoàn tất thanh toán
        $conn->query("
            UPDATE dat_san ds
            LEFT JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
            SET ds.trang_thai = 'Đã hủy', ds.thong_tin_hoan_tien = 'expired_hold_10m'
            WHERE ds.deleted_at IS NULL
              AND (
                  (ds.trang_thai = 'Chờ thanh toán' AND TIMESTAMPDIFF(SECOND, ds.ngay_tao, NOW()) >= 600)
                  OR (ds.trang_thai = 'Chờ xác nhận' AND (tt.trang_thai IS NULL OR (tt.phuong_thuc = 'chuyen_khoan' AND tt.trang_thai != 'da_thanh_toan')) AND TIMESTAMPDIFF(SECOND, ds.ngay_tao, NOW()) >= 600)
              )
        ");
        $stats['cancelled_unpaid'] = $conn->affected_rows > 0 ? $conn->affected_rows : 0;

        // 2. Tự động xử lý đơn quá giờ thi đấu mà Admin chưa duyệt
        // 2a. Đơn Chuyển khoản / VietQR (Khách đã thanh toán): Chuyển Chờ hoàn tiền
        $stale_qr = $conn->query("
            SELECT ds.dat_san_id, ds.nguoi_dung_id, sb.ten_san, ds.ngay_dat, kg.gio_bat_dau
            FROM dat_san ds
            JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
            JOIN san_bong sb ON kg.san_id = sb.san_id
            JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
            WHERE ds.trang_thai = 'Chờ xác nhận'
              AND tt.phuong_thuc = 'chuyen_khoan'
              AND ds.deleted_at IS NULL
              AND CONCAT(ds.ngay_dat, ' ', kg.gio_bat_dau) <= NOW()
        ");

        if ($stale_qr && $stale_qr->num_rows > 0) {
            while ($stale = $stale_qr->fetch_assoc()) {
                $b_id = (int)$stale['dat_san_id'];
                $u_id = (int)$stale['nguoi_dung_id'];
                $rf_note = json_encode([
                    'ngan_hang' => 'Chờ khách bổ sung hoặc Admin liên hệ hoàn',
                    'stk' => 'Chưa cung cấp',
                    'ten_chu_tk' => '',
                    'ly_do' => 'Hệ thống tự động hủy do Quản trị viên chưa duyệt đơn trước giờ thi đấu. Cần hoàn tiền!',
                    'thoi_gian_yeu_cau' => date('Y-m-d H:i:s'),
                    'auto_expired' => true
                ], JSON_UNESCAPED_UNICODE);

                $stmt_up = $conn->prepare("UPDATE dat_san SET trang_thai = 'Chờ hoàn tiền', thong_tin_hoan_tien = ? WHERE dat_san_id = ?");
                $stmt_up->bind_param("si", $rf_note, $b_id);
                $stmt_up->execute();
                $stmt_up->close();

                // Tạo thông báo cho khách hàng
                $noti_stmt = $conn->prepare("
                    INSERT INTO thong_bao (nguoi_dung_id, tieu_de, noi_dung, loai, lien_ket) 
                    VALUES (?, ?, ?, 'dat_san', 'index.php?controller=User&action=history')
                ");
                $title = "Đơn #PM-$b_id được chuyển sang mục Hoàn tiền";
                $content = "Đơn đặt sân " . $stale['ten_san'] . " ngày " . date('d/m/Y', strtotime($stale['ngay_dat'])) . " đã quá giờ thi đấu mà quản trị viên chưa xác nhận. Hệ thống đã chuyển đơn sang mục Hoàn tiền để bảo vệ quyền lợi của bạn.";
                $noti_stmt->bind_param("iss", $u_id, $title, $content);
                $noti_stmt->execute();
                $noti_stmt->close();

                $stats['refund_pending_qr']++;
            }
        }

        // 2b. Đơn Tiền mặt (Khách chưa trả tiền): Tự động chuyển Đã hủy
        $conn->query("
            UPDATE dat_san ds
            JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
            JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
            SET ds.trang_thai = 'Đã hủy'
            WHERE ds.trang_thai = 'Chờ xác nhận'
              AND tt.phuong_thuc = 'tien_mat'
              AND ds.deleted_at IS NULL
              AND CONCAT(ds.ngay_dat, ' ', kg.gio_bat_dau) <= NOW()
        ");
        $stats['cancelled_cash'] = $conn->affected_rows > 0 ? $conn->affected_rows : 0;

        // 3. Đồng bộ giải phóng các slot khóa ở cấp CSDL cho các đơn đã hủy hoặc hoàn tiền
        $conn->query("
            UPDATE dat_san_slots dss
            JOIN dat_san ds ON dss.dat_san_id = ds.dat_san_id
            SET dss.trang_thai = 'released'
            WHERE ds.trang_thai IN ('Đã hủy', 'Hủy', 'Chờ hoàn tiền', 'Đã hoàn tiền')
              AND dss.trang_thai = 'active'
        ");
        $stats['released_slots'] = $conn->affected_rows > 0 ? $conn->affected_rows : 0;

        // 4. Tự động đồng bộ điểm uy tín +5đ cho các trận đấu đã kết thúc
        \Models\User::syncCompletedMatches($conn);

        return $stats;
    }
}
