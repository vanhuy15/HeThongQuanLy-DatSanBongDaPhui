<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();

// Đếm số đơn chờ xác nhận để hiện badge trên sidebar và chuông thông báo
$badge_count_cho = 0;
$recent_admin_notifs = [];
if (isset($conn)) {
    $res_cho = $conn->query("SELECT COUNT(*) FROM dat_san WHERE trang_thai = 'Chờ xác nhận' AND deleted_at IS NULL");
    if ($res_cho) {
        $badge_count_cho = (int)$res_cho->fetch_row()[0];
    }

    // Lấy 6 đơn đặt sân / thanh toán mới nhất để hiện trong Popup thông báo
    $res_notif = $conn->query("
        SELECT ds.dat_san_id, ds.ngay_dat, ds.trang_thai, nd.ho_ten, sb.ten_san, ds.ten_san_con, 
               kg.gio_bat_dau, kg.gio_ket_thuc, tt.phuong_thuc, ds.ngay_tao
        FROM dat_san ds
        JOIN nguoi_dung nd ON ds.nguoi_dung_id = nd.nguoi_dung_id
        JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
        JOIN san_bong sb ON kg.san_id = sb.san_id
        LEFT JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
        WHERE ds.deleted_at IS NULL AND ds.trang_thai != 'Chờ thanh toán'
        ORDER BY ds.dat_san_id DESC
        LIMIT 6
    ");
    if ($res_notif) {
        while ($rn = $res_notif->fetch_assoc()) {
            $recent_admin_notifs[] = $rn;
        }
    }
}

if (!function_exists('admin_format_time_ago')) {
    function admin_format_time_ago($datetime) {
        if (empty($datetime)) return "Vừa xong";
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60) {
            return "Vừa xong";
        } elseif ($diff < 3600) {
            return floor($diff / 60) . " phút trước";
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . " giờ trước";
        } elseif ($diff < 172800) {
            return "Hôm qua lúc " . date('H:i', $time);
        } else {
            return date('d/m/Y H:i', $time);
        }
    }
}

$current_uri = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo function_exists('csrf_token') ? csrf_token() : ''; ?>">
    <title>Quản Trị Viên</title>
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Admin Master CSS -->
    <link rel="stylesheet" href="/quanlysanbong/admin/style_admin.css?v=1.0.2">
</head>
<body>

<div class="admin-container">
    
    <!-- LEFT SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="/quanlysanbong/admin/" class="admin-brand">
                <img src="/quanlysanbong/images/logo.jpg" alt="Logo VH15" class="admin-brand-logo">
                <div class="admin-brand-text">
                    <span class="brand-title">Trang Quản Trị</span>
                    <span class="brand-sub">Quản Lý Sân Bóng</span>
                </div>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <?php
                    $is_home = (strpos($current_uri, '/admin/index.php') !== false || $current_uri === '/quanlysanbong/admin/' || $current_uri === '/quanlysanbong/admin');
                    $is_san = strpos($current_uri, '/admin/sanbong') !== false;
                    $is_khunggio = strpos($current_uri, '/admin/khunggio') !== false;
                    $is_datsan = strpos($current_uri, '/admin/datsan') !== false;
                    $is_user = strpos($current_uri, '/admin/user') !== false;
                    $is_doanhthu = strpos($current_uri, '/admin/doanhthu') !== false;
                    $is_danhgia = strpos($current_uri, '/admin/danhgia') !== false;
                ?>
                <li>
                    <a href="/quanlysanbong/admin/" class="<?php echo $is_home ? 'active' : ''; ?>">
                        <i class="fas fa-th-large fa-fw"></i>
                        <span class="text">Tổng quan</span>
                    </a>
                </li>
                <li>
                    <a href="/quanlysanbong/admin/sanbong/" class="<?php echo $is_san ? 'active' : ''; ?>">
                        <i class="fas fa-futbol fa-fw"></i>
                        <span class="text">Quản lý Sân</span>
                    </a>
                </li>
                <li>
                    <a href="/quanlysanbong/admin/khunggio/index.php?san_id=1" class="<?php echo $is_khunggio ? 'active' : ''; ?>">
                        <i class="fas fa-clock fa-fw"></i>
                        <span class="text">Khung giờ &amp; Giá</span>
                    </a>
                </li>
                <li>
                    <a href="/quanlysanbong/admin/datsan/" class="<?php echo $is_datsan ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-check fa-fw"></i>
                        <span class="text">Quản lý Đặt sân</span>
                        <?php if ($badge_count_cho > 0): ?>
                            <span class="nav-badge"><?php echo $badge_count_cho; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="/quanlysanbong/admin/user/" class="<?php echo $is_user ? 'active' : ''; ?>">
                        <i class="fas fa-users fa-fw"></i>
                        <span class="text">Khách hàng</span>
                    </a>
                </li>
                <li>
                    <a href="/quanlysanbong/admin/doanhthu/" class="<?php echo $is_doanhthu ? 'active' : ''; ?>">
                        <i class="fas fa-wallet fa-fw"></i>
                        <span class="text">Doanh thu</span>
                    </a>
                </li>
                <li>
                    <a href="/quanlysanbong/admin/danhgia/" class="<?php echo $is_danhgia ? 'active' : ''; ?>">
                        <i class="fas fa-star fa-fw"></i>
                        <span class="text">Đánh giá</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- NÚT ĐĂNG XUẤT ĐÁY SIDEBAR GÓC TRÁI -->
        <div class="sidebar-footer">
            <a href="/quanlysanbong/admin/logout.php" class="btn-logout" title="Đăng xuất khỏi hệ thống" onclick="handleAdminLogout(event, this.href);">
                <i class="fas fa-sign-out-alt fa-fw"></i>
                <span class="text">Đăng xuất</span>
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="main-wrapper">
        <header class="admin-topbar">
            <div></div>
            
            <div class="topbar-actions">
                
                <!-- NOTIFICATION BELL & POPUP (ĐỒNG BỘ CẤU TRÚC 100% VỚI USER) -->
                <div class="nav-notification-item" id="notifDropdownWrapper">
                    <button type="button" class="btn-notification-bell" id="notifBellBtn" title="Thông báo" onclick="toggleNotifDropdown(event)">
                        <i class="far fa-bell"></i>
                        <span class="notification-badge" id="adminNotifBadge" style="<?php echo ($badge_count_cho > 0) ? 'display:flex;' : 'display:none;'; ?>">
                            <?php echo $badge_count_cho > 99 ? '99+' : $badge_count_cho; ?>
                        </span>
                    </button>
                    
                    <div class="notification-dropdown-menu" id="notifDropdownMenu">
                        <div class="notification-header">
                            <div class="notif-title"><i class="fas fa-bell" style="color: var(--primary);"></i> Thông báo</div>
                            <button type="button" class="btn-mark-all-read" onclick="markAllAdminNotificationsRead(event)">Đã đọc tất cả</button>
                        </div>
                        
                        <div class="notification-list">
                            <?php if (!empty($recent_admin_notifs)): ?>
                                <?php foreach ($recent_admin_notifs as $nf): 
                                    $is_cho = ($nf['trang_thai'] === 'Chờ xác nhận');
                                    $is_ck = ($nf['phuong_thuc'] === 'chuyen_khoan');
                                    $icon_class = $is_ck ? 'xac_nhan' : 'dat_san';
                                    $icon_tag = $is_ck ? 'fas fa-check' : 'fas fa-futbol';
                                    $time_str = admin_format_time_ago($nf['ngay_tao'] ?? null);
                                ?>
                                <a href="/quanlysanbong/admin/datsan/?status=<?php echo $is_cho ? 'cho-xac-nhan' : 'tat_ca'; ?>" class="notification-item <?php echo $is_cho ? 'unread' : ''; ?>">
                                    <div class="notification-icon <?php echo $icon_class; ?>">
                                        <i class="<?php echo $icon_tag; ?>"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notif-item-title">
                                            <strong><?php echo htmlspecialchars($nf['ho_ten']); ?></strong> 
                                            <?php if ($is_ck): ?>
                                                đã thanh toán <span class="pay-tag pay-tag-qr" style="font-size: 10px; padding: 1px 5px; border-radius: 4px; background: #ecfdf5; color: #047857; font-weight: 600;">VietQR</span>
                                            <?php else: ?>
                                                đã đặt sân <span class="pay-tag pay-tag-cash" style="font-size: 10px; padding: 1px 5px; border-radius: 4px; background: #eff6ff; color: #1d4ed8; font-weight: 600;">Tiền mặt</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="notif-item-text">
                                            Đơn #<?php echo $nf['dat_san_id']; ?> • <?php echo htmlspecialchars($nf['ten_san']); ?><?php echo !empty($nf['ten_san_con']) ? ' (Sân '.$nf['ten_san_con'].')' : ''; ?> lúc <?php echo date("H:i", strtotime($nf['gio_bat_dau'])); ?> ngày <?php echo date("d/m", strtotime($nf['ngay_dat'])); ?>
                                        </div>
                                        <div class="notif-item-time">
                                            <?php echo $time_str; ?>
                                        </div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="notification-empty">
                                    <i class="far fa-bell-slash" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                                    Chưa có thông báo nào
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="notification-footer">
                            <a href="/quanlysanbong/admin/datsan/">
                                Xem toàn bộ lịch đặt sân <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <main class="admin-content">
