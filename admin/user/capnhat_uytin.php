<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_admin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
    exit();
}

// Kiểm tra CSRF token
$token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verify_csrf_token($token)) {
    echo json_encode(['success' => false, 'message' => 'Mã bảo mật CSRF không hợp lệ']);
    exit();
}

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$action_type = $_POST['action_type'] ?? 'adjust'; // 'reset_100' hoặc 'adjust'
$new_score = isset($_POST['new_score']) ? intval($_POST['new_score']) : 100;
$reason = trim($_POST['reason'] ?? '');

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID người dùng không hợp lệ']);
    exit();
}

// Lấy thông tin user hiện tại
$stmt_curr = $conn->prepare("SELECT nguoi_dung_id, ho_ten, diem_uy_tin FROM nguoi_dung WHERE nguoi_dung_id = ?");
$stmt_curr->bind_param("i", $user_id);
$stmt_curr->execute();
$current_user = $stmt_curr->get_result()->fetch_assoc();
$stmt_curr->close();

if (!$current_user) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy người dùng']);
    exit();
}

$old_score = (int)($current_user['diem_uy_tin'] ?? 100);

if ($action_type === 'reset_100') {
    $target_score = 100;
    $change_points = $target_score - $old_score;
    $log_reason = "Quản trị viên khôi phục điểm uy tín về 100đ";
    
    $stmt_up = $conn->prepare("UPDATE nguoi_dung SET diem_uy_tin = 100, so_lan_bung_san = 0 WHERE nguoi_dung_id = ?");
    $stmt_up->bind_param("i", $user_id);
    $stmt_up->execute();
    $stmt_up->close();
} else {
    // Điều chỉnh số điểm cụ thể
    $target_score = max(0, min(100, $new_score));
    $change_points = $target_score - $old_score;
    if (empty($reason)) {
        $reason = "Admin điều chỉnh điểm uy tín";
    }
    $log_reason = "Admin điều chỉnh: " . $reason;

    $stmt_up = $conn->prepare("UPDATE nguoi_dung SET diem_uy_tin = ? WHERE nguoi_dung_id = ?");
    $stmt_up->bind_param("ii", $target_score, $user_id);
    $stmt_up->execute();
    $stmt_up->close();
}

// Ghi lịch sử uy tín
$stmt_log = $conn->prepare("INSERT INTO lich_su_uy_tin (nguoi_dung_id, so_diem, diem_sau_cung, ghi_chu, ngay_tao) VALUES (?, ?, ?, ?, NOW())");
$stmt_log->bind_param("iiis", $user_id, $change_points, $target_score, $log_reason);
$stmt_log->execute();
$stmt_log->close();

if (class_exists('\\Models\\AuditLog')) {
    \Models\AuditLog::log($_SESSION['nguoi_dung_id'] ?? null, 'ADMIN_CHANGE_USER_REPUTATION', 'nguoi_dung', $user_id, "{$log_reason} (từ {$old_score}đ thành {$target_score}đ)");
}

echo json_encode([
    'success' => true,
    'message' => "Đã cập nhật điểm uy tín của {$current_user['ho_ten']} thành {$target_score}đ thành công!",
    'new_score' => $target_score
]);
exit();
