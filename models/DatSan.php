<?php

namespace Models;

use Config\Database;

class DatSan
{

    public const STATUS_HOLD           = \Core\BookingStatus::CHO_THANH_TOAN;
    public const STATUS_PENDING        = \Core\BookingStatus::CHO_XAC_NHAN;
    public const STATUS_CONFIRMED      = \Core\BookingStatus::DA_XAC_NHAN;
    public const STATUS_REJECTED       = \Core\BookingStatus::DA_TU_CHOI;
    public const STATUS_REFUND_PENDING = \Core\BookingStatus::CHO_HOAN_TIEN;
    public const STATUS_REFUNDED       = \Core\BookingStatus::DA_HOAN_TIEN;
    public const STATUS_CANCELLED      = \Core\BookingStatus::DA_HUY;

    /**
     * Dọn dẹp các đơn đặt sân quá hạn thanh toán
     */
    public static function cleanupExpiredBookings()
    {
        \Services\BookingCleanupService::runCleanup(Database::getConnection());
    }

    /**
     * Tự động hủy các đơn Chờ thanh toán quá thời gian quy định (mặc định 10 phút)
     * và lập tức giải phóng slot CSDL để người khác có thể đặt sân ngay.
     * @return int Số lượng đơn đã được tự động hủy
     */
    public static function autoCancelExpiredHolds(int $holdDurationMinutes = 10): int
    {
        $conn = Database::getConnection();

        // 1. Tìm tất cả đơn Chờ thanh toán quá thời hạn (hoặc Chờ xác nhận nhưng chưa thanh toán quá 10 phút)
        $seconds = $holdDurationMinutes * 60;
        $stmt = $conn->prepare("
            SELECT ds.dat_san_id, ds.nguoi_dung_id, sb.ten_san, ds.ngay_dat
            FROM dat_san ds
            JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
            JOIN san_bong sb ON kg.san_id = sb.san_id
            LEFT JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
            WHERE ds.deleted_at IS NULL
              AND (
                  (ds.trang_thai = 'Chờ thanh toán' AND TIMESTAMPDIFF(SECOND, ds.ngay_tao, NOW()) >= ?)
                  OR (ds.trang_thai = 'Chờ xác nhận' AND (tt.trang_thai IS NULL OR (tt.phuong_thuc = 'chuyen_khoan' AND tt.trang_thai != 'da_thanh_toan')) AND TIMESTAMPDIFF(SECOND, ds.ngay_tao, NOW()) >= ?)
              )
        ");
        if (!$stmt) {
            return 0;
        }
        $stmt->bind_param("ii", $seconds, $seconds);
        $stmt->execute();
        $expiredBookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if (empty($expiredBookings)) {
            return 0;
        }

        $ids = array_column($expiredBookings, 'dat_san_id');
        $idList = implode(',', array_map('intval', $ids));

        // 2. Chuyển trạng thái đơn sang 'Đã hủy' và đánh dấu lý do hết hạn giữ chỗ 10 phút
        $conn->query("UPDATE dat_san SET trang_thai = 'Đã hủy', thong_tin_hoan_tien = 'expired_hold_10m' WHERE dat_san_id IN ($idList)");

        // 3. Giải phóng slot trong dat_san_slots (trang_thai = 'released')
        $conn->query("UPDATE dat_san_slots SET trang_thai = 'released' WHERE dat_san_id IN ($idList)");

        // 4. Gửi thông báo cho từng khách hàng
        require_once __DIR__ . '/Notification.php';
        if (class_exists('\\Models\\Notification')) {
            foreach ($expiredBookings as $b) {
                \Models\Notification::create(
                    (int)$b['nguoi_dung_id'],
                    "Đơn đặt sân #{$b['dat_san_id']} đã tự động hủy",
                    "Đơn đặt sân " . $b['ten_san'] . " ngày " . date('d/m/Y', strtotime($b['ngay_dat'])) . " đã tự động hủy do quá thời hạn thanh toán {$holdDurationMinutes} phút. Sân đã được giải phóng cho khách hàng khác.",
                    'dat_san',
                    "index.php?controller=User&action=history"
                );
            }
        }

        return count($expiredBookings);
    }

    /**
     * Lấy danh sách các sân con đã bị khóa trong khung giờ và ngày cụ thể
     */
    public static function getLockedSubPitches($khung_gio_id, $ngay_dat)
    {
        self::autoCancelExpiredHolds(10);
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            SELECT ds.ten_san_con, ds.san_con_khoa 
            FROM dat_san ds
            LEFT JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
            WHERE ds.khung_gio_id = ? AND ds.ngay_dat = ? 
              AND (
                  ds.trang_thai = 'Đã xác nhận' 
                  OR (ds.trang_thai = 'Chờ xác nhận' AND (tt.phuong_thuc = 'tien_mat' OR tt.trang_thai = 'da_thanh_toan'))
                  OR (ds.trang_thai = 'Chờ xác nhận' AND tt.phuong_thuc = 'chuyen_khoan' AND tt.trang_thai != 'da_thanh_toan' AND TIMESTAMPDIFF(SECOND, ds.ngay_tao, NOW()) < 600)
                  OR (ds.trang_thai = 'Chờ thanh toán' AND TIMESTAMPDIFF(SECOND, ds.ngay_tao, NOW()) < 600)
              ) 
              AND ds.deleted_at IS NULL
        ");
        $stmt->bind_param("is", $khung_gio_id, $ngay_dat);
        $stmt->execute();
        $res = $stmt->get_result();

        $locked = [];
        while ($r = $res->fetch_assoc()) {
            if (!empty($r['san_con_khoa'])) {
                $parts = explode(',', $r['san_con_khoa']);
                foreach ($parts as $p) {
                    if (trim($p) !== '') $locked[] = trim($p);
                }
            }
            if (!empty($r['ten_san_con'])) {
                $locked[] = trim($r['ten_san_con']);
            }
        }
        $stmt->close();
        return array_unique($locked);
    }

    /**
     * Lấy chi tiết trạng thái từng sân con: Đã đặt (confirmed) và Đang giữ chỗ (holding)
     * @return array ['confirmed' => array, 'holding' => array, 'locked' => array]
     */
    public static function getSubPitchesStatusDetails($khung_gio_id, $ngay_dat)
    {
        self::autoCancelExpiredHolds(10);
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            SELECT ds.ten_san_con, ds.san_con_khoa, ds.trang_thai, ds.ngay_tao, tt.phuong_thuc, tt.trang_thai as tt_trang_thai 
            FROM dat_san ds
            LEFT JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
            WHERE ds.khung_gio_id = ? AND ds.ngay_dat = ? 
              AND (
                  ds.trang_thai = 'Đã xác nhận' 
                  OR (ds.trang_thai = 'Chờ xác nhận' AND (tt.phuong_thuc = 'tien_mat' OR tt.trang_thai = 'da_thanh_toan'))
                  OR (ds.trang_thai = 'Chờ xác nhận' AND tt.phuong_thuc = 'chuyen_khoan' AND tt.trang_thai != 'da_thanh_toan' AND TIMESTAMPDIFF(SECOND, ds.ngay_tao, NOW()) < 600)
                  OR (ds.trang_thai = 'Chờ thanh toán' AND TIMESTAMPDIFF(SECOND, ds.ngay_tao, NOW()) < 600)
              ) 
              AND ds.deleted_at IS NULL
        ");
        $stmt->bind_param("is", $khung_gio_id, $ngay_dat);
        $stmt->execute();
        $res = $stmt->get_result();

        $confirmed = [];
        $holding = [];

        while ($r = $res->fetch_assoc()) {
            $is_confirmed = ($r['trang_thai'] === 'Đã xác nhận' || ($r['trang_thai'] === 'Chờ xác nhận' && ($r['phuong_thuc'] === 'tien_mat' || $r['tt_trang_thai'] === 'da_thanh_toan')));
            $pitches = [];
            if (!empty($r['san_con_khoa'])) {
                $parts = explode(',', $r['san_con_khoa']);
                foreach ($parts as $p) {
                    if (trim($p) !== '') $pitches[] = trim($p);
                }
            }
            if (!empty($r['ten_san_con'])) {
                $pitches[] = trim($r['ten_san_con']);
            }

            if ($is_confirmed) {
                foreach ($pitches as $p) $confirmed[] = $p;
            } else {
                foreach ($pitches as $p) $holding[] = $p;
            }
        }
        $stmt->close();

        $confirmed = array_values(array_unique($confirmed));
        $holding = array_values(array_unique($holding));
        // Nếu sân đã được chốt confirmed thì không tính là holding nữa
        $holding = array_values(array_diff($holding, $confirmed));
        $locked = array_values(array_unique(array_merge($confirmed, $holding)));

        return [
            'confirmed' => $confirmed,
            'holding' => $holding,
            'locked' => $locked
        ];
    }

    /**
     * Thực hiện đặt sân có cơ chế khóa dòng chống Race Condition
     * @return array ['success' => bool, 'message' => string, 'dat_san_id' => int|null]
     */
    public static function bookWithLock($nguoi_dung_id, $khung_gio_id, $ngay_dat, $loai_san_dat, $ten_san_con, $san_con_khoa)
    {
        self::autoCancelExpiredHolds(10);
        $conn = Database::getConnection();

        // 1. Kiểm tra điểm uy tín khách hàng
        $user = User::findById($nguoi_dung_id);
        $diem_uy_tin = (int)($user['diem_uy_tin'] ?? 100);

        if ($diem_uy_tin < 40) {
            return [
                'success' => false,
                'message' => "⛔ Tạm dừng đặt sân! Điểm uy tín của bạn hiện tại là {$diem_uy_tin}/100 (Dưới 40đ). Vui lòng liên hệ Hotline hoặc Admin để được hỗ trợ điều chỉnh điểm uy tín."
            ];
        }

        // 2. Kiểm tra tính hợp lệ của ngày đặt và khung giờ (Chống đặt khung giờ đã trôi qua)
        $min_allowed = max('2025-01-01', date('Y-m-d'));
        if (empty($ngay_dat) || $ngay_dat < $min_allowed) {
            return [
                'success' => false,
                'message' => 'Lỗi: Ngày đặt sân không hợp lệ hoặc đã trôi qua trong quá khứ.'
            ];
        }

        $kg_check = $conn->prepare("SELECT gio_bat_dau FROM khung_gio WHERE khung_gio_id = ?");
        $kg_check->bind_param("i", $khung_gio_id);
        $kg_check->execute();
        $kg_info = $kg_check->get_result()->fetch_assoc();
        $kg_check->close();

        if (!$kg_info) {
            return [
                'success' => false,
                'message' => 'Lỗi: Khung giờ không tồn tại!'
            ];
        }

        $slot_start_time = strtotime($ngay_dat . ' ' . $kg_info['gio_bat_dau']);
        $buffer_seconds = 5 * 60; // 5 phút khoảng đệm chuẩn bị sân
        if ($slot_start_time <= (time() + $buffer_seconds)) {
            return [
                'success' => false,
                'message' => 'Khung giờ này đã trôi qua hoặc quá sát giờ thi đấu (dưới 5 phút). Vui lòng chọn khung giờ khác!'
            ];
        }

        // 3. Bắt đầu giao tác (Transaction)
        $conn->begin_transaction();

        try {
            $keys_to_check = explode(',', $san_con_khoa);
            $conflict_found = false;

            // FOR UPDATE: Khóa dòng ngăn chặn các transaction khác đọc/ghi cùng lúc
            $check_stmt = $conn->prepare("
                SELECT ds.dat_san_id, ds.ten_san_con, ds.san_con_khoa 
                FROM dat_san ds
                LEFT JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
                WHERE ds.khung_gio_id = ? AND ds.ngay_dat = ? 
                  AND (
                      ds.trang_thai = 'Đã xác nhận' 
                      OR (ds.trang_thai = 'Chờ xác nhận' AND (tt.phuong_thuc = 'tien_mat' OR tt.trang_thai = 'da_thanh_toan'))
                      OR (ds.trang_thai = 'Chờ xác nhận' AND tt.phuong_thuc = 'chuyen_khoan' AND tt.trang_thai != 'da_thanh_toan' AND TIMESTAMPDIFF(SECOND, ds.ngay_tao, NOW()) < 600)
                      OR (ds.trang_thai = 'Chờ thanh toán' AND TIMESTAMPDIFF(SECOND, ds.ngay_tao, NOW()) < 600)
                  ) 
                  AND ds.deleted_at IS NULL 
                FOR UPDATE
            ");
            $check_stmt->bind_param("is", $khung_gio_id, $ngay_dat);
            $check_stmt->execute();
            $check_res = $check_stmt->get_result();

            while ($active_booking = $check_res->fetch_assoc()) {
                $already_locked = [];
                if (!empty($active_booking['san_con_khoa'])) {
                    $parts = explode(',', $active_booking['san_con_khoa']);
                    foreach ($parts as $p) $already_locked[] = trim($p);
                }
                if (!empty($active_booking['ten_san_con'])) {
                    $already_locked[] = trim($active_booking['ten_san_con']);
                }

                if (count(array_intersect($keys_to_check, $already_locked)) > 0) {
                    $conflict_found = true;
                    break;
                }
            }
            $check_stmt->close();

            if ($conflict_found) {
                $conn->rollback();
                return [
                    'success' => false,
                    'message' => "Rất tiếc! Sân bạn vừa chọn (hoặc sân con thành phần) vừa có người khác đặt. Vui lòng chọn sân hoặc khung giờ khác."
                ];
            }

            // Ghi nhận đặt sân
            $stmt_insert = $conn->prepare("
                INSERT INTO dat_san (nguoi_dung_id, khung_gio_id, ngay_dat, trang_thai, ten_san_con, loai_san_dat, san_con_khoa) 
                VALUES (?, ?, ?, 'Chờ thanh toán', ?, ?, ?)
            ");
            $stmt_insert->bind_param("iissss", $nguoi_dung_id, $khung_gio_id, $ngay_dat, $ten_san_con, $loai_san_dat, $san_con_khoa);
            $stmt_insert->execute();
            $new_dat_san_id = $conn->insert_id;
            $stmt_insert->close();

            // Ghi nhận slot khóa cứng cấp CSDL (Hard Database Constraints)
            $sub_pitches = array_filter(array_map('trim', explode(',', $san_con_khoa ?: $ten_san_con)));
            $slot_stmt = $conn->prepare("
                INSERT INTO dat_san_slots (dat_san_id, khung_gio_id, ngay_dat, ten_san_con, trang_thai) 
                VALUES (?, ?, ?, ?, 'active')
            ");
            foreach ($sub_pitches as $sp) {
                $slot_stmt->bind_param("iiss", $new_dat_san_id, $khung_gio_id, $ngay_dat, $sp);
                if (!$slot_stmt->execute()) {
                    // Lỗi trùng lặp UNIQUE KEY active_key (MySQL Error 1062)
                    if ($conn->errno === 1062) {
                        $slot_stmt->close();
                        $conn->rollback();
                        return [
                            'success' => false,
                            'message' => "Rất tiếc! Sân bạn vừa chọn (hoặc sân con thành phần) vừa có người khác đặt. Vui lòng chọn sân hoặc khung giờ khác."
                        ];
                    }
                    throw new \Exception("Lỗi ghi nhận slot CSDL: " . $conn->error);
                }
            }
            $slot_stmt->close();

            // Commit giao dịch thành công
            $conn->commit();

            // Lấy thông tin chi tiết để gửi thông báo
            $stmt_detail = $conn->prepare("
                SELECT ds.*, nd.ho_ten, nd.email, sb.ten_san, sb.dia_chi, kg.gio_bat_dau, kg.gio_ket_thuc, kg.gia_tien, kg.gia_tien_san_7 
                FROM dat_san ds
                JOIN nguoi_dung nd ON ds.nguoi_dung_id = nd.nguoi_dung_id
                JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
                JOIN san_bong sb ON kg.san_id = sb.san_id
                WHERE ds.dat_san_id = ?
            ");
            $stmt_detail->bind_param("i", $new_dat_san_id);
            $stmt_detail->execute();
            $booking = $stmt_detail->get_result()->fetch_assoc();
            $stmt_detail->close();

            if ($booking) {
                $ten_san = $booking['ten_san'];
                $san_con = $booking['ten_san_con'];
                $ngay_dat_fmt = date('d/m/Y', strtotime($booking['ngay_dat']));
                $gio_da = date('H:i', strtotime($booking['gio_bat_dau'])) . ' - ' . date('H:i', strtotime($booking['gio_ket_thuc']));

                \Models\Notification::create(
                    $nguoi_dung_id,
                    "Vui lòng thanh toán đơn đặt sân #$new_dat_san_id",
                    "Bạn vừa giữ chỗ sân $ten_san ($san_con) ngày $ngay_dat_fmt lúc $gio_da. Vui lòng hoàn tất thanh toán trong 10 phút, nếu không đơn sẽ bị tự động hủy!",
                    'dat_san',
                    "index.php?controller=Payment&action=index&dat_san_id=$new_dat_san_id"
                );
            }

            return [
                'success' => true,
                'message' => 'Đặt sân thành công!',
                'dat_san_id' => $new_dat_san_id,
                'redirect' => "index.php?controller=Payment&action=index&dat_san_id=$new_dat_san_id"
            ];
        } catch (\Exception $e) {
            $conn->rollback();
            \Core\Logger::error("Lỗi đặt sân bookWithLock: " . $e->getMessage(), [
                'user_id' => $nguoi_dung_id,
                'khung_gio_id' => $khung_gio_id,
                'ngay_dat' => $ngay_dat,
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'Rất tiếc! Hệ thống đang bận hoặc có lỗi xảy ra trong quá trình xử lý đơn đặt sân. Vui lòng thử lại sau.'
            ];
        }
    }

    /**
     * Lấy thông tin đơn đặt sân theo ID kèm chi tiết sân và khung giờ
     */
    public static function findById($id)
    {
        $conn = Database::getConnection();
        $sql = "SELECT ds.*, sb.san_id, sb.ten_san, sb.dia_chi, sb.quy_tac_ghep,
                       kg.gio_bat_dau, kg.gio_ket_thuc, kg.gia_tien, kg.gia_tien_san_7,
                       nd.ho_ten, nd.email, nd.sdt, nd.diem_uy_tin, nd.so_lan_bung_san,
                       tt.phuong_thuc, tt.trang_thai as tt_trang_thai
                FROM dat_san ds
                JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
                JOIN san_bong sb ON kg.san_id = sb.san_id
                JOIN nguoi_dung nd ON ds.nguoi_dung_id = nd.nguoi_dung_id
                LEFT JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
                WHERE ds.dat_san_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res;
    }

    /**
     * Lấy lịch sử đặt sân của một người dùng
     */
    public static function getByUserId($user_id, $status_filter = null)
    {
        $conn = Database::getConnection();
        $where = "WHERE ds.nguoi_dung_id = ? AND ds.deleted_at IS NULL";
        if (!empty($status_filter)) {
            $where .= " AND ds.trang_thai = ?";
        }

        $sql = "SELECT ds.*, sb.san_id, sb.ten_san, sb.dia_chi, kg.gio_bat_dau, kg.gio_ket_thuc, kg.gia_tien, kg.gia_tien_san_7,
                       tt.phuong_thuc, tt.trang_thai as tt_trang_thai
                FROM dat_san ds
                JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
                JOIN san_bong sb ON kg.san_id = sb.san_id
                LEFT JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
                $where
                ORDER BY ds.ngay_dat DESC, kg.gio_bat_dau DESC";

        $stmt = $conn->prepare($sql);
        if (!empty($status_filter)) {
            $stmt->bind_param("is", $user_id, $status_filter);
        } else {
            $stmt->bind_param("i", $user_id);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $list = [];
        while ($r = $res->fetch_assoc()) {
            $list[] = $r;
        }
        $stmt->close();
        return $list;
    }

    /**
     * Cập nhật trạng thái đơn đặt sân
     */
    public static function updateStatus($dat_san_id, $trang_thai)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE dat_san SET trang_thai = ? WHERE dat_san_id = ?");
        $stmt->bind_param("si", $trang_thai, $dat_san_id);
        $res = $stmt->execute();
        $stmt->close();

        // Tự động giải phóng slot cấp CSDL nếu đơn chuyển sang trạng thái Hủy hoặc Hoàn tiền
        if (in_array($trang_thai, ['Đã hủy', 'Hủy', 'Chờ hoàn tiền', 'Đã hoàn tiền'])) {
            $rel_stmt = $conn->prepare("UPDATE dat_san_slots SET trang_thai = 'released' WHERE dat_san_id = ?");
            if ($rel_stmt) {
                $rel_stmt->bind_param("i", $dat_san_id);
                $rel_stmt->execute();
                $rel_stmt->close();
            }
        }

        return $res;
    }

    /**
     * Xóa vĩnh viễn đơn đặt sân và bản ghi thanh toán liên quan
     */
    public static function deleteHard($dat_san_id)
    {
        $conn = Database::getConnection();
        // Xóa bản ghi slot CSDL
        $stmt0 = $conn->prepare("DELETE FROM dat_san_slots WHERE dat_san_id = ?");
        if ($stmt0) {
            $stmt0->bind_param("i", $dat_san_id);
            $stmt0->execute();
            $stmt0->close();
        }

        $stmt1 = $conn->prepare("DELETE FROM thanh_toan WHERE dat_san_id = ?");
        $stmt1->bind_param("i", $dat_san_id);
        $stmt1->execute();
        $stmt1->close();

        $stmt2 = $conn->prepare("DELETE FROM dat_san WHERE dat_san_id = ?");
        $stmt2->bind_param("i", $dat_san_id);
        $res = $stmt2->execute();
        $stmt2->close();
        return $res;
    }

    /**
     * Đếm số đơn chờ xác nhận
     */
    public static function countPending()
    {
        $conn = Database::getConnection();
        $res = $conn->query("SELECT COUNT(*) FROM dat_san WHERE trang_thai = 'Chờ xác nhận' AND deleted_at IS NULL");
        return (int)($res->fetch_row()[0] ?? 0);
    }

    /**
     * Đếm tổng số đơn
     */
    public static function countTotal()
    {
        $conn = Database::getConnection();
        $res = $conn->query("SELECT COUNT(*) FROM dat_san WHERE deleted_at IS NULL");
        return (int)($res->fetch_row()[0] ?? 0);
    }

    /**
     * Lấy danh sách các đơn chờ duyệt gần nhất
     */
    public static function getRecentPending(int $limit = 4): array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            SELECT ds.dat_san_id, ds.ngay_dat, ds.ten_san_con, nd.ho_ten, sb.ten_san, kg.gio_bat_dau, kg.gio_ket_thuc
            FROM dat_san ds
            JOIN nguoi_dung nd ON ds.nguoi_dung_id = nd.nguoi_dung_id
            JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
            JOIN san_bong sb ON kg.san_id = sb.san_id
            WHERE ds.trang_thai = 'Chờ xác nhận' AND ds.deleted_at IS NULL
            ORDER BY ds.dat_san_id DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }

    /**
     * Tính toán mức phạt điểm uy tín khi hủy đơn
     */
    public static function calculateCancellationPenalty(float $hoursUntilMatch, bool $isConfirmed, string $paymentMethod = 'tien_mat'): array
    {
        $penaltyPoints = 0;
        $penaltyMsg = "";
        if ($isConfirmed) {
            if ($hoursUntilMatch < 12.0) {
                $penaltyPoints = 15;
                $penaltyMsg = " Bạn bị trừ 15 điểm uy tín do hủy sân sát giờ (dưới 12 tiếng).";
            } elseif ($hoursUntilMatch <= 24.0) {
                $penaltyPoints = 5;
                $penaltyMsg = " Bạn bị trừ 5 điểm uy tín do hủy sân sát ngày (dưới 24 tiếng).";
            }
        }
        return [
            'penalty_points' => $penaltyPoints,
            'penalty_msg' => $penaltyMsg
        ];
    }

    /**
     * Kiểm tra xung đột khóa chéo sân ghép đa năng
     */
    public static function checkCompoundPitchConflict(string $targetSanCon, ?string $targetSanConKhoa, array $existingBookings): bool
    {
        foreach ($existingBookings as $b) {
            $bookedSanCon = $b['ten_san_con'] ?? '';
            $bookedKhoa = $b['san_con_khoa'] ?? null;

            if ($targetSanCon === $bookedSanCon) {
                return true;
            }

            if (!empty($targetSanConKhoa)) {
                $lockedList = array_map('trim', explode(',', $targetSanConKhoa));
                foreach ($lockedList as $part) {
                    if (stripos($bookedSanCon, $part) !== false) {
                        return true;
                    }
                }
            }

            if (!empty($bookedKhoa)) {
                $lockedList = array_map('trim', explode(',', $bookedKhoa));
                foreach ($lockedList as $part) {
                    if (stripos($targetSanCon, $part) !== false) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Lấy số giây giữ chỗ còn lại của đơn Chờ thanh toán
     */
    public static function getRemainingHoldTime(string $createdAt, int $holdDurationSeconds = 600): int
    {
        $elapsed = time() - strtotime($createdAt);
        return max(0, $holdDurationSeconds - $elapsed);
    }

    /**
     * Kiểm tra đơn đã hết hạn giữ chỗ hay chưa
     */
    public static function isHoldExpired(string $createdAt, string $status, int $holdDurationSeconds = 600): bool
    {
        if ($status !== 'Chờ thanh toán') {
            return false;
        }
        return (time() - strtotime($createdAt)) > $holdDurationSeconds;
    }
}
