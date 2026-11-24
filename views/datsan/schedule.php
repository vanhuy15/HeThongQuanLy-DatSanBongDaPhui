<?php

/**
 * View Partial: Schedule Table & Pitch Map
 * Rendered by DatSanController::getSchedule()
 */

use Models\DatSan;
?>
<!-- Header & Mode Switcher -->
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
    <h3 class="lich-header" style="margin:0;">
        Lịch sân "<?php echo htmlspecialchars($ten_san_xem); ?>" ngày <?php echo date("d/m/Y", strtotime($ngay_dat)); ?>
    </h3>

    <?php if ($loai_san_goc === 'san_5_7'): ?>
        <!-- Toggle Mode: Sân 5 / Sân 7 -->
        <div style="display: flex; background: #e2e8f0; padding: 4px; border-radius: 50px; gap: 4px;">
            <button type="button" onclick="switchBookingMode('san_5')" style="border: none; padding: 6px 16px; border-radius: 50px; font-size: 0.86rem; font-weight: 700; cursor: pointer; transition: all 0.2s; <?php echo ($view_mode === 'san_5') ? 'background: #0d9488; color: white;' : 'background: transparent; color: #475569;'; ?>">
                Đặt Sân 5
            </button>
            <button type="button" onclick="switchBookingMode('san_7')" style="border: none; padding: 6px 16px; border-radius: 50px; font-size: 0.86rem; font-weight: 700; cursor: pointer; transition: all 0.2s; <?php echo ($view_mode === 'san_7') ? 'background: #0d9488; color: white;' : 'background: transparent; color: #475569;'; ?>">
                Đặt Sân 7 (Ghép <?php echo $quy_tac_ghep; ?> sân)
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Chú thích trạng thái sơ đồ sân -->
<div class="schedule-legend-bar" style="display: flex; gap: 16px; align-items: center; justify-content: flex-end; margin: 12px 0 16px; font-size: 0.85rem; color: #475569; font-weight: 600; flex-wrap: wrap;">
    <div style="display: flex; align-items: center; gap: 6px;">
        <span style="width: 14px; height: 14px; border-radius: 4px; background: #0d9488; display: inline-block;"></span> Sân trống
    </div>
    <div style="display: flex; align-items: center; gap: 6px;">
        <span style="width: 14px; height: 14px; border-radius: 4px; background: #f59e0b; display: inline-block;"></span> Đang giữ chỗ (10p)
    </div>
    <div style="display: flex; align-items: center; gap: 6px;">
        <span style="width: 14px; height: 14px; border-radius: 4px; background: #ef4444; display: inline-block;"></span> Đã đặt
    </div>
    <div style="display: flex; align-items: center; gap: 6px;">
        <span style="width: 14px; height: 14px; border-radius: 4px; background: #94a3b8; display: inline-block;"></span> Đã qua giờ
    </div>
</div>

<table class="lichsu-table" style="margin-top:5px;">
    <thead>
        <tr>
            <th>Khung giờ</th>
            <th>Giá tiền (<?php echo ($view_mode === 'san_7') ? 'Sân 7' : 'Sân 5'; ?>)</th>
            <th style="text-align: center;">Trạng thái (Click vào sân trống để chọn)</th>
            <th style="text-align: center; width: 140px;">Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($kg = $khung_gio_list->fetch_assoc()) :
            // Lấy chi tiết trạng thái từng sân con trong khung giờ này qua Model
            $status_details = DatSan::getSubPitchesStatusDetails($kg['khung_gio_id'], $ngay_dat);
            $confirmed_sub_pitches = $status_details['confirmed'];
            $holding_sub_pitches = $status_details['holding'];
            $locked_sub_pitches = $status_details['locked'];

            // Kiểm tra khung giờ đã qua hoặc quá sát giờ thi đấu (dưới 5 phút khoảng đệm)
            $slot_start_time = strtotime($ngay_dat . ' ' . $kg['gio_bat_dau']);
            $buffer_seconds = 5 * 60; // 5 phút đệm chuẩn bị sân
            $is_slot_expired = ($slot_start_time <= (time() + $buffer_seconds));

            // Giá tiền theo chế độ
            $gia_hien_thi = \Models\SanBong::calculateDisplayPrice($kg['gia_tien'], $view_mode, (int)$quy_tac_ghep, $kg['gia_tien_san_7'] ?? null);

            // Tính số sân còn lại theo chế độ
            $has_available_slot = false;
            $has_holding_slot = false;
        ?>
            <tr class="schedule-row <?php echo $is_slot_expired ? 'row-expired' : ''; ?>" style="<?php echo $is_slot_expired ? 'background: #f8fafc; opacity: 0.75;' : ''; ?>">
                <td>
                    <strong><?php echo date("H:i", strtotime($kg['gio_bat_dau'])) . ' - ' . date("H:i", strtotime($kg['gio_ket_thuc'])); ?></strong>
                </td>
                <td class="price-cell"><?php echo number_format($gia_hien_thi); ?> VND</td>

                <td>
                    <div class="mini-pitch-map" style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                        <?php
                        if ($view_mode === 'san_5') {
                            // Chế độ xem & đặt Sân 5
                            for ($i = 0; $i < $tong_so_san; $i++) {
                                $pitch_name = $sub_pitches[$i] ?? ($i + 1);
                                $is_confirmed = in_array($pitch_name, $confirmed_sub_pitches);
                                $is_holding = in_array($pitch_name, $holding_sub_pitches);

                                if ($is_confirmed) {
                                    // SÂN 5 - ĐÃ ĐẶT: đỏ, X mark
                                    $booked_style = $is_slot_expired ? "style='cursor: not-allowed; opacity: 0.5; pointer-events: none;'" : "style='cursor: not-allowed;'";
                                    echo "<svg class='mini-pitch-svg booked' $booked_style viewBox='0 0 80 115' title='Sân $pitch_name đã được đặt chính thức'><rect width='80' height='115' rx='9' fill='#ef4444'/><rect x='6' y='6' width='68' height='76' rx='3' fill='none' stroke='white' stroke-width='1.2' opacity='0.25'/><line x1='6' y1='44' x2='74' y2='44' stroke='white' stroke-width='1.2' opacity='0.25'/><circle cx='40' cy='44' r='9' fill='none' stroke='white' stroke-width='1.2' opacity='0.25'/><line x1='28' y1='26' x2='52' y2='60' stroke='white' stroke-width='3.5' stroke-linecap='round' opacity='0.9'/><line x1='52' y1='26' x2='28' y2='60' stroke='white' stroke-width='3.5' stroke-linecap='round' opacity='0.9'/><rect x='12' y='91' width='56' height='18' rx='9' fill='rgba(255,255,255,0.18)'/><text x='40' y='100' text-anchor='middle' dominant-baseline='middle' font-family='Inter,sans-serif' font-size='9' font-weight='700' fill='rgba(255,255,255,0.85)'>Đã đặt</text></svg>";
                                } elseif ($is_holding) {
                                    // SÂN 5 - ĐANG GIỮ CHỖ (10 PHÚT): vàng hổ phách, icon đồng hồ cát
                                    $has_holding_slot = true;
                                    $holding_style = $is_slot_expired ? "style='cursor: not-allowed; opacity: 0.5; pointer-events: none;'" : "style='cursor: not-allowed;'";
                                    echo "<svg class='mini-pitch-svg holding' $holding_style viewBox='0 0 80 115' title='Sân $pitch_name đang được giữ chỗ (Chờ thanh toán)'><rect width='80' height='115' rx='9' fill='#f59e0b'/><rect x='6' y='6' width='68' height='76' rx='3' fill='none' stroke='white' stroke-width='1.2' opacity='0.3'/><line x1='6' y1='44' x2='74' y2='44' stroke='white' stroke-width='1.2' opacity='0.3'/><circle cx='40' cy='44' r='9' fill='none' stroke='white' stroke-width='1.2' opacity='0.3'/><circle cx='40' cy='44' r='14' fill='rgba(255,255,255,0.2)'/><text x='40' y='49' text-anchor='middle' font-size='16' fill='white'>⏳</text><rect x='10' y='91' width='60' height='18' rx='9' fill='rgba(255,255,255,0.25)'/><text x='40' y='100' text-anchor='middle' dominant-baseline='middle' font-family='Inter,sans-serif' font-size='9' font-weight='700' fill='white'>Đang giữ</text></svg>";
                                } elseif ($is_slot_expired) {
                                    // SÂN 5 - ĐÃ QUA GIỜ: màu xám (kiểu ẩn), không click được
                                    echo "<svg class='mini-pitch-svg expired' style='cursor: not-allowed; pointer-events: none; opacity: 0.6;' viewBox='0 0 80 115' title='Sân $pitch_name (Đã qua giờ)'><rect class='pitch-fill' width='80' height='115' rx='9' fill='#94a3b8'/><rect x='6' y='6' width='68' height='76' rx='3' fill='none' stroke='white' stroke-width='1.3' opacity='0.4'/><line x1='6' y1='44' x2='74' y2='44' stroke='white' stroke-width='1.3' opacity='0.4'/><circle cx='40' cy='44' r='9' fill='none' stroke='white' stroke-width='1.3' opacity='0.4'/><circle cx='40' cy='44' r='2' fill='white' opacity='0.5'/><rect x='20' y='6' width='40' height='10' fill='none' stroke='white' stroke-width='1' opacity='0.28'/><rect x='20' y='72' width='40' height='10' fill='none' stroke='white' stroke-width='1' opacity='0.28'/><rect x='16' y='90' width='48' height='19' rx='9.5' fill='white' opacity='0.95'/><text x='40' y='100' text-anchor='middle' dominant-baseline='middle' font-family='Inter,sans-serif' font-size='13' font-weight='800' fill='#64748b'>$pitch_name</text></svg>";
                                } else {
                                    // SÂN 5 - TRỐNG KHẢ DỤNG: teal xanh, click được
                                    $has_available_slot = true;
                                    echo "<svg class='mini-pitch-svg available' data-pitch-name='$pitch_name' data-lock-keys='$pitch_name' data-mode='san_5' viewBox='0 0 80 115' title='Sân $pitch_name (Trống)'><rect class='pitch-fill' width='80' height='115' rx='9' fill='#0d9488'/><rect x='6' y='6' width='68' height='76' rx='3' fill='none' stroke='white' stroke-width='1.3' opacity='0.4'/><line x1='6' y1='44' x2='74' y2='44' stroke='white' stroke-width='1.3' opacity='0.4'/><circle cx='40' cy='44' r='9' fill='none' stroke='white' stroke-width='1.3' opacity='0.4'/><circle cx='40' cy='44' r='2' fill='white' opacity='0.5'/><rect x='20' y='6' width='40' height='10' fill='none' stroke='white' stroke-width='1' opacity='0.28'/><rect x='20' y='72' width='40' height='10' fill='none' stroke='white' stroke-width='1' opacity='0.28'/><rect x='16' y='90' width='48' height='19' rx='9.5' fill='white' opacity='0.95'/><text x='40' y='100' text-anchor='middle' dominant-baseline='middle' font-family='Inter,sans-serif' font-size='13' font-weight='800' fill='#0d9488'>$pitch_name</text></svg>";
                                }
                            }
                        } else {
                            // Chế độ xem & đặt Sân 7 (Ghép từ các sân 5)
                            foreach ($groups_san_7 as $g7) {
                                $g_name = $g7['name'];
                                $g_comps = $g7['components'];
                                $g_key = $g7['key'];

                                $conflict_confirmed = array_intersect($g_comps, $confirmed_sub_pitches);
                                $conflict_holding = array_intersect($g_comps, $holding_sub_pitches);
                                $is_group_confirmed = (count($conflict_confirmed) > 0);
                                $is_group_holding = (count($conflict_holding) > 0);

                                $g_num = $g7['num'] ?? 1;

                                if ($is_group_confirmed) {
                                    $conflict_str = implode(', ', $conflict_confirmed);
                                    $booked_style = $is_slot_expired ? "style='cursor: not-allowed; opacity: 0.5; pointer-events: none;'" : "style='cursor: not-allowed;'";
                                    // SÂN 7 - ĐÃ ĐẶT: đỏ, X mark
                                    echo "<svg class='mini-pitch-svg pitch-7 booked' $booked_style viewBox='0 0 95 130' title='Sân 7.$g_num bị chặn ($conflict_str đã đặt)'><rect width='95' height='130' rx='9' fill='#ef4444'/><rect x='7' y='8' width='81' height='96' rx='3' fill='none' stroke='white' stroke-width='1.3' opacity='0.2'/><line x1='7' y1='56' x2='88' y2='56' stroke='white' stroke-width='1.3' opacity='0.2'/><circle cx='47.5' cy='56' r='11' fill='none' stroke='white' stroke-width='1.3' opacity='0.2'/><rect x='24' y='8' width='47' height='13' fill='none' stroke='white' stroke-width='1' opacity='0.15'/><path d='M 38.5 21 A 9 9 0 0 0 56.5 21' fill='none' stroke='white' stroke-width='1' opacity='0.15'/><rect x='24' y='91' width='47' height='13' fill='none' stroke='white' stroke-width='1' opacity='0.15'/><path d='M 38.5 91 A 9 9 0 0 1 56.5 91' fill='none' stroke='white' stroke-width='1' opacity='0.15'/><line x1='30' y1='34' x2='65' y2='78' stroke='white' stroke-width='3.5' stroke-linecap='round' opacity='0.9'/><line x1='65' y1='34' x2='30' y2='78' stroke='white' stroke-width='3.5' stroke-linecap='round' opacity='0.9'/><rect x='15' y='109' width='65' height='18' rx='9' fill='rgba(255,255,255,0.18)'/><text x='47.5' y='118' text-anchor='middle' dominant-baseline='middle' font-family='Inter,sans-serif' font-size='9' font-weight='700' fill='rgba(255,255,255,0.85)'>Đã đặt</text></svg>";
                                } elseif ($is_group_holding) {
                                    $has_holding_slot = true;
                                    $conflict_str = implode(', ', $conflict_holding);
                                    $holding_style = $is_slot_expired ? "style='cursor: not-allowed; opacity: 0.5; pointer-events: none;'" : "style='cursor: not-allowed;'";
                                    // SÂN 7 - ĐANG GIỮ CHỖ: vàng hổ phách, icon đồng hồ cát
                                    echo "<svg class='mini-pitch-svg pitch-7 holding' $holding_style viewBox='0 0 95 130' title='Sân 7.$g_num đang giữ chỗ ($conflict_str đang thanh toán)'><rect width='95' height='130' rx='9' fill='#f59e0b'/><rect x='7' y='8' width='81' height='96' rx='3' fill='none' stroke='white' stroke-width='1.3' opacity='0.25'/><line x1='7' y1='56' x2='88' y2='56' stroke='white' stroke-width='1.3' opacity='0.25'/><circle cx='47.5' cy='56' r='11' fill='none' stroke='white' stroke-width='1.3' opacity='0.25'/><circle cx='47.5' cy='56' r='16' fill='rgba(255,255,255,0.2)'/><text x='47.5' y='62' text-anchor='middle' font-size='18' fill='white'>⏳</text><rect x='15' y='109' width='65' height='18' rx='9' fill='rgba(255,255,255,0.25)'/><text x='47.5' y='118' text-anchor='middle' dominant-baseline='middle' font-family='Inter,sans-serif' font-size='9' font-weight='700' fill='white'>Đang giữ</text></svg>";
                                } elseif ($is_slot_expired) {
                                    // SÂN 7 - ĐÃ QUA GIỜ: màu xám (kiểu ẩn), không click được
                                    echo "<svg class='mini-pitch-svg pitch-7 expired' style='cursor: not-allowed; pointer-events: none; opacity: 0.6;' viewBox='0 0 95 130' title='$g_name (Đã qua giờ)'><rect class='pitch-fill' width='95' height='130' rx='9' fill='#94a3b8'/><rect x='7' y='8' width='81' height='96' rx='3' fill='none' stroke='white' stroke-width='1.3' opacity='0.4'/><line x1='7' y1='56' x2='88' y2='56' stroke='white' stroke-width='1.3' opacity='0.4'/><circle cx='47.5' cy='56' r='11' fill='none' stroke='white' stroke-width='1.3' opacity='0.4'/><circle cx='47.5' cy='56' r='2' fill='white' opacity='0.5'/><rect x='24' y='8' width='47' height='13' fill='none' stroke='white' stroke-width='1' opacity='0.32'/><path d='M 38.5 21 A 9 9 0 0 0 56.5 21' fill='none' stroke='white' stroke-width='1' opacity='0.32'/><rect x='24' y='91' width='47' height='13' fill='none' stroke='white' stroke-width='1' opacity='0.32'/><path d='M 38.5 91 A 9 9 0 0 1 56.5 91' fill='none' stroke='white' stroke-width='1' opacity='0.32'/><rect x='15' y='109' width='65' height='19' rx='9.5' fill='white' opacity='0.95'/><text x='47.5' y='118.5' text-anchor='middle' dominant-baseline='middle' font-family='Inter,sans-serif' font-size='12' font-weight='800' fill='#64748b'>7.$g_num</text></svg>";
                                } else {
                                    // SÂN 7 - TRỐNG KHẢ DỤNG: teal xanh, click được
                                    $has_available_slot = true;
                                    echo "<svg class='mini-pitch-svg pitch-7 available' data-pitch-name='$g_name' data-lock-keys='$g_key' data-mode='san_7' viewBox='0 0 95 130' title='$g_name (Trống)'><rect class='pitch-fill' width='95' height='130' rx='9' fill='#0d9488'/><rect x='7' y='8' width='81' height='96' rx='3' fill='none' stroke='white' stroke-width='1.3' opacity='0.4'/><line x1='7' y1='56' x2='88' y2='56' stroke='white' stroke-width='1.3' opacity='0.4'/><circle cx='47.5' cy='56' r='11' fill='none' stroke='white' stroke-width='1.3' opacity='0.4'/><circle cx='47.5' cy='56' r='2' fill='white' opacity='0.5'/><rect x='24' y='8' width='47' height='13' fill='none' stroke='white' stroke-width='1' opacity='0.32'/><path d='M 38.5 21 A 9 9 0 0 0 56.5 21' fill='none' stroke='white' stroke-width='1' opacity='0.32'/><rect x='24' y='91' width='47' height='13' fill='none' stroke='white' stroke-width='1' opacity='0.32'/><path d='M 38.5 91 A 9 9 0 0 1 56.5 91' fill='none' stroke='white' stroke-width='1' opacity='0.32'/><rect x='15' y='109' width='65' height='19' rx='9.5' fill='white' opacity='0.95'/><text x='47.5' y='118.5' text-anchor='middle' dominant-baseline='middle' font-family='Inter,sans-serif' font-size='12' font-weight='800' fill='#0d9488'>7.$g_num</text></svg>";
                                }
                            }
                        }
                        ?>
                    </div>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <?php if ($is_slot_expired): ?>
                        <button type="button" class="btn-thanhtoan btn-expired" disabled style="background: #f1f5f9; color: #64748b; cursor: not-allowed; border: 1px solid #e2e8f0; box-shadow: none; font-size: 0.85rem; padding: 7px 14px; border-radius: 8px;">
                            <i class="fas fa-ban"></i> Đã Quá Giờ
                        </button>
                    <?php elseif ($has_available_slot): ?>
                        <form onsubmit="submitBooking(event, this)" style="margin:0; display: inline-flex; justify-content: center; align-items: center;">
                            <input type="hidden" name="khung_gio_id" value="<?php echo $kg['khung_gio_id']; ?>">
                            <input type="hidden" name="ngay_dat" value="<?php echo $ngay_dat; ?>">
                            <input type="hidden" name="loai_san_dat" value="<?php echo $view_mode; ?>">
                            <input type="hidden" name="ten_san_con" class="hidden-pitch-name" value="">
                            <input type="hidden" name="san_con_khoa" class="hidden-lock-keys" value="">
                            <button type="submit" class="btn-thanhtoan" style="background: #0d9488; color: white; border: none; font-size: 0.85rem; padding: 7px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; box-shadow: 0 2px 6px rgba(13, 148, 136, 0.25);">
                                Đặt Ngay
                            </button>
                        </form>
                    <?php elseif ($has_holding_slot): ?>
                        <button type="button" class="btn-thanhtoan" disabled style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; cursor: not-allowed; box-shadow: none; font-size: 0.82rem; padding: 7px 12px; border-radius: 8px; font-weight: 700;" title="Sân đang có người giữ chỗ thanh toán trong 10 phút">
                            <i class="fas fa-hourglass-half"></i> Đang Giữ Chỗ
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn-thanhtoan" disabled style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; cursor: not-allowed; box-shadow: none; font-size: 0.85rem; padding: 7px 16px; border-radius: 8px; font-weight: 700;">
                            Đã Hết
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>