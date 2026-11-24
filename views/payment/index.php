<?php
/**
 * View Thanh toán đặt sân (Pure PHP MVC)
 *
 * @var array $dat_san
 * @var int $dat_san_id
 * @var array|null $existing_payment
 * @var int|null $remaining_seconds
 * @var string|null $error
 * @var bool|null $is_cash_locked
 * @var string|null $bank_id
 * @var string|null $bank_name
 * @var string|null $stk
 * @var string|null $chu_tk
 * @var string|null $noidung_ck
 * @var int|null $so_tien_ck
 * @var string|null $qr_url
 */

// Fallback an toàn cho static analyzer và view rendering
$dat_san = $dat_san ?? [];
$dat_san_id = $dat_san_id ?? (int)($_GET['dat_san_id'] ?? 0);
$existing_payment = $existing_payment ?? null;
$remaining_seconds = $remaining_seconds ?? 0;
$error = $error ?? null;
$is_cash_locked = $is_cash_locked ?? false;
$bank_id = $bank_id ?? (getenv('VIETQR_BANK_ID') ?: "MB");
$bank_name = $bank_name ?? (getenv('VIETQR_BANK_NAME') ?: "MB Bank");
$stk = $stk ?? (getenv('VIETQR_ACCOUNT_NO') ?: "000000000000");
$chu_tk = $chu_tk ?? (getenv('VIETQR_ACCOUNT_NAME') ?: "SAN BONG PITCH");
$noidung_ck = $noidung_ck ?? ("DATSAN " . $dat_san_id);
$so_tien_ck = $so_tien_ck ?? (int)($dat_san['gia_tien'] ?? 0);

if (empty($qr_url) && class_exists('\Services\VietQR')) {
    $qr_url = \Services\VietQR::generateUrl($dat_san_id, $so_tien_ck, $bank_id, $stk, $chu_tk, 'qr_only.svg');
} else {
    $qr_url = $qr_url ?? '';
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/menu.php';
?>

<style>
/* ĐẢM BẢO LAYOUT NGANG 2 CỘT 100% KHÔNG BỊ TRÌNH DUYỆT CACHE */
.thanhtoan-container.thanhtoan-horizontal {
    max-width: 1100px !important;
    width: 95% !important;
    margin: 2rem auto !important;
    background: #ffffff !important;
    border-radius: 20px !important;
    padding: 2.25rem 2.5rem !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.08) !important;
    border: 1px solid #e2e8f0 !important;
    text-align: left !important;
    box-sizing: border-box !important;
}
.thanhtoan-header {
    text-align: center;
    margin-bottom: 2rem;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 1.25rem;
}
.thanhtoan-header h2 {
    margin: 0 0 0.5rem 0;
    color: #0f172a;
    font-size: 1.7rem;
    font-weight: 800;
}
.pitch-name-badge {
    display: inline-block;
    background: #f0fdfa;
    color: #0f766e;
    font-weight: 700;
    font-size: 0.95rem;
    padding: 6px 18px;
    border-radius: 30px;
    border: 1px solid #ccfbf1;
}

/* 2-Column Grid Layout */
.thanhtoan-layout-grid {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 2.5rem !important;
    align-items: start !important;
}
@media (max-width: 960px) {
    .thanhtoan-layout-grid {
        grid-template-columns: 1fr !important;
        gap: 1.75rem !important;
    }
    .thanhtoan-container.thanhtoan-horizontal {
        padding: 1.5rem 1.25rem !important;
        margin: 1rem auto !important;
    }
}

/* Left Column */
.payment-left-col {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.payment-countdown {
    background: #fffbeb;
    border: 1.5px solid #fde68a;
    border-radius: 12px;
    padding: 12px 18px;
    color: #92400e;
    font-weight: 600;
    font-size: 0.92rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 0;
}
.payment-countdown strong {
    color: #dc2626;
    font-size: 1.2rem;
    font-weight: 800;
    font-family: monospace;
    letter-spacing: 1.5px;
}
.thongtin-datsan {
    background: #f8fafc;
    padding: 1.25rem 1.5rem;
    border-radius: 14px;
    font-size: 0.95rem;
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
    margin: 0;
    max-width: 100%;
}
.booking-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px dashed #e2e8f0;
}
.booking-info-row.price-row {
    border-bottom: none;
    padding-top: 10px;
    margin-top: 4px;
    border-top: 1.5px solid #e2e8f0;
}
.booking-info-row .info-label {
    color: #64748b;
    font-weight: 500;
    font-size: 0.92rem;
}
.booking-info-row .info-value {
    color: #1e293b;
    font-weight: 700;
    font-size: 0.96rem;
}
.price-highlight {
    color: #dc2626;
    font-weight: 800;
    font-size: 1.35rem;
}
.payment-selection-box {
    margin-top: 0.25rem;
}
.payment-method-title {
    margin: 0 0 0.85rem 0;
    color: #1e293b;
    font-size: 1rem;
    font-weight: 700;
}
.payment-methods {
    display: flex;
    gap: 0.85rem;
    margin-bottom: 0;
}
.payment-card {
    flex: 1;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.85rem 0.75rem;
    min-height: 75px;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    background: #ffffff;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 0;
    user-select: none;
}
.payment-card:hover:not(.locked) {
    border-color: #0d9488;
    background: #f0fdfa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 148, 136, 0.12);
}
.payment-card.selected {
    border-color: #0d9488 !important;
    background-color: #f0fdfa !important;
    box-shadow: 0 4px 14px rgba(13, 148, 136, 0.18);
    transform: translateY(-2px);
}
.payment-card.locked {
    border: 2px dashed #fca5a5;
    background: #fef2f2;
    opacity: 0.8;
    cursor: not-allowed;
}
.payment-card-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
}
.payment-card-icon {
    font-size: 1.45rem;
    color: #64748b;
    transition: all 0.2s;
}
.payment-card.selected .payment-card-icon {
    color: #0d9488;
}
.payment-card-title {
    display: block;
    font-size: 0.95rem;
    color: #1e293b;
    font-weight: 700;
}
.payment-card.selected .payment-card-title {
    color: #0d9488;
}

/* Right Column */
.payment-right-col {
    min-height: 380px;
}
.unselected-guide-box {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 16px;
    padding: 3rem 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    min-height: 380px;
    box-sizing: border-box;
}
.guide-hand-icon {
    font-size: 2.8rem;
    animation: bounceLeft 1.5s infinite;
}
@keyframes bounceLeft {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(-8px); }
}
.unselected-guide-box h4 {
    margin: 0;
    color: #0f172a;
    font-size: 1.25rem;
    font-weight: 700;
}
.unselected-guide-box p {
    margin: 0;
    color: #64748b;
    font-size: 0.92rem;
    line-height: 1.6;
    max-width: 320px;
}
.qr-container-horizontal {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.75rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    box-sizing: border-box;
    animation: fadeIn 0.25s ease;
}
.qr-frame-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
.qr-frame {
    background: #ffffff;
    padding: 12px;
    border-radius: 14px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.qr-image {
    width: 220px;
    height: 220px;
    object-fit: contain;
    border-radius: 6px;
    display: block;
}
.qr-pulse-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.84rem;
    color: #0f766e;
    background: #ccfbf1;
    padding: 4px 14px;
    border-radius: 20px;
    font-weight: 600;
}
.pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #0d9488;
    box-shadow: 0 0 0 0 rgba(13, 148, 136, 0.7);
    animation: pulseRing 1.5s infinite cubic-bezier(0.66, 0, 0, 1);
}
@keyframes pulseRing {
    0% { box-shadow: 0 0 0 0 rgba(13, 148, 136, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(13, 148, 136, 0); }
    100% { box-shadow: 0 0 0 0 rgba(13, 148, 136, 0); }
}
.qr-detail-box {
    width: 100%;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 18px;
    box-sizing: border-box;
}
.highlight-noidung {
    background: #f0fdfa;
    margin: 4px -6px -4px -6px;
    padding: 8px 10px !important;
    border-radius: 8px;
    border-bottom: none !important;
}
.cash-guide-panel {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 2.5rem 1.75rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    min-height: 380px;
    box-sizing: border-box;
    animation: fadeIn 0.25s ease;
}
.cash-icon-circle {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    font-size: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cash-guide-panel h4 {
    margin: 0;
    font-size: 1.25rem;
    color: #0f172a;
    font-weight: 700;
}
.cash-guide-panel p {
    margin: 0;
    font-size: 0.92rem;
    color: #475569;
    line-height: 1.6;
}
.cash-warning-note {
    background: #fffbeb;
    border: 1px solid #fef3c7;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 0.85rem;
    color: #92400e;
    text-align: left;
    line-height: 1.5;
}
</style>

<div class="thanhtoan-container thanhtoan-horizontal">
    <div class="thanhtoan-header">
        <h2>Thanh toán đặt sân</h2>
        <div class="pitch-name-badge">
            <?php echo (stripos($dat_san['ten_san'] ?? '', 'sân') === 0) ? htmlspecialchars($dat_san['ten_san']) : 'Sân ' . htmlspecialchars($dat_san['ten_san'] ?? ''); ?>
            <?php if (!empty($dat_san['ten_san_con'])): ?>
                • Sân con <?php echo htmlspecialchars($dat_san['ten_san_con']); ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($dat_san['trang_thai'] === 'Đã hủy'): ?>
        <div class="payment-expired-card" style="text-align: center; padding: 2rem 1.5rem;">
            <h3 style="color: #b91c1c; margin-bottom: 0.5rem;">Đơn đặt sân đã bị hủy</h3>
            <p style="color: #64748b; margin: 0;">Đơn đặt sân #<?php echo $dat_san_id; ?> đã quá thời gian giữ chỗ 10 phút chưa hoàn tất thanh toán.</p>
        </div>
    <?php else: ?>
        <!-- 10-Minute Countdown Banner -->
        <?php if (!empty($error)): ?>
            <div class="error" style="margin-bottom: 20px;">
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!$existing_payment): ?>
            <?php
            $user_diem_uy_tin = (int)($dat_san['diem_uy_tin'] ?? 100);
            $is_cash_locked = ($user_diem_uy_tin < 60);
            ?>
            <form method="POST" id="formThanhToan" class="thanhtoan-layout-grid">
                <?php echo function_exists("csrf_field") ? csrf_field() : ""; ?>

                <!-- CỘT TRÁI: THÔNG TIN ĐƠN, ĐẾM NGƯỢC & PHƯƠNG THỨC -->
                <div class="payment-left-col">
                    <div class="payment-countdown">
                        <i class="fas fa-clock" style="font-size: 1.1rem; color: #ea580c;"></i>
                        <span>Thời gian giữ chỗ còn lại: <strong id="countdownTimer" data-remaining-seconds="<?php echo max(0, (int)($remaining_seconds ?? 0)); ?>" data-cash-locked="<?php echo (!empty($is_cash_locked) && $is_cash_locked) ? '1' : '0'; ?>">--:--</strong></span>
                    </div>

                    <div class="thongtin-datsan">
                        <div class="booking-info-row">
                            <span class="info-label">Ngày đá:</span>
                            <span class="info-value"><?php echo date("d/m/Y", strtotime($dat_san['ngay_dat'])); ?></span>
                        </div>
                        <div class="booking-info-row">
                            <span class="info-label">Khung giờ:</span>
                            <span class="info-value"><?php echo date("H:i", strtotime($dat_san['gio_bat_dau'])) . " - " . date("H:i", strtotime($dat_san['gio_ket_thuc'])); ?></span>
                        </div>
                        <div class="booking-info-row">
                            <span class="info-label">Loại sân:</span>
                            <span class="info-value"><?php echo ($dat_san['loai_san_dat'] === 'san_7') ? 'Sân 7 người (Ghép)' : 'Sân 5 người'; ?></span>
                        </div>
                        <div class="booking-info-row price-row">
                            <span class="info-label">Tổng thanh toán:</span>
                            <span class="price-highlight"><?php echo number_format($dat_san['gia_tien']); ?> VNĐ</span>
                        </div>
                    </div>

                    <div class="payment-selection-box">
                        <h3 class="payment-method-title">Chọn hình thức thanh toán:</h3>
                        <div class="payment-methods">
                            <?php if ($is_cash_locked): ?>
                                <div class="payment-card locked" id="cardTienMat" onclick="selectPaymentMethod('tien_mat')" title="Khóa do điểm uy tín dưới 60">
                                    <input type="radio" name="phuong_thuc" id="radioTienMat" value="tien_mat" disabled style="display: none;">
                                    <div class="payment-card-content">
                                        <i class="fas fa-ban payment-card-icon" style="color: #ef4444;"></i>
                                        <strong class="payment-card-title" style="color: #991b1b;">Tiền mặt</strong>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="payment-card" id="cardTienMat" onclick="selectPaymentMethod('tien_mat')">
                                    <input type="radio" name="phuong_thuc" id="radioTienMat" value="tien_mat" style="display: none;">
                                    <div class="payment-card-content">
                                        <i class="fas fa-money-bill-wave payment-card-icon"></i>
                                        <strong class="payment-card-title">Tiền mặt</strong>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="payment-card" id="cardChuyenKhoan" onclick="selectPaymentMethod('chuyen_khoan')">
                                <input type="radio" name="phuong_thuc" id="radioChuyenKhoan" value="chuyen_khoan" style="display: none;">
                                <div class="payment-card-content">
                                    <i class="fas fa-qrcode payment-card-icon"></i>
                                    <strong class="payment-card-title">Chuyển khoản</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nút xác nhận tiền mặt (chỉ hiện khi chọn tiền mặt, căn giữa) -->
                    <div id="submitBtnWrapper" style="display:none; margin-top: 1.25rem; text-align: center;">
                        <button type="submit" id="btnSubmitPayment" class="btn-submit" style="background: #0d9488; min-width: 220px; padding: 12px 28px; border-radius: 12px; font-weight: 700; font-size: 0.95rem; border: none; color: white; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(13, 148, 136, 0.25); margin: 0 auto;">
                            <span id="submitBtnText">Xác nhận đặt sân</span>
                        </button>
                    </div>
                </div>

                <!-- CỘT PHẢI: KHUNG VIETQR & THÔNG TIN CHUYỂN KHOẢN HOẶC HƯỚNG DẪN -->
                <div class="payment-right-col">
                    <!-- Trạng thái khi chưa chọn phương thức -->
                    <div id="emptyGuideBox" class="unselected-guide-box">
                        <div class="guide-hand-icon">👉</div>
                        <h4>Vui lòng chọn hình thức thanh toán</h4>
                        <p>Hãy nhấp chọn <strong>Tiền mặt</strong> hoặc <strong>Chuyển khoản</strong> ở cột bên cạnh để tiếp tục.</p>
                    </div>

                    <!-- Khung VietQR khi chọn QR -->
                    <div id="chuyenKhoanBox" style="display:none;">
                        <?php
                        if (empty($qr_url) && class_exists('\Services\VietQR')) {
                            $qr_url = \Services\VietQR::generateUrl($dat_san_id, $so_tien_ck, $bank_id, $stk, $chu_tk, 'qr_only.svg');
                        }
                        ?>
                        <div class="qr-container-horizontal">
                            <div class="qr-frame-wrapper">
                                <div class="qr-frame">
                                    <img src="<?php echo $qr_url; ?>" alt="Mã QR Chuyển khoản" class="qr-image" loading="eager" crossorigin="anonymous" draggable="true" title="Quét mã VietQR thanh toán tự động">
                                </div>
                            </div>

                            <!-- Thông tin chuyển khoản chi tiết nếu khách không quét được QR -->
                            <div class="qr-detail-box">
                                <div class="qr-detail-row">
                                    <span class="lbl">Ngân hàng:</span>
                                    <strong class="val"><?php echo htmlspecialchars($bank_name); ?></strong>
                                </div>
                                <div class="qr-detail-row">
                                    <span class="lbl">Số tài khoản:</span>
                                    <div class="qr-val-with-action">
                                        <strong id="stkText" class="val val-stk"><?php echo htmlspecialchars($stk); ?></strong>
                                        <button type="button" class="qr-copy-btn" onclick="copyText('stkText', this)" title="Sao chép số tài khoản">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="qr-detail-row">
                                    <span class="lbl">Chủ tài khoản:</span>
                                    <strong class="val" style="text-transform: uppercase;"><?php echo htmlspecialchars($chu_tk); ?></strong>
                                </div>
                                <div class="qr-detail-row">
                                    <span class="lbl">Số tiền:</span>
                                    <strong class="val val-amount"><?php echo number_format($so_tien_ck); ?> VNĐ</strong>
                                </div>
                                <div class="qr-detail-row highlight-noidung">
                                    <span class="lbl">Nội dung CK:</span>
                                    <div class="qr-val-with-action">
                                        <strong id="noiDungText" class="val val-noidung"><?php echo htmlspecialchars($noidung_ck); ?></strong>
                                        <button type="button" class="qr-copy-btn" onclick="copyText('noiDungText', this)" title="Sao chép nội dung chuyển khoản">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Box hướng dẫn thanh toán khi chọn Tiền mặt -->
                    <div id="cashGuideBox" style="display:none;" class="cash-guide-panel">
                        <div class="cash-icon-circle">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h4>Thanh toán tiền mặt</h4>
                        <p>Thanh toán trực tiếp tại quầy tiếp tân khi nhận sân hoặc sau trận đấu.</p>
                        <div class="cash-warning-note">
                            <strong>Lưu ý:</strong> Có mặt trước giờ đá 10 phút. Bùng sân sẽ bị trừ 25 điểm uy tín và khóa tài khoản theo quy định.
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="payment-success-card" style="text-align: center; max-width: 520px; margin: 2rem auto; padding: 45px 25px; background: #ecfdf5; border: 2px solid #10b981; border-radius: 20px; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.1);">
                <div style="width: 72px; height: 72px; background: #10b981; color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2.2rem; margin-bottom: 18px; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);">
                    <i class="fas fa-check"></i>
                </div>
                <h3 style="color: #065f46; font-size: 1.5rem; font-weight: 800; margin-bottom: 8px;">
                    <?php if ($existing_payment['phuong_thuc'] == 'chuyen_khoan'): ?>
                        THANH TOÁN THÀNH CÔNG!
                    <?php else: ?>
                        GIỮ CHỖ SÂN THÀNH CÔNG!
                    <?php endif; ?>
                </h3>
                <p style="color: #047857; font-size: 0.95rem; margin-bottom: 15px;">
                    Phương thức:
                    <strong style="color: #065f46;">
                        <?php
                        if ($existing_payment['phuong_thuc'] == 'chuyen_khoan') echo 'Chuyển khoản (Đã xác nhận)';
                        else echo 'Tiền mặt';
                        ?>
                    </strong>
                </p>
                <p style="color: #059669; font-size: 0.88rem; margin-bottom: 25px;">
                    Thông tin đơn đặt sân và hóa đơn đã được cập nhật vào tài khoản của bạn.
                </p>
                <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
                    <a href="index.php?controller=User&action=history" class="btn-action-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-history"></i> Lịch sử đặt sân
                    </a>
                    <a href="index.php" class="btn-action-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-home"></i> Về trang chủ
                    </a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="assets/js/payment.js?v=1.0.3"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        startPaymentStatusPolling(<?php echo (int)$dat_san_id; ?>);
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>