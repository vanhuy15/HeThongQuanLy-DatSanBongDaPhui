<?php
namespace Controllers;

use Core\Controller;
use Models\Notification;

class NotificationController extends Controller {

    /**
     * Lấy danh sách thông báo mới nhất qua AJAX
     */
    public function getLatest() {
        if (!isset($_SESSION['nguoi_dung_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
            return;
        }

        $userId = $_SESSION['nguoi_dung_id'];
        $notifications = Notification::getByUser($userId, 10);
        $unreadCount = Notification::getUnreadCount($userId);

        $this->jsonResponse([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc
     */
    public function markRead() {
        if (!isset($_SESSION['nguoi_dung_id'])) {
            $this->jsonResponse(['success' => false], 401);
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $userId = $_SESSION['nguoi_dung_id'];

        if ($id > 0) {
            Notification::markAsRead($id, $userId);
        }

        $this->jsonResponse([
            'success' => true,
            'unread_count' => Notification::getUnreadCount($userId)
        ]);
    }

    /**
     * Đánh dấu tất cả là đã đọc
     */
    public function markAllRead() {
        if (!isset($_SESSION['nguoi_dung_id'])) {
            $this->jsonResponse(['success' => false], 401);
            return;
        }

        $userId = $_SESSION['nguoi_dung_id'];
        Notification::markAllAsRead($userId);

        $this->jsonResponse([
            'success' => true,
            'unread_count' => 0
        ]);
    }

    /**
     * API đếm số đơn chờ duyệt cho Admin (Polling thời gian thực)
     */
    public function getAdminPendingCount() {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $this->jsonResponse(['success' => false, 'count' => 0], 403);
            return;
        }

        $pending_confirm = \Models\DatSan::countPending(); // Đơn 'Chờ xác nhận' (tiền mặt / chuyển khoản)
        $conn = \Core\Database::getConnection();
        $res_rf = $conn->query("SELECT COUNT(*) FROM dat_san WHERE trang_thai = 'Chờ hoàn tiền' AND deleted_at IS NULL");
        $pending_refund = (int)($res_rf->fetch_row()[0] ?? 0);
        $total_count = $pending_confirm + $pending_refund;

        $this->jsonResponse([
            'success' => true,
            'count' => $total_count,
            'pending_confirm' => $pending_confirm,
            'pending_refund' => $pending_refund
        ]);
    }
}
