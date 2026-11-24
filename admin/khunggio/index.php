<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_admin();

$error = $success = "";
$san_id_filter = isset($_GET['san_id']) ? intval($_GET['san_id']) : 0;

// ==========================================================
// 1. XỬ LÝ THÊM KHUNG GIỜ MỚI (Xử lý trước khi gửi Header HTML)
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $san_id_add = intval($_POST['san_id']);
    $gio_bat_dau = clean_input($_POST['gio_bat_dau']);
    $gio_ket_thuc = clean_input($_POST['gio_ket_thuc']);
    $gia_tien = !empty($_POST['gia_tien']) ? floatval($_POST['gia_tien']) : 0;
    $gia_tien_san_7 = !empty($_POST['gia_tien_san_7']) ? floatval($_POST['gia_tien_san_7']) : NULL;

    if ($san_id_add <= 0 || empty($gio_bat_dau) || empty($gio_ket_thuc)) {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $check = $conn->prepare("SELECT khung_gio_id FROM khung_gio WHERE san_id=? AND gio_bat_dau=? AND gio_ket_thuc=?");
        $check->bind_param("iss", $san_id_add, $gio_bat_dau, $gio_ket_thuc);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Khung giờ này đã tồn tại trong hệ thống!";
        } else {
            $stmt = $conn->prepare("INSERT INTO khung_gio (san_id, gio_bat_dau, gio_ket_thuc, gia_tien, gia_tien_san_7) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issdd", $san_id_add, $gio_bat_dau, $gio_ket_thuc, $gia_tien, $gia_tien_san_7);
            if ($stmt->execute()) {
                $success = "Thêm khung giờ thành công!";
                header("Location: index.php?san_id=$san_id_add&success=" . urlencode($success));
                exit();
            } else {
                \Core\Logger::error("Lỗi thêm khung giờ: " . $stmt->error);
                $error = "Có lỗi xảy ra khi lưu khung giờ. Vui lòng kiểm tra lại.";
            }
            $stmt->close();
        }
        $check->close();
    }
}

// ==========================================================
// 2. XỬ LÝ XÓA KHUNG GIỜ
// ==========================================================
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $khung_gio_id = intval($_GET['khung_gio_id']);
    $stmt_chk = $conn->prepare("SELECT COUNT(*) FROM dat_san WHERE khung_gio_id = ?");
    $stmt_chk->bind_param("i", $khung_gio_id);
    $stmt_chk->execute();
    $check_fk = (int)($stmt_chk->get_result()->fetch_row()[0] ?? 0);
    $stmt_chk->close();

    if ($check_fk > 0) {
        $error = "Không thể xóa: Có đơn đặt sân đang dùng khung giờ này.";
    } else {
        $stmt_del = $conn->prepare("DELETE FROM khung_gio WHERE khung_gio_id = ?");
        $stmt_del->bind_param("i", $khung_gio_id);
        $stmt_del->execute();
        $stmt_del->close();
        $success = "Đã xóa khung giờ.";
        header("Location: index.php?san_id=$san_id_filter&success=".urlencode($success));
        exit();
    }
}

if (isset($_GET['success'])) $success = $_GET['success'];
if (isset($_GET['error'])) $error = $_GET['error'];

// Gọi Header sau khi đã hoàn tất mọi chuyển hướng (tránh lỗi Cannot modify header information)
include '../header_admin.php';

// ==========================================================
// 3. LẤY DỮ LIỆU SÂN & PHÂN TRANG
// ==========================================================
$san_list_result = $conn->query("SELECT san_id, ten_san, loai_san, quy_tac_ghep FROM san_bong ORDER BY ten_san");

if ($san_id_filter == 0 && $san_list_result->num_rows > 0) {
    // Mặc định là Sân Đại Học Sư Phạm
    $su_pham_id = 0;
    while ($sb = $san_list_result->fetch_assoc()) {
        if (stripos($sb['ten_san'], 'Sư Phạm') !== false || (int)$sb['san_id'] === 1) {
            $su_pham_id = (int)$sb['san_id'];
            break;
        }
    }
    $san_list_result->data_seek(0);
    $first_san = $san_list_result->fetch_assoc();
    $san_id_filter = $su_pham_id ?: $first_san['san_id'];
    $san_list_result->data_seek(0);
}

$current_loai_san = 'san_5';
$current_san_name = '';
$san_list_result->data_seek(0);
while ($san = $san_list_result->fetch_assoc()) {
    if ($san['san_id'] == $san_id_filter) {
        $current_loai_san = $san['loai_san'];
        $current_san_name = $san['ten_san'];
        break;
    }
}
$san_list_result->data_seek(0);

$so_luong_moi_trang = 10;
$trang_hien_tai = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($trang_hien_tai < 1) $trang_hien_tai = 1;

$stmt_cnt = $conn->prepare("SELECT COUNT(*) FROM khung_gio WHERE san_id = ?");
$stmt_cnt->bind_param("i", $san_id_filter);
$stmt_cnt->execute();
$total_records = (int)($stmt_cnt->get_result()->fetch_row()[0] ?? 0);
$stmt_cnt->close();
$tong_so_trang = ceil($total_records / $so_luong_moi_trang);
$offset = ($trang_hien_tai - 1) * $so_luong_moi_trang;

$stmt_kg = $conn->prepare("SELECT * FROM khung_gio WHERE san_id = ? ORDER BY gio_bat_dau ASC LIMIT ? OFFSET ?");
$stmt_kg->bind_param("iii", $san_id_filter, $so_luong_moi_trang, $offset);
$stmt_kg->execute();
$khung_gio_list = $stmt_kg->get_result();
$stt = $offset;
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div>
        <h1 class="page-title">Khung giờ &amp; Bảng giá</h1>
        <p class="page-subtitle">Cấu hình giá thuê sân theo từng khung giờ trong ngày.</p>
    </div>
    
    <div class="page-actions">
        <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: center;">
            <label style="font-weight: 700; color: var(--text-heading); font-size: 0.88rem;">Chọn sân:</label>
            <select name="san_id" onchange="this.form.submit()" style="padding: 9px 16px; border-radius: var(--radius-md); border: 1.5px solid var(--primary); font-weight: 700; color: var(--primary); background: #ffffff; outline: none;">
                <?php while ($san = $san_list_result->fetch_assoc()) : ?>
                    <option value="<?php echo $san['san_id']; ?>" <?php echo ($san['san_id'] == $san_id_filter) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($san['ten_san']); ?> 
                        (<?php echo ($san['loai_san'] == 'san_5_7') ? 'Sân 5 & 7' : (($san['loai_san'] == 'san_7') ? 'Sân 7' : 'Sân 5'); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>
</div>

<?php if ($success): ?>
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: var(--radius-md); padding: 14px 20px; margin-bottom: 20px; color: #047857; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-check-circle" style="font-size: 1.2rem;"></i>
        <span><?php echo htmlspecialchars($success); ?></span>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: var(--radius-md); padding: 14px 20px; margin-bottom: 20px; color: #b91c1c; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-exclamation-circle" style="font-size: 1.2rem;"></i>
        <span><?php echo htmlspecialchars($error); ?></span>
    </div>
<?php endif; ?>

<!-- 2-COLUMN LAYOUT (TABLE & ADD FORM) -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
    
    <!-- LEFT: STANDARD RATE TABLE -->
    <div class="table-card">
        <div class="table-card-header">
            <div>
                <h3 class="table-card-title">Bảng giá tiêu chuẩn — <?php echo htmlspecialchars($current_san_name); ?></h3>
            </div>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>KHUNG GIỜ</th>
                        <th>GIÁ SÂN 5</th>
                        <th>GIÁ SÂN 7</th>
                        <th style="text-align: center;">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($khung_gio_list && $khung_gio_list->num_rows > 0): ?>
                        <?php while($kg = $khung_gio_list->fetch_assoc()): 
                            $stt++;
                        ?>
                        <tr>
                            <!-- Khung giờ -->
                            <td>
                                <strong style="color: var(--text-heading); font-size: 0.95rem;">
                                    <?php echo date("H:i", strtotime($kg['gio_bat_dau'])); ?> - <?php echo date("H:i", strtotime($kg['gio_ket_thuc'])); ?>
                                </strong>
                            </td>

                            <!-- Giá sân 5 -->
                            <td>
                                <?php if ($current_loai_san == 'san_7'): ?>
                                    <span style="color: var(--text-subtle);">-</span>
                                <?php else: ?>
                                    <strong style="color: #059669; font-size: 0.95rem;"><?php echo number_format($kg['gia_tien']); ?> đ</strong>
                                <?php endif; ?>
                            </td>

                            <!-- Giá sân 7 -->
                            <td>
                                <?php if ($current_loai_san == 'san_5'): ?>
                                    <span style="color: var(--text-subtle);">-</span>
                                <?php else: ?>
                                    <strong style="color: #d97706; font-size: 0.95rem;"><?php echo !empty($kg['gia_tien_san_7']) ? number_format($kg['gia_tien_san_7']) . ' đ' : '-'; ?></strong>
                                <?php endif; ?>
                            </td>

                            <!-- Thao tác -->
                            <td style="text-align: center;">
                                <div class="action-buttons-group" style="justify-content: center;">
                                    <a href="sua.php?khung_gio_id=<?php echo $kg['khung_gio_id']; ?>" class="btn-action-icon btn-edit" title="Sửa khung giờ">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?action=delete&khung_gio_id=<?php echo $kg['khung_gio_id']; ?>&san_id=<?php echo $san_id_filter; ?>" 
                                       class="btn-action-icon btn-cancel" 
                                       onclick="return confirm('Xóa khung giờ này?');" title="Xóa khung giờ">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                Sân này hiện chưa có khung giờ nào được thiết lập.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($tong_so_trang > 1) : ?>
            <div class="admin-pagination-box" style="margin-top: 0;">
                <?php 
                    $page_params = "san_id=" . urlencode($san_id_filter);
                    $pagination_range = function_exists('get_pagination_range') ? get_pagination_range($trang_hien_tai, $tong_so_trang, 1) : range(1, $tong_so_trang);
                ?>
                <?php if ($trang_hien_tai > 1): ?>
                    <a href="index.php?<?php echo $page_params; ?>&page=<?php echo $trang_hien_tai - 1; ?>" class="admin-page-btn" title="Trang trước">
                        <i class="fas fa-chevron-left" style="font-size: 0.75rem;"></i>
                    </a>
                <?php endif; ?>

                <?php foreach ($pagination_range as $p): ?>
                    <?php if ($p === '...'): ?>
                        <span class="admin-page-ellipsis">...</span>
                    <?php else: ?>
                        <a href="index.php?<?php echo $page_params; ?>&page=<?php echo $p; ?>" 
                           class="admin-page-btn <?php echo ($p == $trang_hien_tai) ? 'active' : ''; ?>">
                            <?php echo $p; ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if ($trang_hien_tai < $tong_so_trang): ?>
                    <a href="index.php?<?php echo $page_params; ?>&page=<?php echo $trang_hien_tai + 1; ?>" class="admin-page-btn" title="Trang sau">
                        <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT: ADD NEW TIME SLOT FORM -->
    <div class="admin-form-card">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-heading); margin-bottom: 18px;">
            Thêm Khung Giờ Mới
        </h3>
        
        <form method="POST" action="index.php?san_id=<?php echo $san_id_filter; ?>">
            <?php echo function_exists("csrf_field") ? csrf_field() : ""; ?>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="san_id" value="<?php echo $san_id_filter; ?>">

            <div class="form-row">
                <div class="form-col">
                    <label class="form-label">Giờ bắt đầu</label>
                    <input type="time" name="gio_bat_dau" id="gio_bat_dau" class="form-control" required>
                </div>
                <div class="form-col">
                    <label class="form-label">Giờ kết thúc</label>
                    <input type="time" name="gio_ket_thuc" id="gio_ket_thuc" class="form-control" required>
                </div>
            </div>

            <?php
                $is_san_5 = ($current_loai_san == 'san_5');
                $is_san_7 = ($current_loai_san == 'san_7');
                
                $req_san_5 = ($is_san_7) ? "disabled readonly style='background-color: #f1f5f9; cursor: not-allowed; opacity: 0.6;'" : "required";
                $req_san_7 = ($is_san_5) ? "disabled readonly style='background-color: #f1f5f9; cursor: not-allowed; opacity: 0.6;'" : "";
                if ($current_loai_san == 'san_7') {
                    $req_san_7 = "required";
                }
            ?>
            <div class="form-group">
                <label class="form-label">Giá Sân 5 (VND) <?php echo $is_san_7 ? '' : '<span style="color:#ef4444;">*</span>'; ?></label>
                <input type="number" name="gia_tien" min="0" step="1000" class="form-control" <?php echo $req_san_5; ?> placeholder="ví dụ: 300000">
            </div>

            <div class="form-group">
                <label class="form-label">Giá Sân 7 (VND) <?php echo $current_loai_san == 'san_7' ? '<span style="color:#ef4444;">*</span>' : ''; ?></label>
                <input type="number" name="gia_tien_san_7" min="0" step="1000" class="form-control" <?php echo $req_san_7; ?> placeholder="ví dụ: 600000">
            </div>

            <button type="submit" class="btn-primary-modern" style="width: 100%; justify-content: center; margin-top: 10px;">
                Thêm khung giờ
            </button>
        </form>
    </div>

</div>

<?php 
$stmt_kg->close();
include '../footer_admin.php'; 
?>
