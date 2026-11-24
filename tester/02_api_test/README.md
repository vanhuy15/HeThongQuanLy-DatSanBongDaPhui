# 📮 Kiểm Thử API Tự Động (API Testing - Postman & Newman)

Bộ kiểm thử API bao phủ **100% các endpoint** của cả 2 phân hệ **Khách Hàng (Client)** và **Quản Trị Viên (Admin)** theo mô hình MVC.

---

## 📂 1. Cấu Trúc Các Nhóm API Kiểm Thử (25 Test Cases)

1. **`01 - Authentication & Security`**:
   - `TC00`: Khởi tạo phiên & Trích xuất CSRF Token.
   - `TC00_B`: Chặn truy cập đặt sân khi chưa đăng nhập.
   - `TC01`: Đăng nhập Khách hàng thành công (Cấp phát Cookie `PHPSESSID`).
   - `TC02`: Chặn đăng nhập sai mật khẩu (Báo lỗi HTTP / HTML Alert).
   - `TC05`: Chặn yêu cầu POST khi thiếu CSRF Token hợp lệ.

2. **`02 - Discovery & Pitch Search`**:
   - `TC06`: Tải danh sách sân bóng trang chủ (`pitch-grid`).
   - `TC07`: Tìm kiếm thời gian thực theo từ khóa (`keyword=Nam+Cao`).
   - `TC08`: Lọc sân bóng theo quy mô (`loai_san=san_5`).
   - `TC09`: Lọc sân bóng theo khoảng giá (`gia_min=200000&gia_max=300000`).

3. **`03 - Booking & Slot Availability`**:
   - `TC10`: Lấy dữ liệu ma trận khung giờ & bảng giá AJAX (`DatSan/getSchedule`).
   - `TC12`: Kiểm tra chi tiết trạng thái Slot sân con (`DatSan/getSchedule`).
   - `TC12_B`: Đặt sân trực tuyến tạo đơn giữ chỗ (`DatSan/bookSubmit`).

4. **`04 - Checkout & Payment`**:
   - `TC13`: Xem trang thanh toán kèm bộ đếm giữ chỗ 10 phút.
   - `TC14`: Xác nhận phương thức chuyển khoản VietQR tự động.
   - `TC14_B`: Kiểm tra an toàn khi truyền dat_san_id âm hoặc không hợp lệ (-1).

5. **`05 - Customer Portal`**:
   - `TC15`: Truy xuất lịch sử đặt sân phân trang & bộ lọc ngày.
   - `TC16`: Xem hồ sơ cá nhân, hạng uy tín & lịch sử biến động điểm.
   - `TC17`: Lấy danh sách thông báo chưa đọc qua AJAX.
   - `TC18`: Đánh giá 5 sao sân bóng (+2 điểm uy tín).

6. **`06 - Admin Management`**:
   - `TC18_B`: Đăng nhập tài khoản Quản trị viên (`admin15`).
   - `TC19`: Xem KPI doanh thu & biểu đồ thống kê Dashboard.
   - `TC20`: Quản lý đặt sân: Lọc đơn theo trạng thái (Chờ xác nhận, Đã xác nhận...).
   - `TC21`: Danh sách sân bóng & cấu hình ghép sân 5 & 7.
   - `TC22`: Cấu hình khung giờ & bảng giá linh hoạt.
   - `TC23`: Quản lý khách hàng & can thiệp điểm uy tín.

---

## 🚀 2. Hướng Dẫn Chạy Kiểm Thử

### Cách 1: Chạy tự động bằng Newman CLI
Nếu máy có cài đặt `newman` (Node.js), chạy trực tiếp dòng lệnh:
```bash
newman run tester/02_api_test/QuanLySanBong_API.postman_collection.json -e tester/02_api_test/QuanLySanBong.postman_environment.json
```

### Cách 2: Sử dụng Postman Desktop GUI
1. Mở ứng dụng **Postman**.
2. Chọn **Import** $\rightarrow$ Kéo thả 2 file:
   - `QuanLySanBong_API.postman_collection.json`
   - `QuanLySanBong.postman_environment.json`
3. Chọn môi trường `QuanLySanBong_Local_Env`.
4. Bấm vào thư mục gốc `QuanLySanBong_Full_API_TestSuite` $\rightarrow$ Nhấn **Run collection**.

