<?php
namespace Controllers;

use Core\Controller;
use Config\Database;

require_once __DIR__ . '/../includes/send_mail.php';
require_once __DIR__ . '/../includes/email_templates.php';

class PaymentController extends Controller {

    public function index() {
        if (!isset($_SESSION['nguoi_dung_id'])) {
            $this->redirect('index.php?controller=Auth&action=login');
        }

        // Tự động hủy và giải phóng các đơn giữ chỗ quá 10 phút chưa thanh toán
        \Models\DatSan::autoCancelExpiredHolds(10);

        $dat_san_id = isset($_GET['dat_san_id']) ? intval($_GET['dat_san_id']) : 0;
        $nguoi_dung_id = $_SESSION['nguoi_dung_id'];
        
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            SELECT ds.*, sb.san_id, sb.ten_san, sb.dia_chi, kg.gio_bat_dau, kg.gio_ket_thuc, kg.gia_tien, kg.gia_tien_san_7, nd.ho_ten, nd.email, nd.sdt, nd.diem_uy_tin, nd.so_lan_bung_san 
            FROM dat_san ds
            JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
            JOIN san_bong sb ON kg.san_id = sb.san_id
            JOIN nguoi_dung nd ON ds.nguoi_dung_id = nd.nguoi_dung_id
            WHERE ds.dat_san_id=? AND ds.nguoi_dung_id=?
        ");
        $stmt->bind_param("ii", $dat_san_id, $nguoi_dung_id);
        $stmt->execute();
        $dat_san = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($dat_san && isset($dat_san['loai_san_dat']) && $dat_san['loai_san_dat'] === 'san_7' && !empty($dat_san['gia_tien_san_7'])) {
            $dat_san['gia_tien'] = $dat_san['gia_tien_san_7'];
        }
        
        if(!$dat_san){
            $_SESSION['flash_error'] = "Đơn đặt sân không tồn tại hoặc không hợp lệ.";
            $this->redirect('index.php');
        }

        $user_diem_uy_tin = (int)($dat_san['diem_uy_tin'] ?? 100);

        $stmt_check = $conn->prepare("SELECT * FROM thanh_toan WHERE dat_san_id = ?");
        $stmt_check->bind_param("i", $dat_san_id);
        $stmt_check->execute();
        $existing_payment = $stmt_check->get_result()->fetch_assoc();
        $stmt_check->close();

        // Kiểm tra thời hạn giữ chỗ 10 phút
        $created_time = strtotime($dat_san['ngay_tao']);
        $expire_time = $created_time + (10 * 60);
        $remaining_seconds = $expire_time - time();

        if ($remaining_seconds <= 0 && $dat_san['trang_thai'] === 'Chờ thanh toán' && !$existing_payment) {
            $stmt_cancel = $conn->prepare("UPDATE dat_san SET trang_thai = 'Đã hủy' WHERE dat_san_id = ?");
            $stmt_cancel->bind_param("i", $dat_san_id);
            $stmt_cancel->execute();
            $stmt_cancel->close();

            // Đồng bộ giải phóng slot cấp CSDL
            $stmt_rel = $conn->prepare("UPDATE dat_san_slots SET trang_thai = 'released' WHERE dat_san_id = ?");
            if ($stmt_rel) {
                $stmt_rel->bind_param("i", $dat_san_id);
                $stmt_rel->execute();
                $stmt_rel->close();
            }

            $dat_san['trang_thai'] = 'Đã hủy';
        }
        
        $bank_id = getenv('VIETQR_BANK_ID') ?: "MB";
        $bank_name = getenv('VIETQR_BANK_NAME') ?: "MB Bank";
        $stk = getenv('VIETQR_ACCOUNT_NO') ?: "000000000000";
        $chu_tk = getenv('VIETQR_ACCOUNT_NAME') ?: "SAN BONG PITCH";
        $noidung_ck = "DATSAN " . $dat_san_id;
        $so_tien_ck = (int)($dat_san['gia_tien'] ?? 0);
        $qr_url = \Services\VietQR::generateUrl($dat_san_id, $so_tien_ck, $bank_id, $stk, $chu_tk, 'qr_only.svg');

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$existing_payment) {
            if ($dat_san['trang_thai'] === 'Đã hủy') {
                $rebook_param = !empty($dat_san['san_id']) ? '&san_id=' . (int)$dat_san['san_id'] : '';
                $_SESSION['flash_error'] = 'Đơn đặt sân đã hết hạn giữ chỗ (10 phút)! Vui lòng chọn và đặt lại sân.';
                $this->redirect('index.php?controller=DatSan&action=book' . $rebook_param);
            }

            $phuong_thuc = $_POST['phuong_thuc'] ?? 'tien_mat';
            
            // Khóa phương thức tiền mặt nếu điểm uy tín dưới 60
            if ($phuong_thuc === 'tien_mat' && $user_diem_uy_tin < 60) {
                $this->render('payment/index', [
                    'dat_san' => $dat_san,
                    'dat_san_id' => $dat_san_id,
                    'existing_payment' => $existing_payment,
                    'remaining_seconds' => max(0, $remaining_seconds),
                    'bank_id' => $bank_id,
                    'bank_name' => $bank_name,
                    'stk' => $stk,
                    'chu_tk' => $chu_tk,
                    'noidung_ck' => $noidung_ck,
                    'so_tien_ck' => $so_tien_ck,
                    'qr_url' => $qr_url,
                    'error' => "⚠️ Điểm uy tín của bạn hiện tại là {$user_diem_uy_tin}/100 (Dưới 60đ). Bạn bắt buộc phải chọn Chuyển khoản VietQR!"
                ]);
                return;
            }

            if ($phuong_thuc === 'chuyen_khoan') {
                // Chuyển khoản: Đơn chờ quản trị viên đối soát tài khoản
                $tt_trang_thai = 'cho_xu_ly';
                $ds_trang_thai = 'Chờ xác nhận';
            } else {
                // Tiền mặt: Đơn chờ quản trị viên xác nhận tại sân
                $tt_trang_thai = 'cho_xu_ly';
                $ds_trang_thai = 'Chờ xác nhận';
            }

            // Lưu thông tin thanh toán
            $stmt2 = $conn->prepare("INSERT INTO thanh_toan (dat_san_id, phuong_thuc, trang_thai) VALUES (?, ?, ?)");
            $stmt2->bind_param("iss", $dat_san_id, $phuong_thuc, $tt_trang_thai);
            $stmt2->execute();
            $stmt2->close();

            // Cập nhật trạng thái dat_san thành Chờ xác nhận (Vẫn giữ chỗ, admin sẽ duyệt)
            $stmt_update_ds = $conn->prepare("UPDATE dat_san SET trang_thai = ? WHERE dat_san_id = ?");
            $stmt_update_ds->bind_param("si", $ds_trang_thai, $dat_san_id);
            $stmt_update_ds->execute();
            $stmt_update_ds->close();

            $existing_payment = ['phuong_thuc' => $phuong_thuc, 'trang_thai' => $tt_trang_thai];
            $dat_san['trang_thai'] = $ds_trang_thai;

            // Tạo thông báo
            $ngay_dat = date('d/m/Y', strtotime($dat_san['ngay_dat']));
            $gio_da = date('H:i', strtotime($dat_san['gio_bat_dau'])) . ' - ' . date('H:i', strtotime($dat_san['gio_ket_thuc']));
            $ten_san = $dat_san['ten_san'];
            $san_con = $dat_san['ten_san_con'];

            if ($phuong_thuc === 'chuyen_khoan') {
                $tb_title = "Yêu cầu thanh toán VietQR đơn #$dat_san_id";
                $tb_content = "Yêu cầu thanh toán chuyển khoản đơn $ten_san (Sân $san_con) ngày $ngay_dat lúc $gio_da đã được tiếp nhận. Đơn đang chờ Admin đối soát ngân hàng và xác nhận duyệt.";
            } else {
                $tb_title = "Giữ chỗ sân thành công #$dat_san_id";
                $tb_content = "Đơn đặt sân $ten_san (Sân $san_con) ngày $ngay_dat lúc $gio_da đã được giữ chỗ. Vui lòng thanh toán tiền mặt sau khi hoàn thành trận đấu.";
            }

            \Models\Notification::create(
                $nguoi_dung_id,
                $tb_title,
                $tb_content,
                'dat_san',
                "index.php?controller=User&action=history"
            );

            // Giải phóng session lock ngay lập tức để giải phóng tài nguyên trình duyệt
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }

            // Gửi email hóa đơn và gửi cho Admin
            try {
                if (!empty($dat_san['email']) && filter_var($dat_san['email'], FILTER_VALIDATE_EMAIL)) {
                    $emailSubject = "Xác nhận đặt sân thành công #" . $dat_san_id . " - Sân " . $ten_san;
                    $emailBody = getBookingEmailHtml($dat_san, 'confirmed');
                    sendEmail($dat_san['email'], $emailSubject, $emailBody);
                }

                $adminEmail = getenv('MAIL_ADMIN_NOTIFY') ?: 'alexanderhuy2@gmail.com';
                if (!empty($adminEmail) && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                    $adminSubject = "[ĐƠN ĐẶT SÂN MỚI #" . $dat_san_id . "] " . $dat_san['ho_ten'] . " - Sân " . $ten_san;
                    $adminBody = getAdminNewBookingEmailHtml($dat_san);
                    sendEmail($adminEmail, $adminSubject, $adminBody);
                }
            } catch (\Throwable $mailEx) {
                error_log("Mail error in PaymentController: " . $mailEx->getMessage());
            }
        }
        
        $this->render('payment/index', [
            'dat_san' => $dat_san,
            'dat_san_id' => $dat_san_id,
            'existing_payment' => $existing_payment,
            'remaining_seconds' => max(0, $remaining_seconds),
            'bank_id' => $bank_id,
            'bank_name' => $bank_name,
            'stk' => $stk,
            'chu_tk' => $chu_tk,
            'noidung_ck' => $noidung_ck,
            'so_tien_ck' => $so_tien_ck,
            'qr_url' => $qr_url
        ]);
    }

    /**
     * API kiểm tra trạng thái thanh toán theo thời gian thực (Polling)
     */
    public function checkStatus() {
        // Tự động hủy và giải phóng các đơn giữ chỗ quá 10 phút chưa thanh toán
        \Models\DatSan::autoCancelExpiredHolds(10);

        $dat_san_id = isset($_GET['dat_san_id']) ? intval($_GET['dat_san_id']) : 0;
        if ($dat_san_id <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Mã đơn không hợp lệ'], 400);
            return;
        }

        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            SELECT ds.dat_san_id, ds.trang_thai, tt.trang_thai as tt_trang_thai, tt.phuong_thuc 
            FROM dat_san ds 
            LEFT JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id 
            WHERE ds.dat_san_id = ?
        ");
        $stmt->bind_param("i", $dat_san_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$res) {
            $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy đơn'], 404);
            return;
        }

        $is_confirmed = ($res['trang_thai'] === 'Đã xác nhận');
        $is_paid = ($res['tt_trang_thai'] === 'da_thanh_toan');
        $is_cancelled = in_array($res['trang_thai'], ['Đã hủy', 'Hủy']);

        $this->jsonResponse([
            'success' => true,
            'dat_san_id' => $dat_san_id,
            'trang_thai' => $res['trang_thai'],
            'payment_status' => $res['tt_trang_thai'] ?? 'cho_xu_ly',
            'is_confirmed' => $is_confirmed,
            'is_paid' => $is_paid,
            'is_cancelled' => $is_cancelled
        ]);
    }

    /**
     * Webhook tự động nhận diện thanh toán VietQR (Tương thích SePAY & Casso)
     */
    public function webhook() {
        // 1. Kiểm tra xác thực Token nếu được cấu hình
        $configuredKey = getenv('SEPAY_API_KEY') ?: '';
        if (!empty($configuredKey)) {
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? $headers['Authorization'] ?? $headers['authorization'] ?? '';
            $expectedBearer = 'Bearer ' . $configuredKey;
            $expectedApiKey = 'Apikey ' . $configuredKey;
            if ($authHeader !== $expectedBearer && $authHeader !== $expectedApiKey && $authHeader !== $configuredKey) {
                $this->jsonResponse(['success' => false, 'message' => 'Unauthorized Webhook Token'], 401);
                return;
            }
        }

        // 2. Đọc payload JSON
        $rawInput = file_get_contents('php://input');
        $payload = json_decode($rawInput, true);

        if (!$payload && !empty($_POST)) {
            $payload = $_POST;
        }

        if (empty($payload)) {
            $this->jsonResponse(['success' => false, 'message' => 'Empty webhook payload'], 400);
            return;
        }

        // 3. Trích xuất danh sách giao dịch (hỗ trợ cả SePAY đơn lẻ lẫn Casso mảng)
        $transactions = [];
        if (isset($payload['data']) && is_array($payload['data'])) {
            // Casso format
            $transactions = $payload['data'];
        } else {
            // SePAY format hoặc giao dịch đơn lẻ
            $transactions = [$payload];
        }

        $processedCount = 0;
        $conn = Database::getConnection();

        foreach ($transactions as $tx) {
            $amount = (int)($tx['transferAmount'] ?? $tx['amount'] ?? 0);
            $content = $tx['content'] ?? $tx['description'] ?? '';

            if (empty($content) || $amount <= 0) {
                continue;
            }

            // Tìm mã đơn DATSAN {id} hoặc DATSAN_{id}
            if (!preg_match('/DATSAN[_\s]*(\d+)/i', $content, $matches)) {
                continue;
            }

            $dat_san_id = (int)$matches[1];
            if ($dat_san_id <= 0) {
                continue;
            }

            // Xử lý đơn an toàn với Transaction và Khóa mức hàng
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("
                    SELECT ds.*, sb.ten_san, sb.dia_chi, kg.gio_bat_dau, kg.gio_ket_thuc, kg.gia_tien, kg.gia_tien_san_7,
                           nd.ho_ten, nd.email, nd.sdt
                    FROM dat_san ds
                    JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
                    JOIN san_bong sb ON kg.san_id = sb.san_id
                    JOIN nguoi_dung nd ON ds.nguoi_dung_id = nd.nguoi_dung_id
                    WHERE ds.dat_san_id = ? FOR UPDATE
                ");
                $stmt->bind_param("i", $dat_san_id);
                $stmt->execute();
                $booking = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$booking) {
                    $conn->rollback();
                    continue;
                }

                $gia_dung = ($booking['loai_san_dat'] === 'san_7' && !empty($booking['gia_tien_san_7'])) 
                            ? (int)$booking['gia_tien_san_7'] 
                            : (int)$booking['gia_tien'];

                // Chỉ duyệt nếu số tiền chuyển khoản >= giá tiền sân
                if ($amount >= $gia_dung && in_array($booking['trang_thai'], ['Chờ thanh toán', 'Chờ xác nhận'])) {
                    // Cập nhật trạng thái đặt sân
                    $stmt_up = $conn->prepare("UPDATE dat_san SET trang_thai = 'Đã xác nhận' WHERE dat_san_id = ?");
                    $stmt_up->bind_param("i", $dat_san_id);
                    $stmt_up->execute();
                    $stmt_up->close();

                    // Cập nhật trạng thái thanh toán
                    $stmt_tt = $conn->prepare("
                        INSERT INTO thanh_toan (dat_san_id, phuong_thuc, trang_thai) 
                        VALUES (?, 'chuyen_khoan', 'da_thanh_toan')
                        ON DUPLICATE KEY UPDATE phuong_thuc = 'chuyen_khoan', trang_thai = 'da_thanh_toan'
                    ");
                    $stmt_tt->bind_param("i", $dat_san_id);
                    $stmt_tt->execute();
                    $stmt_tt->close();

                    // Gửi thông báo hệ thống cho User
                    if (class_exists('\\Models\\Notification')) {
                        \Models\Notification::create(
                            (int)$booking['nguoi_dung_id'],
                            'Thanh toán VietQR tự động thành công!',
                            "Đơn đặt sân #{$dat_san_id} tại {$booking['ten_san']} đã được hệ thống tự động xác nhận qua VietQR.",
                            'xac_nhan',
                            'index.php?controller=User&action=history'
                        );
                    }

                    // Ghi nhật ký hệ thống AuditLog
                    if (class_exists('\\Models\\AuditLog')) {
                        \Models\AuditLog::log(null, 'AUTO_CONFIRM_VIETQR_WEBHOOK', 'dat_san', $dat_san_id, "Tự động duyệt qua Webhook. Số tiền: " . number_format($amount) . " đ");
                    }

                    $conn->commit();
                    $processedCount++;

                    // Gửi email hóa đơn trong background
                    try {
                        if (!empty($booking['email']) && filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {
                            $subject = "Xác nhận thanh toán tự động #" . $dat_san_id . " - Sân " . $booking['ten_san'];
                            $body = getBookingEmailHtml($booking, 'confirmed');
                            sendEmail($booking['email'], $subject, $body);
                        }
                    } catch (\Throwable $mEx) {
                        error_log("Mail error in Webhook: " . $mEx->getMessage());
                    }
                } else {
                    $conn->rollback();
                }
            } catch (\Throwable $ex) {
                $conn->rollback();
                error_log("Webhook transaction error: " . $ex->getMessage());
            }
        }

        $this->jsonResponse([
            'success' => true,
            'processed' => $processedCount,
            'message' => "Đã xử lý {$processedCount} giao dịch qua Webhook"
        ]);
    }

    /**
     * Giả lập bắn Webhook thanh toán (Cách 1 - Miễn phí 0đ cho Developer & Tester)
     */
    public function simulateWebhook() {
        $dat_san_id = isset($_REQUEST['dat_san_id']) ? intval($_REQUEST['dat_san_id']) : 0;
        if ($dat_san_id <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Mã đơn không hợp lệ'], 400);
            return;
        }

        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            SELECT ds.*, kg.gia_tien, kg.gia_tien_san_7 
            FROM dat_san ds
            JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
            WHERE ds.dat_san_id = ?
        ");
        $stmt->bind_param("i", $dat_san_id);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$booking) {
            $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy đơn đặt sân'], 404);
            return;
        }

        $amount = ($booking['loai_san_dat'] === 'san_7' && !empty($booking['gia_tien_san_7'])) 
                  ? (int)$booking['gia_tien_san_7'] 
                  : (int)$booking['gia_tien'];

        // Cập nhật trạng thái
        $conn->begin_transaction();
        try {
            $u1 = $conn->prepare("UPDATE dat_san SET trang_thai = 'Đã xác nhận' WHERE dat_san_id = ?");
            $u1->bind_param("i", $dat_san_id);
            $u1->execute();
            $u1->close();

            $u2 = $conn->prepare("
                INSERT INTO thanh_toan (dat_san_id, phuong_thuc, trang_thai) 
                VALUES (?, 'chuyen_khoan', 'da_thanh_toan')
                ON DUPLICATE KEY UPDATE phuong_thuc = 'chuyen_khoan', trang_thai = 'da_thanh_toan'
            ");
            $u2->bind_param("i", $dat_san_id);
            $u2->execute();
            $u2->close();

            // Thông báo
            if (class_exists('\\Models\\Notification')) {
                \Models\Notification::create(
                    (int)$booking['nguoi_dung_id'],
                    'Thanh toán giả lập thành công (Test Mock 0đ)!',
                    "Đơn đặt sân #{$dat_san_id} đã được kích hoạt thành công qua chế độ Giả lập Webhook.",
                    'xac_nhan',
                    'index.php?controller=User&action=history'
                );
            }

            $conn->commit();
            $this->jsonResponse([
                'success' => true,
                'message' => "🧪 [GIẢ LẬP SEPAY 0Đ] Đã xác nhận đơn #{$dat_san_id} với số tiền " . number_format($amount) . " đ thành công!"
            ]);
        } catch (\Throwable $e) {
            $conn->rollback();
            $this->jsonResponse(['success' => false, 'message' => 'Lỗi khi kích hoạt giả lập: ' . $e->getMessage()], 500);
        }
    }

    /**
     * In phiếu đặt sân / Xuất PDF (Hóa đơn bán lẻ chuẩn)
     */
    public function receipt() {
        if (!isset($_SESSION['nguoi_dung_id'])) {
            $this->redirect('index.php?controller=Auth&action=login');
        }

        $dat_san_id = isset($_GET['dat_san_id']) ? intval($_GET['dat_san_id']) : 0;
        $conn = Database::getConnection();

        $stmt = $conn->prepare("
            SELECT ds.*, sb.ten_san, sb.dia_chi, kg.gio_bat_dau, kg.gio_ket_thuc, kg.gia_tien, kg.gia_tien_san_7,
                   nd.ho_ten, nd.email, nd.sdt,
                   tt.phuong_thuc, tt.trang_thai as tt_trang_thai, tt.ngay_thanh_toan
            FROM dat_san ds
            JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
            JOIN san_bong sb ON kg.san_id = sb.san_id
            JOIN nguoi_dung nd ON ds.nguoi_dung_id = nd.nguoi_dung_id
            LEFT JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
            WHERE ds.dat_san_id = ?
        ");
        $stmt->bind_param("i", $dat_san_id);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$booking) {
            $_SESSION['flash_error'] = 'Không tìm thấy hóa đơn đơn đặt sân này.';
            $this->redirect('index.php?controller=User&action=history');
        }

        // Kiểm tra quyền (chỉ chủ đơn hoặc admin mới được in)
        $isAdmin = isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'admin';
        if ($booking['nguoi_dung_id'] != $_SESSION['nguoi_dung_id'] && !$isAdmin) {
            $_SESSION['flash_error'] = 'Bạn không có quyền xem hóa đơn này.';
            $this->redirect('index.php?controller=User&action=history');
        }

        $this->render('payment/receipt', [
            'booking' => $booking
        ]);
    }

    /**
     * Xuất hóa đơn đơn lẻ dạng Excel (.csv UTF-8 BOM)
     */
    public function exportReceiptExcel() {
        if (!isset($_SESSION['nguoi_dung_id'])) {
            $this->redirect('index.php?controller=Auth&action=login');
        }

        $dat_san_id = isset($_GET['dat_san_id']) ? intval($_GET['dat_san_id']) : 0;
        $conn = Database::getConnection();

        $stmt = $conn->prepare("
            SELECT ds.*, sb.ten_san, sb.dia_chi, kg.gio_bat_dau, kg.gio_ket_thuc, kg.gia_tien, kg.gia_tien_san_7,
                   nd.ho_ten, nd.email, nd.sdt,
                   tt.phuong_thuc, tt.trang_thai as tt_trang_thai, tt.ngay_thanh_toan
            FROM dat_san ds
            JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
            JOIN san_bong sb ON kg.san_id = sb.san_id
            JOIN nguoi_dung nd ON ds.nguoi_dung_id = nd.nguoi_dung_id
            LEFT JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
            WHERE ds.dat_san_id = ?
        ");
        $stmt->bind_param("i", $dat_san_id);
        $stmt->execute();
        $b = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$b) {
            exit('Không tìm thấy đơn đặt sân');
        }

        $filename = "Hoa_Don_Dat_San_" . $dat_san_id . "_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Xuất BOM UTF-8 để mở tiếng Việt không lỗi font trên Excel
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');
        fputcsv($output, ['PHIẾU XÁC NHẬN ĐẶT SÂN BÓNG ĐÁ - PITCHMANAGER']);
        fputcsv($output, ['Ngày xuất file', date('d/m/Y H:i:s')]);
        fputcsv($output, []);
        fputcsv($output, ['Mã đơn đặt sân', '#' . $b['dat_san_id']]);
        fputcsv($output, ['Khách hàng', $b['ho_ten']]);
        fputcsv($output, ['Số điện thoại', $b['sdt']]);
        fputcsv($output, ['Email', $b['email']]);
        fputcsv($output, ['Tên sân bóng', $b['ten_san']]);
        fputcsv($output, ['Sân con', $b['ten_san_con'] ?: 'Sân tiêu chuẩn']);
        fputcsv($output, ['Loại sân', ($b['loai_san_dat'] === 'san_7') ? 'Sân ghép 7 người' : 'Sân 5 người']);
        fputcsv($output, ['Địa chỉ', $b['dia_chi']]);
        fputcsv($output, ['Ngày thi đấu', date('d/m/Y', strtotime($b['ngay_dat']))]);
        fputcsv($output, ['Khung giờ', date('H:i', strtotime($b['gio_bat_dau'])) . ' - ' . date('H:i', strtotime($b['gio_ket_thuc']))]);
        
        $gia = ($b['loai_san_dat'] === 'san_7' && !empty($b['gia_tien_san_7'])) ? $b['gia_tien_san_7'] : $b['gia_tien'];
        fputcsv($output, ['Tổng tiền (VNĐ)', number_format($gia)]);
        fputcsv($output, ['Phương thức thanh toán', ($b['phuong_thuc'] === 'chuyen_khoan') ? 'Chuyển khoản VietQR' : 'Tiền mặt']);
        fputcsv($output, ['Trạng thái đơn', $b['trang_thai']]);
        fputcsv($output, ['Trạng thái thanh toán', ($b['tt_trang_thai'] === 'da_thanh_toan') ? 'Đã thanh toán' : 'Chờ xử lý']);
        fclose($output);
        exit();
    }
}
