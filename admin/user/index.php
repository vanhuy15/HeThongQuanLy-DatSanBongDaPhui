<?php
require_once dirname(__DIR__) . '/bootstrap.php';
include '../header_admin.php';

// Thống kê người dùng qua Model User (Chuẩn MVC)
$user_stats = \Models\User::getCustomerStats();
$total_users = $user_stats['total'];
$avg_reputation = $user_stats['avg_reputation'];
$banned_users = $user_stats['banned'];

// Lấy danh sách khách hàng qua Model
$customers_list = \Models\User::getAllCustomers();
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div>
        <h1 class="page-title">Quản lý Khách hàng</h1>
        <p class="page-subtitle">Quản lý tài khoản, phân quyền và điểm uy tín của khách hàng.</p>
    </div>
</div>

<!-- DATA TABLE CARD -->
<div class="table-card">
    <div class="table-card-header">
        <h3 class="table-card-title">Danh sách Khách hàng</h3>
        <span style="font-size: 0.85rem; color: var(--text-muted);">Tổng cộng: <strong><?php echo $total_users; ?></strong> tài khoản</span>
    </div>

    <div class="admin-table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>KHÁCH HÀNG</th>
                    <th>VAI TRÒ</th>
                    <th>ĐIỂM UY TÍN</th>
                    <th>TRẠNG THÁI</th>
                    <th style="text-align: center;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($customers_list)) : ?>
                    <?php foreach ($customers_list as $row) : 
                        $first_char = mb_substr(trim($row['ho_ten']), 0, 1, 'UTF-8');
                        $uy_tin = (int)($row['diem_uy_tin'] ?? 100);
                        $bar_color = ($uy_tin >= 80) ? '#007a64' : (($uy_tin >= 60) ? '#0284c7' : (($uy_tin >= 40) ? '#d97706' : '#dc2626'));
                        $avatar_file = !empty($row['avatar']) ? $row['avatar'] : '';
                        $has_avatar = !empty($avatar_file) && (file_exists(dirname(__DIR__, 2) . '/' . ltrim($avatar_file, '/')) || filter_var($avatar_file, FILTER_VALIDATE_URL));
                        $avatar_src = $has_avatar ? (filter_var($avatar_file, FILTER_VALIDATE_URL) ? $avatar_file : '/quanlysanbong/' . ltrim($avatar_file, '/')) : '';
                    ?>
                    <tr>
                        <!-- Khách hàng -->
                        <td>
                            <div class="user-cell">
                                <?php if ($has_avatar): ?>
                                    <img src="<?php echo htmlspecialchars($avatar_src); ?>" alt="<?php echo htmlspecialchars($row['ho_ten']); ?>" class="avatar-letter" style="object-fit: cover; width: 36px; height: 36px; border-radius: 50%; border: 1.5px solid #e2e8f0;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                    <div class="avatar-letter avatar-teal" style="display: none;">
                                        <?php echo htmlspecialchars(mb_strtoupper($first_char, 'UTF-8')); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="avatar-letter avatar-teal">
                                        <?php echo htmlspecialchars(mb_strtoupper($first_char, 'UTF-8')); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="user-cell-info">
                                    <span class="user-cell-name"><?php echo htmlspecialchars($row['ho_ten']); ?></span>
                                    <span class="user-cell-sub"><?php echo htmlspecialchars($row['email'] ?: $row['sdt']); ?></span>
                                </div>
                            </div>
                        </td>

                        <!-- Vai trò -->
                        <td>
                            <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                                Khách hàng
                            </span>
                        </td>

                        <!-- Điểm uy tín (Progress Bar) -->
                        <td style="min-width: 160px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="flex: 1; background: #e2e8f0; height: 8px; border-radius: var(--radius-full); overflow: hidden;">
                                    <div style="width: <?php echo max(0, min(100, $uy_tin)); ?>%; background: <?php echo $bar_color; ?>; height: 100%; border-radius: var(--radius-full); transition: width 0.3s ease;"></div>
                                </div>
                                <span style="font-weight: 800; font-size: 0.88rem; color: <?php echo $bar_color; ?>; min-width: 28px;">
                                    <?php echo $uy_tin; ?>
                                </span>
                            </div>
                            <?php if (!empty($row['so_lan_bung_san']) && (int)$row['so_lan_bung_san'] > 0 && $uy_tin < 80): ?>
                                <span style="color: #dc2626; font-size: 0.72rem; font-weight: 700; display: block; margin-top: 2px;">
                                    Bùng <?php echo $row['so_lan_bung_san']; ?> lần
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Trạng thái -->
                        <td>
                            <?php if ($row['trang_thai'] == 'active' || is_null($row['trang_thai'])) : ?>
                                <span class="badge badge-xacnhan"><span class="badge-dot"></span> Hoạt động</span>
                            <?php elseif ($row['trang_thai'] == 'banned') : ?>
                                <span class="badge badge-huy"><span class="badge-dot"></span> Đã khóa</span>
                            <?php else : ?>
                                <span class="badge badge-hoantien"><span class="badge-dot"></span> Vô hiệu hóa</span>
                            <?php endif; ?>
                        </td>

                        <!-- Thao tác -->
                        <td style="text-align: center;">
                            <div class="action-buttons-group" style="justify-content: center;">
                                <a href="sua.php?id=<?php echo $row['nguoi_dung_id']; ?>" class="btn-action-icon btn-edit" title="Chỉnh sửa thông tin">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button type="button" class="btn-action-icon" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;" title="Xem lịch sử tín dụng" onclick="openReputationModal(<?php echo $row['nguoi_dung_id']; ?>)">
                                    <i class="fas fa-history"></i>
                                </button>

                                <button type="button" class="btn-action-icon" style="background: #f0fdfa; color: #0d9488; border: 1px solid #99f6e4;" title="Quản lý điểm uy tín" onclick="openAdjustReputationModal(<?php echo $row['nguoi_dung_id']; ?>, '<?php echo addslashes(htmlspecialchars($row['ho_ten'])); ?>', <?php echo $uy_tin; ?>)">
                                    <i class="fas fa-sliders-h"></i>
                                </button>

                                <?php if ($row['trang_thai'] == 'banned') : ?>
                                    <form method="POST" action="capnhat_trangthai.php" style="display:inline;" onsubmit="return confirm('Mở khóa tài khoản này?');">
                                        <?php echo function_exists('csrf_field') ? csrf_field() : ''; ?>
                                        <input type="hidden" name="id" value="<?php echo $row['nguoi_dung_id']; ?>">
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="btn-action-icon btn-accept" title="Mở khóa tài khoản">
                                            <i class="fas fa-unlock"></i>
                                        </button>
                                    </form>
                                <?php else : ?>
                                    <form method="POST" action="capnhat_trangthai.php" style="display:inline;" onsubmit="return confirm('Khóa tài khoản này?');">
                                        <?php echo function_exists('csrf_field') ? csrf_field() : ''; ?>
                                        <input type="hidden" name="id" value="<?php echo $row['nguoi_dung_id']; ?>">
                                        <input type="hidden" name="status" value="banned">
                                        <button type="submit" class="btn-action-icon btn-cancel" title="Khóa tài khoản">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            Chưa có tài khoản khách hàng nào trong hệ thống.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Đồng bộ tự động các trận bóng đã kết thúc
syncCompletedMatchesReputation($conn);
?>
<!-- MODAL XEM LỊCH SỬ TÍN DỤNG -->
<div id="reputationHistoryModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: #ffffff; width: 95%; max-width: 820px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); overflow: hidden; margin: 20px; animation: modalFadeIn 0.2s ease;">
        <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a;">
                Lịch sử điểm tín dụng
            </h3>
            <button type="button" onclick="closeReputationModal()" style="background: none; border: none; font-size: 1.4rem; color: #64748b; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <div id="reputationModalBody" style="padding: 24px;">
            <div style="text-align: center; padding: 30px; color: #64748b;">
                <i class="fas fa-spinner fa-spin" style="font-size: 1.8rem; color: #007a64; margin-bottom: 10px;"></i>
                <p>Đang tải dữ liệu lịch sử tín dụng...</p>
            </div>
        </div>
        <div style="padding: 14px 24px; border-top: 1px solid #e2e8f0; text-align: right; background: #f8fafc;">
            <button type="button" class="btn-outline-modern" onclick="closeReputationModal()" style="padding: 8px 18px; font-size: 0.88rem;">Đóng</button>
        </div>
    </div>
</div>

<!-- MODAL ĐIỀU CHỈNH ĐIỂM UY TÍN -->
<div id="adjustReputationModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: #ffffff; width: 95%; max-width: 480px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); overflow: hidden; margin: 20px; animation: modalFadeIn 0.2s ease;">
        <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-sliders-h" style="color: #0d9488;"></i> Quản lý điểm uy tín
            </h3>
            <button type="button" onclick="closeAdjustReputationModal()" style="background: none; border: none; font-size: 1.4rem; color: #64748b; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <form id="adjustReputationForm" onsubmit="handleAdjustReputationSubmit(event)">
            <input type="hidden" name="user_id" id="adjustUserId" value="">
            <input type="hidden" name="csrf_token" id="adjustCsrfToken" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            <div style="padding: 20px;">
                <!-- Khách hàng & Điểm hiện tại -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b;">KHÁCH HÀNG</div>
                        <div id="adjustUserName" style="font-weight: 700; color: #0f172a; font-size: 0.95rem;">-</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b;">ĐIỂM HIỆN TẠI</div>
                        <div id="adjustCurrentScore" style="font-weight: 800; color: #0d9488; font-size: 1.15rem;">-</div>
                    </div>
                </div>

                <!-- Khôi phục nhanh 100đ -->
                <div style="margin-bottom: 16px; padding: 12px 14px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 700; color: #15803d; font-size: 0.85rem;">Khôi phục nhanh 100đ</div>
                        <div style="font-size: 0.75rem; color: #16a34a;">Reset về 100 điểm & xóa số lần bùng sân</div>
                    </div>
                    <button type="button" onclick="quickResetFromModal()" style="background: #16a34a; color: #ffffff; border: none; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 5px; transition: all 0.2s;">
                        <i class="fas fa-undo"></i> Reset 100đ
                    </button>
                </div>

                <!-- Tùy chỉnh điểm -->
                <div style="margin-bottom: 14px;">
                    <label for="adjustNewScore" style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Điểm mới tùy chỉnh (0 - 100):</label>
                    <input type="number" id="adjustNewScore" name="new_score" min="0" max="100" required style="width: 100%; padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 700; font-size: 0.95rem; color: #0f172a; box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 14px;">
                    <label for="adjustReason" style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Lý do điều chỉnh (Ghi nhật ký kiểm toán):</label>
                    <textarea id="adjustReason" name="reason" rows="3" placeholder="Ví dụ: Khách đã thanh toán bù tiền cọc hoặc được ân xá" required style="width: 100%; padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem; color: #0f172a; resize: vertical; box-sizing: border-box;"></textarea>
                </div>
            </div>
            <div style="padding: 12px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; background: #f8fafc;">
                <button type="button" class="btn-outline-modern" onclick="closeAdjustReputationModal()" style="padding: 8px 16px; font-size: 0.85rem;">Hủy</button>
                <button type="submit" id="btnSubmitAdjustRep" style="background: #0d9488; color: #ffffff; border: none; padding: 8px 18px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
function quickResetFromModal() {
    const userId = document.getElementById('adjustUserId').value;
    const userName = document.getElementById('adjustUserName').textContent;
    if (!userId) return;

    if (!confirm('Khôi phục điểm uy tín của khách hàng "' + userName + '" về mức tối đa 100 điểm?')) {
        return;
    }
    const csrfToken = document.getElementById('adjustCsrfToken') ? document.getElementById('adjustCsrfToken').value : '<?php echo $_SESSION['csrf_token'] ?? ''; ?>';
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('action_type', 'reset_100');
    formData.append('csrf_token', csrfToken);

    fetch('capnhat_uytin.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra khi khôi phục điểm!');
        }
    })
    .catch(err => {
        alert('Lỗi kết nối máy chủ: ' + err);
    });
}

function openAdjustReputationModal(userId, userName, currentScore) {
    const modal = document.getElementById('adjustReputationModal');
    if (!modal) return;
    document.getElementById('adjustUserId').value = userId;
    document.getElementById('adjustUserName').textContent = userName;
    document.getElementById('adjustCurrentScore').textContent = currentScore + ' điểm';
    document.getElementById('adjustNewScore').value = currentScore;
    document.getElementById('adjustReason').value = '';
    modal.style.display = 'flex';
}

function closeAdjustReputationModal() {
    const modal = document.getElementById('adjustReputationModal');
    if (modal) modal.style.display = 'none';
}

function handleAdjustReputationSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitAdjustRep');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Đang lưu...';

    const form = document.getElementById('adjustReputationForm');
    const formData = new FormData(form);
    formData.append('action_type', 'adjust');

    fetch('capnhat_uytin.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = originalText;
        if (data.success) {
            alert(data.message);
            closeAdjustReputationModal();
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra khi điều chỉnh điểm!');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = originalText;
        alert('Lỗi kết nối máy chủ: ' + err);
    });
}

window.addEventListener('click', function(e) {
    const adjModal = document.getElementById('adjustReputationModal');
    if (adjModal && e.target === adjModal) {
        closeAdjustReputationModal();
    }
});
</script>

<?php include '../footer_admin.php'; ?>