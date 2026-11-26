<?php

namespace Models;

use Config\Database;

class User
{

    /**
     * Tìm người dùng theo ID
     */
    public static function findById($id)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE nguoi_dung_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res;
    }

    /**
     * Tìm người dùng theo tên đăng nhập (Phân biệt chữ hoa / chữ thường chính xác - Case Sensitive)
     */
    public static function findByUsername($username)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE BINARY ten_dang_nhap = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Kiểm tra hai tầng ở cấp PHP để đảm bảo tuyệt đối khớp chính xác từng ký tự
        if ($res && $res['ten_dang_nhap'] !== $username) {
            return null;
        }

        return $res;
    }

    /**
     * Tìm người dùng theo email
     */
    public static function findByEmail($email)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res;
    }

    /**
     * Tìm người dùng theo số điện thoại
     */
    public static function findByPhone($sdt)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE sdt = ?");
        $stmt->bind_param("s", $sdt);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res;
    }

    /**
     * Tạo tài khoản người dùng mới
     */
    public static function create($username, $hashedPassword, $hoTen, $email, $sdt, $vaiTro = 'khach_hang')
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, ho_ten, email, sdt, vai_tro, diem_uy_tin, trang_thai) VALUES (?, ?, ?, ?, ?, ?, 100, 'active')");
        $stmt->bind_param("ssssss", $username, $hashedPassword, $hoTen, $email, $sdt, $vaiTro);
        $res = $stmt->execute();
        $insertId = $conn->insert_id;
        $stmt->close();
        return $res ? $insertId : false;
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public static function updateProfile($id, $hoTen, $sdt, $email, $avatar = null)
    {
        $conn = Database::getConnection();
        if ($avatar) {
            $stmt = $conn->prepare("UPDATE nguoi_dung SET ho_ten = ?, sdt = ?, email = ?, avatar = ? WHERE nguoi_dung_id = ?");
            $stmt->bind_param("ssssi", $hoTen, $sdt, $email, $avatar, $id);
        } else {
            $stmt = $conn->prepare("UPDATE nguoi_dung SET ho_ten = ?, sdt = ?, email = ? WHERE nguoi_dung_id = ?");
            $stmt->bind_param("sssi", $hoTen, $sdt, $email, $id);
        }
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    /**
     * Đổi mật khẩu
     */
    public static function updatePassword($id, $newHashedPassword)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE nguoi_dung SET mat_khau = ? WHERE nguoi_dung_id = ?");
        $stmt->bind_param("si", $newHashedPassword, $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    /**
     * Thay đổi điểm uy tín (cộng hoặc trừ) và ghi log lịch sử
     */
    public static function updateReputation($id, $delta, $actionType, $note = '')
    {
        $conn = Database::getConnection();

        // Cập nhật điểm uy tín (không vượt quá 100, không dưới 0)
        if ($delta >= 0) {
            $stmt = $conn->prepare("UPDATE nguoi_dung SET diem_uy_tin = LEAST(100, diem_uy_tin + ?) WHERE nguoi_dung_id = ?");
        } else {
            $absDelta = abs($delta);
            $stmt = $conn->prepare("UPDATE nguoi_dung SET diem_uy_tin = GREATEST(0, diem_uy_tin - ?) WHERE nguoi_dung_id = ?");
            $delta = -$absDelta;
        }
        $val = abs($delta);
        $stmt->bind_param("ii", $val, $id);
        $stmt->execute();
        $stmt->close();

        // Lấy điểm sau cùng
        $currentScore = (int)($conn->query("SELECT diem_uy_tin FROM nguoi_dung WHERE nguoi_dung_id = $id")->fetch_row()[0] ?? 100);

        // Ghi nhận vào bảng lịch sử
        $histStmt = $conn->prepare("INSERT INTO lich_su_uy_tin (nguoi_dung_id, so_diem, diem_sau_cung, loai_hanh_dong, ghi_chu) VALUES (?, ?, ?, ?, ?)");
        $histStmt->bind_param("iiiss", $id, $delta, $currentScore, $actionType, $note);
        $histStmt->execute();
        $histStmt->close();

        return $currentScore;
    }

    /**
     * Lấy lịch sử thay đổi điểm uy tín
     */
    public static function getReputationHistory($id, $limit = 20)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM lich_su_uy_tin WHERE nguoi_dung_id = ? ORDER BY ngay_tao DESC LIMIT ?");
        $stmt->bind_param("ii", $id, $limit);
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
     * Cập nhật trạng thái tài khoản (active, banned, inactive)
     */
    public static function updateStatus($id, $status)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE nguoi_dung SET trang_thai = ? WHERE nguoi_dung_id = ?");
        $stmt->bind_param("si", $status, $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    /**
     * Đếm tổng số khách hàng
     */
    public static function countCustomers()
    {
        $conn = Database::getConnection();
        $res = $conn->query("SELECT COUNT(*) FROM nguoi_dung WHERE vai_tro = 'khach_hang'");
        return (int)($res->fetch_row()[0] ?? 0);
    }

    /**
     * Thống kê thông tin khách hàng cho Admin
     */
    public static function getCustomerStats(): array
    {
        $conn = Database::getConnection();
        $total = $conn->query("SELECT COUNT(*) FROM nguoi_dung WHERE vai_tro = 'khach_hang'")->fetch_row()[0] ?? 0;
        $avg = $conn->query("SELECT ROUND(AVG(diem_uy_tin)) FROM nguoi_dung WHERE vai_tro = 'khach_hang'")->fetch_row()[0] ?? 100;
        $banned = $conn->query("SELECT COUNT(*) FROM nguoi_dung WHERE vai_tro = 'khach_hang' AND trang_thai = 'banned'")->fetch_row()[0] ?? 0;
        return [
            'total' => (int)$total,
            'avg_reputation' => (int)$avg,
            'banned' => (int)$banned
        ];
    }

    /**
     * Lấy toàn bộ danh sách khách hàng
     */
    public static function getAllCustomers(): array
    {
        $conn = Database::getConnection();
        $res = $conn->query("SELECT * FROM nguoi_dung WHERE vai_tro = 'khach_hang' ORDER BY nguoi_dung_id DESC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Giới hạn điểm uy tín trong khoảng chuẩn [0, 100]
     */
    public static function clampReputation(int $score): int
    {
        return max(0, min(100, $score));
    }

    /**
     * Kiểm tra điều kiện được phép đặt sân (>= 40 điểm)
     */
    public static function canBook(int $score): bool
    {
        return $score >= 40;
    }

    /**
     * Kiểm tra điều kiện được phép thanh toán Tiền mặt (>= 60 điểm)
     */
    public static function canPayCash(int $score): bool
    {
        return $score >= 60;
    }

    /**
     * Tự động đồng bộ cộng +5 điểm uy tín cho các trận đấu đã hoàn thành
     */
    public static function syncCompletedMatches($conn = null, $userId = null)
    {
        if ($conn === null) {
            $conn = Database::getConnection();
        }
        $where_user = "";
        if ($userId !== null) {
            $uidInt = (int)$userId;
            $where_user = " AND ds.nguoi_dung_id = $uidInt";
        }

        $sql = "SELECT ds.dat_san_id, ds.nguoi_dung_id, sb.ten_san, ds.ngay_dat, kg.gio_ket_thuc,
                       CONCAT(ds.ngay_dat, ' ', kg.gio_ket_thuc) as match_end_time
                FROM dat_san ds
                JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
                JOIN san_bong sb ON kg.san_id = sb.san_id
                JOIN thanh_toan tt ON ds.dat_san_id = tt.dat_san_id
                WHERE ds.trang_thai = 'Đã xác nhận' 
                  AND tt.trang_thai = 'da_thanh_toan'
                  AND CONCAT(ds.ngay_dat, ' ', kg.gio_ket_thuc) <= NOW()
                  $where_user
                  AND NOT EXISTS (
                      SELECT 1 FROM lich_su_uy_tin lsu 
                      WHERE lsu.nguoi_dung_id = ds.nguoi_dung_id 
                        AND lsu.loai_hanh_dong = 'hoan_tat_dat_san'
                        AND lsu.ghi_chu LIKE CONCAT('%#', ds.dat_san_id, ' %')
                  )";

        $res = $conn->query($sql);
        if ($res && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $u_id = (int)$row['nguoi_dung_id'];
                $b_id = (int)$row['dat_san_id'];
                $p_short = preg_replace('/^Sân bóng đá\s*/iu', '', $row['ten_san']);
                $p_short = str_replace('Trường Đại Học Sư Phạm-Đà Nẵng', 'ĐH Sư Phạm', $p_short);

                $stmt_up = $conn->prepare("UPDATE nguoi_dung SET diem_uy_tin = LEAST(100, diem_uy_tin + 5) WHERE nguoi_dung_id = ?");
                $stmt_up->bind_param("i", $u_id);
                $stmt_up->execute();
                $stmt_up->close();

                $stmt_score = $conn->prepare("SELECT diem_uy_tin FROM nguoi_dung WHERE nguoi_dung_id = ?");
                $stmt_score->bind_param("i", $u_id);
                $stmt_score->execute();
                $cur_score = (int)($stmt_score->get_result()->fetch_row()[0] ?? 100);
                $stmt_score->close();

                $note = "Hoàn thành đơn #$b_id ($p_short)";
                $stmt_ins = $conn->prepare("INSERT INTO lich_su_uy_tin (nguoi_dung_id, so_diem, diem_sau_cung, loai_hanh_dong, ghi_chu, ngay_tao) VALUES (?, 5, ?, 'hoan_tat_dat_san', ?, ?)");
                $stmt_ins->bind_param("iiss", $u_id, $cur_score, $note, $row['match_end_time']);
                $stmt_ins->execute();
                $stmt_ins->close();
            }
        }
    }
}
