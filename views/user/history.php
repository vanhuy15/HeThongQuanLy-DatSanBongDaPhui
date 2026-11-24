<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/menu.php'; ?>

<style>
/* Nút thao tác nhỏ gọn, tinh tế, chuẩn UI/UX */
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 4px 10px;
    height: 28px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    border: 1px solid transparent;
    line-height: 1;
}
.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}
.action-pay {
    background: #0d9488;
    color: #ffffff;
    border-color: #0d9488;
}
.action-pay:hover {
    background: #0f766e;
    color: #ffffff;
}
.action-cancel {
    background: #fef2f2;
    color: #b91c1c;
    border-color: #fecaca;
}
.action-cancel:hover {
    background: #fee2e2;
}
.action-refund {
    background: #fff7ed;
    color: #c2410c;
    border-color: #fed7aa;
}
.action-refund:hover {
    background: #ffedd5;
}
.action-review {
    background: #f0fdf4;
    color: #047857;
    border-color: #a7f3d0;
}
.action-review:hover {
    background: #dcfce7;
}
.action-review-done,
.action-view-review {
    background: #f8fafc;
    color: #0f766e;
    border-color: #cbd5e1;
}
.action-review-done:hover,
.action-view-review:hover {
    background: #f0fdfa;
    border-color: #0d9488;
    color: #0d9488;
}
.action-bank {
    background: #faf5ff;
    color: #7c3aed;
    border-color: #e9d5ff;
}
.action-bank:hover {
    background: #f3e8ff;
}
.action-badge-info {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #e0f2fe;
    color: #0284c7;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 600;
}
</style>

<div class="history-container">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2>Lịch sử đặt sân</h2>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <?php 
                    $base_url = "index.php?controller=User&action=history";
                    if(!empty($tu_ngay)) $base_url .= "&tu_ngay=".urlencode($tu_ngay);
                    if(!empty($den_ngay)) $base_url .= "&den_ngay=".urlencode($den_ngay);
                    $current_tt = $trang_thai ?? '';
                    $tab_inactive = 'background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;';
                ?>
                <a href="<?php echo $base_url; ?>" style="padding: 0.4rem 1rem; border-radius: 50px; text-decoration: none; font-size: 0.85rem; font-weight: 600; <?php echo ($current_tt === '') ? 'background: #0d9488; color: white; border: 1px solid #0d9488;' : $tab_inactive; ?>">Tất cả</a>
                <a href="<?php echo $base_url . "&trang_thai=Chờ thanh toán"; ?>" style="padding: 0.4rem 1rem; border-radius: 50px; text-decoration: none; font-size: 0.85rem; font-weight: 600; <?php echo ($current_tt === 'Chờ thanh toán') ? 'background: #ea580c; color: white; border: 1px solid #ea580c;' : $tab_inactive; ?>">Chờ thanh toán</a>
                <a href="<?php echo $base_url . "&trang_thai=Chờ xác nhận"; ?>" style="padding: 0.4rem 1rem; border-radius: 50px; text-decoration: none; font-size: 0.85rem; font-weight: 600; <?php echo ($current_tt === 'Chờ xác nhận') ? 'background: #eab308; color: white; border: 1px solid #eab308;' : $tab_inactive; ?>">Chờ duyệt</a>
                <a href="<?php echo $base_url . "&trang_thai=Đã xác nhận"; ?>" style="padding: 0.4rem 1rem; border-radius: 50px; text-decoration: none; font-size: 0.85rem; font-weight: 600; <?php echo ($current_tt === 'Đã xác nhận') ? 'background: #10b981; color: white; border: 1px solid #10b981;' : $tab_inactive; ?>">Đã xác nhận</a>
                <a href="<?php echo $base_url . "&trang_thai=Chờ hoàn tiền"; ?>" style="padding: 0.4rem 1rem; border-radius: 50px; text-decoration: none; font-size: 0.85rem; font-weight: 600; <?php echo ($current_tt === 'Chờ hoàn tiền') ? 'background: #8b5cf6; color: white; border: 1px solid #8b5cf6;' : $tab_inactive; ?>">Chờ hoàn tiền</a>
                <a href="<?php echo $base_url . "&trang_thai=Đã hoàn tiền"; ?>" style="padding: 0.4rem 1rem; border-radius: 50px; text-decoration: none; font-size: 0.85rem; font-weight: 600; <?php echo ($current_tt === 'Đã hoàn tiền') ? 'background: #3b82f6; color: white; border: 1px solid #3b82f6;' : $tab_inactive; ?>">Đã hoàn tiền</a>
                <a href="<?php echo $base_url . "&trang_thai=Đã hủy"; ?>" style="padding: 0.4rem 1rem; border-radius: 50px; text-decoration: none; font-size: 0.85rem; font-weight: 600; <?php echo ($current_tt === 'Đã hủy') ? 'background: #ef4444; color: white; border: 1px solid #ef4444;' : $tab_inactive; ?>">Đã hủy</a>
            </div>
        </div>
        
        <form action="index.php" method="GET" style="display: flex; gap: 10px; align-items: center; background: #f8fafc; padding: 0.6rem 1.2rem; border-radius: 12px; border: 1px solid #e2e8f0;">
            <input type="hidden" name="controller" value="User">
            <input type="hidden" name="action" value="history">
            <?php if(!empty($current_tt)): ?>
                <input type="hidden" name="trang_thai" value="<?php echo htmlspecialchars($current_tt); ?>">
            <?php endif; ?>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <label style="font-size: 0.9rem; color: #475569; font-weight: 600;">Từ:</label>
                <input type="date" name="tu_ngay" id="tu_ngay" value="<?php echo htmlspecialchars($tu_ngay); ?>" min="2025-01-01" style="padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none; color: #334155;">
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <label style="font-size: 0.9rem; color: #475569; font-weight: 600;">Đến:</label>
                <input type="date" name="den_ngay" id="den_ngay" value="<?php echo htmlspecialchars($den_ngay); ?>" min="2025-01-01" style="padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none; color: #334155;">
            </div>
            
            <button type="submit" style="background: var(--primary-color); color: white; border: none; padding: 0.5rem 1.2rem; border-radius: 8px; cursor: pointer; font-size: 0.9rem; font-weight: 600; transition: background 0.2s;">Lọc</button>
            <?php if(!empty($tu_ngay) || !empty($den_ngay)): ?>
                <a href="index.php?controller=User&action=history<?php echo !empty($current_tt) ? '&trang_thai='.urlencode($current_tt) : ''; ?>" style="color: #ef4444; font-size: 0.9rem; text-decoration: none; margin-left: 5px;">Xóa lọc</a>
            <?php endif; ?>
        </form>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="history-table lichsu-table">
            <thead>
                <tr>
                    <th>Ngày đặt</th>
                    <th>Thông tin sân</th>
                    <th>Khung giờ</th>
                    <th>Tổng tiền</th>
                    <th style="text-align: center;">Trạng thái</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($history) > 0): ?>
                    <?php foreach($history as $row): 
                        $gia_don = ($row['loai_san_dat'] === 'san_7' && !empty($row['gia_tien_san_7'])) ? $row['gia_tien_san_7'] : $row['gia_tien'];
                        $match_start_time = strtotime($row['ngay_dat'] . ' ' . $row['gio_bat_dau']);
                        $match_end_time = strtotime($row['ngay_dat'] . ' ' . $row['gio_ket_thuc']);
                        $is_match_started = ($match_start_time <= time());
                        $is_match_finished = ($match_end_time <= time());
                    ?>
                    <tr>
                        <td style="font-weight: 600; color: #334155;"><?php echo date('d/m/Y', strtotime($row['ngay_dat'])); ?></td>
                        <td>
                            <strong style="color: #0f766e; font-size: 1.05rem;"><?php echo htmlspecialchars($row['ten_san']); ?></strong>
                            <br><span style="font-size: 0.85rem; color: #64748b;">Sân con: <strong><?php echo htmlspecialchars($row['ten_san_con']); ?></strong></span>
                            <?php if ($row['loai_san_dat'] === 'san_7'): ?>
                                <span style="background: #0d9488; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; margin-left: 4px;">Sân 7</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: 500; color: #334155;"><?php echo date('H:i', strtotime($row['gio_bat_dau'])) . ' - ' . date('H:i', strtotime($row['gio_ket_thuc'])); ?></td>
                        <td>
                            <span style="font-weight: 700; color: #ef4444; font-size: 1.05rem;"><?php echo number_format($gia_don); ?> đ</span>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;">
                                <?php 
                                if ($row['trang_thai'] == 'Chờ thanh toán') {
                                    $remaining_hold = \Models\DatSan::getRemainingHoldTime($row['ngay_tao'], 600);
                                    $rem_min = floor($remaining_hold / 60);
                                    $rem_sec = $remaining_hold % 60;
                                    $time_str = sprintf("%02d:%02d", $rem_min, $rem_sec);
                                    echo '<span class="status-badge" style="background:#fff7ed; color:#ea580c; border:1px solid #fed7aa; margin:0;">Chờ thanh toán</span>';
                                    echo '<small class="history-countdown" data-seconds="' . $remaining_hold . '" data-id="' . $row['dat_san_id'] . '" style="color:#d97706; font-size:0.75rem; font-weight:600;"><i class="far fa-clock"></i> Còn <span class="countdown-timer">' . $time_str . '</span></small>';
                                } elseif ($row['trang_thai'] == 'Chờ xác nhận') {
                                    if ($is_match_started) {
                                        echo '<span class="status-badge" style="background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;" title="Đã quá giờ thi đấu mà quản trị viên chưa duyệt">Quá hạn duyệt</span>';
                                    } else {
                                        echo '<span class="status-badge status-cho">Chờ duyệt</span>';
                                    }
                                } elseif ($row['trang_thai'] == 'Đã xác nhận') {
                                    if ($is_match_finished) {
                                        echo '<span class="status-badge" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0;">Hoàn thành</span>';
                                    } else {
                                        echo '<span class="status-badge status-xacnhan">Đã xác nhận</span>';
                                    }
                                } elseif ($row['trang_thai'] == 'Chờ hoàn tiền') {
                                    echo '<span class="status-badge" style="background:#f5f3ff; color:#6d28d9; border:1px solid #ddd6fe;">Chờ hoàn tiền</span>';
                                } elseif ($row['trang_thai'] == 'Đã hoàn tiền') {
                                    echo '<span class="status-badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">Đã hoàn tiền</span>';
                                } elseif (in_array($row['trang_thai'], ['Đã hủy', 'Hủy'])) {
                                    echo '<span class="status-badge status-huy">Đã hủy</span>';
                                } else {
                                    echo '<span class="status-badge status-huy">' . htmlspecialchars($row['trang_thai']) . '</span>';
                                }
                                ?>
                            </div>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <?php if ($row['trang_thai'] == 'Chờ thanh toán'): ?>
                                <div style="display: inline-flex; flex-direction: column; gap: 5px; align-items: center; justify-content: center;">
                                    <a href="index.php?controller=Payment&action=index&dat_san_id=<?php echo $row['dat_san_id']; ?>" class="action-btn action-pay" title="Thanh toán ngay" style="min-width: 105px; justify-content: center;">
                                        <i class="fas fa-credit-card"></i> Thanh toán
                                    </a>
                                    <button type="button" onclick="cancelUserBooking(<?php echo $row['dat_san_id']; ?>)" class="action-btn action-cancel" title="Hủy đơn này" style="min-width: 105px; justify-content: center;">
                                        <i class="fas fa-times"></i> Hủy
                                    </button>
                                </div>
                            <?php elseif ($row['trang_thai'] == 'Chờ xác nhận'): ?>
                                <?php if ($row['phuong_thuc'] === 'chuyen_khoan'): ?>
                                    <button type="button" onclick="openRefundModal(<?php echo $row['dat_san_id']; ?>, '<?php echo addslashes($row['ten_san']); ?>', <?php echo (int)$gia_don; ?>)" class="action-btn action-refund" title="Hủy yêu cầu và nhận lại tiền chuyển khoản">
                                        <i class="fas fa-undo-alt"></i> Hủy & Hoàn tiền
                                    </button>
                                <?php else: ?>
                                    <button type="button" onclick="cancelUserBooking(<?php echo $row['dat_san_id']; ?>)" class="action-btn action-cancel" title="Hủy yêu cầu đặt sân">
                                        <i class="fas fa-times"></i> Hủy
                                    </button>
                                <?php endif; ?>
                            <?php elseif ($row['trang_thai'] == 'Đã xác nhận'): ?>
                                <?php if ($is_match_finished): ?>
                                    <?php if (!empty($row['danh_gia_id'])): ?>
                                        <button type="button" onclick="openViewReviewModal('<?php echo addslashes($row['ten_san']); ?>', <?php echo (int)$row['so_sao']; ?>, '<?php echo addslashes($row['noi_dung'] ?? ''); ?>')" class="action-btn action-view-review" title="Xem lại đánh giá của bạn">
                                            <i class="fas fa-eye"></i> Xem đánh giá
                                        </button>
                                    <?php else: ?>
                                        <button type="button" onclick="openReviewModal(<?php echo $row['dat_san_id']; ?>, <?php echo $row['san_id']; ?>, '<?php echo addslashes($row['ten_san']); ?>', 5, '')" class="action-btn action-review" title="Viết nhận xét đánh giá (+2 điểm uy tín)">
                                            <i class="fas fa-star"></i> Đánh giá
                                        </button>
                                    <?php endif; ?>
                                <?php elseif ($is_match_started): ?>
                                    <span class="action-badge-info">
                                        <i class="fas fa-futbol"></i> Đang đá
                                    </span>
                                <?php else: ?>
                                    <?php if ($row['phuong_thuc'] === 'chuyen_khoan'): ?>
                                        <button type="button" onclick="openRefundModal(<?php echo $row['dat_san_id']; ?>, '<?php echo addslashes($row['ten_san']); ?>', <?php echo (int)$gia_don; ?>)" class="action-btn action-refund" title="Hủy sân và nhận lại tiền">
                                            <i class="fas fa-undo-alt"></i> Hủy & Hoàn tiền
                                        </button>
                                    <?php else: ?>
                                        <button type="button" onclick="cancelUserBooking(<?php echo $row['dat_san_id']; ?>)" class="action-btn action-cancel" title="Hủy sân trước giờ đá">
                                            <i class="fas fa-times"></i> Hủy
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php elseif ($row['trang_thai'] == 'Chờ hoàn tiền'): ?>
                                <?php 
                                    $rf_info = json_decode($row['thong_tin_hoan_tien'] ?? '{}', true);
                                    $needs_bank_info = (empty($rf_info['stk']) || $rf_info['stk'] === 'Chưa cung cấp');
                                ?>
                                <?php if ($needs_bank_info): ?>
                                    <button type="button" onclick="openRefundModal(<?php echo $row['dat_san_id']; ?>, '<?php echo addslashes($row['ten_san']); ?>', <?php echo (int)$gia_don; ?>)" class="action-btn action-bank" title="Bổ sung STK ngân hàng để nhận tiền hoàn">
                                        <i class="fas fa-university"></i> STK hoàn
                                    </button>
                                <?php else: ?>
                                    <span style="color: #6d28d9; font-size: 0.8rem; font-weight: 600;">
                                        <i class="fas fa-clock"></i> Chờ hoàn
                                    </span>
                                <?php endif; ?>
                            <?php elseif ($row['trang_thai'] == 'Đã hoàn tiền'): ?>
                                <span style="color: #1d4ed8; font-size: 0.8rem; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> Đã hoàn
                                </span>
                            <?php elseif (in_array($row['trang_thai'], ['Đã hủy', 'Hủy'])): ?>
                                <span style="color: #cbd5e1; font-weight: 600; font-size: 0.95rem;">—</span>
                            <?php else: ?>
                                <span style="color: #cbd5e1; font-weight: 600; font-size: 0.95rem;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-msg">Bạn chưa có lịch sử đặt sân nào trong khoảng thời gian này.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div> <!-- End history-container -->

    <?php if ($total_pages > 1): ?>
    <div class="pagination" style="display: flex; justify-content: center; align-items: center; gap: 6px; margin: 1.5rem 0 3rem 0;">
        <?php 
            $query_params = "controller=User&action=history";
            if (!empty($tu_ngay)) $query_params .= "&tu_ngay=" . urlencode($tu_ngay);
            if (!empty($den_ngay)) $query_params .= "&den_ngay=" . urlencode($den_ngay);
            if (!empty($current_tt)) $query_params .= "&trang_thai=" . urlencode($current_tt);
        ?>
        <?php 
            $pagination_range = function_exists('get_pagination_range') ? get_pagination_range($page, $total_pages, 1) : range(1, $total_pages);
        ?>
        <?php if ($page > 1): ?>
            <a href="index.php?<?php echo $query_params; ?>&page=<?php echo $page - 1; ?>" title="Trang trước" style="display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.82rem; color: #475569; text-decoration: none; font-weight: 600; background: #ffffff; transition: all 0.2s;"><i class="fas fa-chevron-left" style="font-size: 0.75rem;"></i></a>
        <?php endif; ?>

        <?php foreach ($pagination_range as $p): ?>
            <?php if ($p === '...'): ?>
                <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 28px; height: 32px; color: #94a3b8; font-weight: 700; font-size: 0.85rem; user-select: none;">...</span>
            <?php else: ?>
                <a href="index.php?<?php echo $query_params; ?>&page=<?php echo $p; ?>" style="display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 8px; border: 1px solid <?php echo ($page == $p) ? '#0d9488' : '#e2e8f0'; ?>; border-radius: 8px; font-size: 0.88rem; color: <?php echo ($page == $p) ? '#ffffff' : '#334155'; ?>; text-decoration: none; font-weight: 600; background: <?php echo ($page == $p) ? '#0d9488' : '#ffffff'; ?>; box-shadow: <?php echo ($page == $p) ? '0 2px 4px rgba(13, 148, 136, 0.2)' : 'none'; ?>; transition: all 0.2s;">
                    <?php echo $p; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($page < $total_pages): ?>
            <a href="index.php?<?php echo $query_params; ?>&page=<?php echo $page + 1; ?>" title="Trang sau" style="display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.82rem; color: #475569; text-decoration: none; font-weight: 600; background: #ffffff; transition: all 0.2s;"><i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

<!-- Modal Đánh Giá Sân -->
<div id="reviewModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; width: 100%; max-width: 480px; padding: 2.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); position: relative; margin: 1rem;">
        <button type="button" onclick="closeReviewModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer;">&times;</button>
        
        <h3 style="margin: 0 0 0.5rem 0; color: #1e293b; font-size: 1.3rem;"><i class="fas fa-star" style="color: #f59e0b;"></i> Đánh giá chất lượng sân</h3>
        <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 2rem;" id="modalPitchName">Sân bóng đá</p>

        <form id="reviewForm" onsubmit="submitReview(event)">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="dat_san_id" id="modalDatSanId">
            <input type="hidden" name="san_id" id="modalSanId">
            <input type="hidden" name="so_sao" id="modalSoSao" value="5">

            <!-- Star Picker -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 0.8rem; font-size: 1rem;">Bạn chấm sân này mấy sao?</label>
                <div id="starContainer" style="font-size: 2.5rem; color: #e2e8f0; cursor: pointer; display: inline-flex; gap: 10px;">
                    <i class="fas fa-star star-item" data-val="1"></i>
                    <i class="fas fa-star star-item" data-val="2"></i>
                    <i class="fas fa-star star-item" data-val="3"></i>
                    <i class="fas fa-star star-item" data-val="4"></i>
                    <i class="fas fa-star star-item" data-val="5"></i>
                </div>
                <div id="starLabel" style="font-size: 0.95rem; font-weight: 700; color: var(--primary-color); margin-top: 8px;">5/5 - Tuyệt vời</div>
            </div>

            <!-- Content Area -->
            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 0.5rem; font-size: 0.95rem;">Nhận xét chi tiết (mặt cỏ, ánh sáng, dịch vụ...):</label>
                <textarea name="noi_dung" id="modalNoiDung" rows="3" placeholder="Chia sẻ cảm nhận chân thực của bạn về trận đấu tại đây..." style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 1rem; font-family: inherit; font-size: 0.95rem; outline: none; box-sizing: border-box;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" onclick="closeReviewModal()" style="padding: 0.75rem 1.5rem; border-radius: 8px; border: 1px solid #cbd5e1; background: white; color: #475569; font-weight: 600; cursor: pointer; font-size: 0.95rem;">Hủy</button>
                <button type="submit" id="submitReviewBtn" style="padding: 0.75rem 1.5rem; border-radius: 8px; border: none; background: var(--primary-color); color: white; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25); font-size: 0.95rem;">Gửi Đánh Giá</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Yêu cầu hoàn tiền -->
<div id="refundModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
    <div style="background: white; border-radius: 16px; width: 90%; max-width: 480px; padding: 2rem; box-shadow: 0 20px 40px rgba(0,0,0,0.2); animation: fadeIn 0.25s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.3rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-undo" style="color: #6d28d9;"></i> Yêu Cầu Hủy Sân &amp; Hoàn Tiền
            </h3>
            <button type="button" onclick="closeRefundModal()" style="background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form id="refundForm" onsubmit="submitRefundRequest(event)">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="dat_san_id" id="refund_dat_san_id" value="">

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                <p style="margin: 0 0 6px 0; font-size: 0.95rem; color: #64748b;">Sân bóng: <strong id="refund_ten_san" style="color: #1e293b;"></strong></p>
                <p style="margin: 0; font-size: 0.95rem; color: #64748b;">Số tiền hoàn dự kiến: <strong id="refund_so_tien" style="color: #047857; font-size: 1.1rem;"></strong></p>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.95rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Ngân hàng nhận tiền (*)</label>
                <select name="ngan_hang" required style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 1rem; outline: none;">
                    <option value="">-- Chọn ngân hàng --</option>
                    <option value="MB">MB Bank (Quân Đội)</option>
                    <option value="VCB">Vietcombank</option>
                    <option value="TCB">Techcombank</option>
                    <option value="BIDV">BIDV</option>
                    <option value="VBA">Agribank</option>
                    <option value="VPB">VPBank</option>
                    <option value="ACB">ACB</option>
                    <option value="TPB">TPBank</option>
                    <option value="STB">Sacombank</option>
                    <option value="CTG">VietinBank</option>
                    <option value="VIB">VIB</option>
                    <option value="SHB">SHB</option>
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.95rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Số tài khoản (*)</label>
                <input type="text" name="stk" placeholder="Nhập số tài khoản nhận tiền..." required style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; outline: none; font-family: monospace; font-size: 1.1rem;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.95rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Tên chủ tài khoản (In hoa không dấu) (*)</label>
                <input type="text" name="ten_chu_tk" placeholder="VD: NGUYEN VAN A" required style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 1rem; outline: none; text-transform: uppercase;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.95rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Lý do hủy sân</label>
                <textarea name="ly_do" rows="2" placeholder="VD: Bận việc đột xuất, đổi lịch..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 0.95rem; outline: none;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeRefundModal()" style="padding: 10px 20px; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 10px; font-weight: 600; cursor: pointer; color: #64748b; font-size: 0.95rem;">Hủy bỏ</button>
                <button type="submit" id="submitRefundBtn" style="padding: 10px 24px; border: none; background: #6d28d9; color: white; border-radius: 10px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(109, 40, 217, 0.3); font-size: 0.95rem;">
                    <i class="fas fa-paper-plane"></i> Gửi Yêu Cầu Hoàn Tiền
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Xem Đánh Giá Của Bạn (Read-Only) -->
<div id="viewReviewModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; width: 100%; max-width: 460px; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); position: relative; margin: 1rem;">
        <button type="button" onclick="closeViewReviewModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer;">&times;</button>
        
        <h3 style="margin: 0 0 0.5rem 0; color: #0f172a; font-size: 1.25rem; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-star" style="color: #f59e0b;"></i> Đánh giá của bạn
        </h3>
        <p style="color: #64748b; font-size: 0.92rem; margin-bottom: 1.5rem;" id="viewModalPitchName">Sân bóng đá</p>

        <div style="text-align: center; margin-bottom: 1.5rem; background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0;">
            <div id="viewStarContainer" style="font-size: 2rem; color: #f59e0b; display: inline-flex; gap: 6px;"></div>
            <div id="viewStarLabel" style="font-size: 0.95rem; font-weight: 700; color: #0f766e; margin-top: 6px;"></div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-weight: 600; color: #475569; font-size: 0.88rem; margin-bottom: 6px;">Cảm nhận của bạn:</label>
            <div id="viewCommentBox" style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; font-size: 0.92rem; color: #1e293b; min-height: 70px; line-height: 1.5;"></div>
        </div>

        <div style="text-align: right;">
            <button type="button" onclick="closeViewReviewModal()" class="action-btn" style="padding: 8px 20px; height: 36px; border-radius: 8px; background: #0d9488; color: white; border: none; font-size: 0.9rem; font-weight: 600; cursor: pointer;">Đóng</button>
        </div>
    </div>
</div>

<script src="assets/js/history.js?v=1.0.2"></script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
