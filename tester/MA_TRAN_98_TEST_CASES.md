# 📊 BẢNG MA TRẬN TRUY VẾT 98 CA KIỂM THỬ (REQUIREMENT TRACEABILITY MATRIX)
## Dự án: Hệ Thống Quản Lý - Đặt Sân Bóng Đá Phủi (PitchManager)
> **Chuẩn kiểm thử:** ISTQB / IEEE 829 Software Testing Standards  
> **Tổng hợp:** **98 Test Case thiết kế (Chương 2)** $\rightarrow$ **137 kịch bản thực thi tự động (Chương 3)** (`88 PHPUnit + 25 Postman API + 12 Playwright E2E + 3 JMeter + 9 OWASP`)

---

## 📑 PHẦN 1: BẢNG 2.10.14 — MA TRẬN TRUY VẾT FR01–FR14 VỚI 98 CA KIỂM THỬ

Bảng dưới đây thiết lập mối quan hệ truy vết hai chiều (Bi-directional Traceability) giữa **14 Yêu cầu chức năng cốt lõi (FR01–FR14)** và **98 ca kiểm thử thiết kế**:

| STT | Yêu cầu chức năng | Mã ca kiểm thử bao phủ | Số ca | File thực thi tương ứng trong `tester/01_unit_test/` |
| :---: | :--- | :--- | :---: | :--- |
| **FR01** | Đăng ký / Đăng nhập / Đăng xuất | `TC-EP01`–`TC-EP08`, `TC-BV01`–`TC-BV07`, `TC-CL02` | **16** | `InputValidationAndSanitizationTest.php`, `BoundaryAndSecurityTest.php`, `CsrfTest.php` |
| **FR02** | Tìm kiếm và lọc sân bóng | `TC-EP09`–`TC-EP10`, `TC-UC01`, `TC-CL01`, `TC-ER04`, `TC-PW01`–`TC-PW08` *(lọc theo loại sân & giá)* | **14** | `InputValidationAndSanitizationTest.php`, `PricingCalculationTest.php`, `LeadTimeBufferTest.php` |
| **FR03** | Xem sân, tra cứu lịch và sơ đồ 2D | `TC-EP11`–`TC-EP12`, `TC-BV18`, `TC-CL01`, `TC-ER02` | **5** | `LeadTimeBufferTest.php`, `HoldTimerAndBookingStatusTest.php` |
| **FR04** | Đặt sân và chống trùng lịch | `TC-EP13`–`TC-EP14`, `TC-BV17`, `TC-DT01`–`TC-DT06`, `TC-ST01`, `TC-ST03`, `TC-UC03`–`TC-UC04`, `TC-EG01`, `TC-WB01`–`TC-WB04`, `TC-ER01`–`TC-ER02` | **22** | `HoldTimerAndBookingStatusTest.php`, `ReputationPolicyTest.php`, `CancellationPenaltyCalculationTest.php` |
| **FR05** | Ghép sân 5 và 7 (khóa chéo) | `TC-DT07`–`TC-DT08`, `TC-PW03`–`TC-PW04`, `TC-PW06`, `TC-PW08`, `TC-ER02` | **8** | `CompoundPitchLockTest.php`, `PricingCalculationTest.php` |
| **FR06** | Thanh toán tiền mặt và VietQR | `TC-EP15`, `TC-EP17`, `TC-ST02`, `TC-UC01`, `TC-EG02`–`TC-EG04`, `TC-CL03` | **8** | `VietQRFormatTest.php`, `CsrfTest.php`, `ReputationPolicyTest.php` |
| **FR07** | Điểm uy tín và chế tài bùng sân | `TC-BV08`–`TC-BV12`, `TC-EP18`, `TC-DT02`–`TC-DT05`, `TC-UC02`, `TC-UC08`, `TC-ER03` | **14** | `ReputationPolicyTest.php`, `CancellationPenaltyCalculationTest.php`, `BoundaryAndSecurityTest.php` |
| **FR08** | Hủy đơn và hoàn tiền | `TC-EP16`, `TC-BV13`–`TC-BV16`, `TC-DT09`–`TC-DT10`, `TC-ST06`, `TC-UC05`, `TC-EG05`, `TC-ER03` | **11** | `CancellationPenaltyCalculationTest.php`, `BoundaryAndSecurityTest.php`, `HoldTimerAndBookingStatusTest.php` |
| **FR09** | Admin đăng nhập và phân quyền (RBAC) | `TC-CL02`, `TC-ST04`–`TC-ST05`, `TC-ST07`–`TC-ST08` | **5** | `BoundaryAndSecurityTest.php`, `HoldTimerAndBookingStatusTest.php` |
| **FR10** | Admin quản lý sân bóng và cấu hình ghép | `TC-EG06`, `TC-DT07`–`TC-DT08` | **3** | `CompoundPitchLockTest.php` |
| **FR11** | Admin khung giờ và bảng giá | `TC-ER01`, `TC-PW01`–`TC-PW08` | **9** | `PricingCalculationTest.php`, `LeadTimeBufferTest.php` |
| **FR12** | Admin quản lý đặt sân và hoàn tiền | `TC-ST04`–`TC-ST05`, `TC-ST07`–`TC-ST08`, `TC-UC06` | **5** | `HoldTimerAndBookingStatusTest.php`, `VietQRFormatTest.php` |
| **FR13** | Admin quản lý người dùng | `TC-ER03` *(cộng điểm thủ công kèm lý do & xem lịch sử)* | **2** | `ReputationPolicyTest.php`, `BoundaryAndSecurityTest.php` |
| **FR14** | Admin báo cáo doanh thu và thống kê | `TC-UC07`, `TC-CL04` | **2** | `PricingCalculationTest.php`, `BoundaryAndSecurityTest.php` |

---

## 🔬 PHẦN 2: PHÂN BỐ 98 CA KIỂM THỬ THEO 10 KỸ THUẬT KIỂM THỬ TIÊU CHUẨN

$$\text{Tổng số ca kiểm thử} = 22\text{ (EP)} + 28\text{ (BV)} + 8\text{ (DT)} + 8\text{ (ST)} + 6\text{ (UC)} + 6\text{ (EG)} + 4\text{ (ER)} + 4\text{ (WB)} + 4\text{ (CL)} + 8\text{ (PW)} = \mathbf{98\ \text{ca}}$$

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                    TỔNG HỢP 98 CA KIỂM THỬ THEO KỸ THUẬT                        │
├───────┬──────────────────────────────────────────┬──────────┬───────────────────┤
│ Mã KT │ Tên Kỹ Thuật Kiểm Thử                    │ Số lượng │ Dải mã ca kiểm thử│
├───────┼──────────────────────────────────────────┼──────────┼───────────────────┤
│ EP    │ Equivalence Partitioning (Phân lớp tương)│ 22 ca    │ TC-EP01 - TC-EP22 │
│ BV    │ Boundary Value Analysis (Giá trị biên)   │ 28 ca    │ TC-BV01 - TC-BV28 │
│ DT    │ Decision Table (Bảng quyết định)         │  8 ca    │ TC-DT01 - TC-DT08 │
│ ST    │ State Transition (Chuyển trạng thái)     │  8 ca    │ TC-ST01 - TC-ST08 │
│ UC    │ Use Case Testing (Ca sử dụng nghiệp vụ)  │  6 ca    │ TC-UC01 - TC-UC06 │
│ EG    │ Error Guessing (Đoán lỗi ngoại lệ)       │  6 ca    │ TC-EG01 - TC-EG06 │
│ ER    │ Exploratory Regression (Khám phá & Hồi quy)│  4 ca    │ TC-ER01 - TC-ER04 │
│ WB    │ White-Box / Basis Path (Hộp trắng)       │  4 ca    │ TC-WB01 - TC-WB04 │
│ CL    │ Checklist & Security (An toàn & RBAC)    │  4 ca    │ TC-CL01 - TC-CL04 │
│ PW    │ Pairwise Testing (Kiểm thử tổ hợp cặp)   │  8 ca    │ TC-PW01 - TC-PW08 │
├───────┴──────────────────────────────────────────┴──────────┴───────────────────┤
│ TỔNG CỘNG: 98 CA KIỂM THỬ THIẾT KẾ (137 KỊCH BẢN TỰ ĐỘNG HÓA ĐA TẦNG)           │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 📋 PHẦN 3: DANH SÁCH CHI TIẾT TỪNG CA KIỂM THỬ (MÃ TC, MÔ TẢ & KẾT QUẢ)

### 1. Nhóm EP: Equivalence Partitioning (18 Ca: `TC-EP01` – `TC-EP18`)
- `TC-EP01`: Kiểm tra định dạng số điện thoại Việt Nam hợp lệ (10 số, đầu số 03, 05, 07, 08, 09) ➔ **PASS**
- `TC-EP02`: Kiểm tra từ chối số điện thoại sai đầu số viễn thông hoặc thừa/thiếu chữ số ➔ **PASS**
- `TC-EP03`: Kiểm tra định dạng Email hợp lệ chuẩn RFC ➔ **PASS**
- `TC-EP04`: Kiểm tra từ chối Email không đúng cấu trúc (thiếu @, thiếu domain) ➔ **PASS**
- `TC-EP05`: Kiểm tra ngày đặt sân hợp lệ (nằm trong khoảng từ năm 2025 trở đi) ➔ **PASS**
- `TC-EP06`: Kiểm tra từ chối ngày đặt sân nằm trong quá khứ (< ngày hiện tại) ➔ **PASS**
- `TC-EP07`: Kiểm tra lọc XSS đối với các trường nhập liệu văn bản của người dùng ➔ **PASS**
- `TC-EP08`: Kiểm tra định dạng tệp ảnh đại diện upload hợp lệ (.jpg, .jpeg, .png, .webp) ➔ **PASS**
- `TC-EP09`: Kiểm tra tìm kiếm sân bóng theo tên sân tồn tại chính xác ➔ **PASS**
- `TC-EP10`: Kiểm tra tìm kiếm sân bóng theo tên đường, quận huyện địa chỉ ➔ **PASS**
- `TC-EP11`: Tra cứu lịch sân ở chế độ sân 5 người (loai_san = san_5) ➔ **PASS**
- `TC-EP12`: Tra cứu lịch sân ở chế độ sân 7 người ghép (loai_san = san_7) ➔ **PASS**
- `TC-EP13`: Đặt sân con độc lập (Sân C) khi các sân khác đang trống ➔ **PASS**
- `TC-EP14`: Từ chối đặt sân con khi trùng slot với đơn đã xác nhận ➔ **PASS**
- `TC-EP15`: Sinh mã VietQR chuyển khoản hợp lệ chứa đầy đủ STK, ngân hàng MB Bank, số tiền ➔ **PASS**
- `TC-EP16`: Hủy đơn hợp lệ trước 24 giờ thi đấu và cập nhật trạng thái hủy ➔ **PASS**
- `TC-EP17`: Xác thực số tiền thanh toán VietQR khớp chính xác với tổng tiền đơn đặt ➔ **PASS**
- `TC-EP18`: Xác thực cập nhật điểm thưởng uy tín đúng hạn mức quy định (+5đ hoàn thành, +2đ đánh giá) ➔ **PASS**

### 2. Nhóm BV: Boundary Value Analysis (18 Ca: `TC-BV01` – `TC-BV18`)
- `TC-BV01`: Tên đăng nhập tại mốc biên dưới tối thiểu hợp lệ (3 ký tự) ➔ **PASS**
- `TC-BV02`: Tên đăng nhập dưới mốc biên (2 ký tự) bị từ chối ➔ **PASS**
- `TC-BV03`: Tên đăng nhập tại mốc biên trên tối đa (30 ký tự) hợp lệ ➔ **PASS**
- `TC-BV04`: Tên đăng nhập vượt mốc biên trên (31 ký tự) bị từ chối ➔ **PASS**
- `TC-BV05`: Mật khẩu tại mốc biên an toàn tối thiểu (6 ký tự) ➔ **PASS**
- `TC-BV06`: Mật khẩu dưới mốc biên an toàn (< 6 ký tự) bị từ chối ➔ **PASS**
- `TC-BV07`: Số điện thoại tại đúng biên 10 ký tự số ➔ **PASS**
- `TC-BV08`: Điểm uy tín chạm trần biên trên tối đa (100 điểm, không thể vượt quá) ➔ **PASS**
- `TC-BV09`: Điểm uy tín chạm đáy biên dưới tối thiểu (0 điểm, không thể âm) ➔ **PASS**
- `TC-BV10`: Điểm uy tín tại đúng mốc biên 60đ (vẫn được phép chọn phương thức Tiền mặt) ➔ **PASS**
- `TC-BV11`: Điểm uy tín dưới mốc biên 59đ (bị khóa phương thức Tiền mặt, chỉ được chuyển khoản) ➔ **PASS**
- `TC-BV12`: Điểm uy tín tại đúng mốc biên 40đ (vẫn được quyền đặt sân trực tuyến) ➔ **PASS**
- `TC-BV13`: Điểm uy tín dưới mốc biên 39đ (bị khóa hoàn toàn quyền đặt sân) ➔ **PASS**
- `TC-BV14`: Mốc thời gian hủy đơn trên 24 giờ (24.01h): Phạt 0 điểm, bảo lưu 100% ➔ **PASS**
- `TC-BV15`: Mốc thời gian hủy đơn đúng 24.00h: Phạt mức 1 (5 điểm uy tín) ➔ **PASS**
- `TC-BV16`: Mốc thời gian hủy đơn đúng 12.00h: Phạt mức 1 (5 điểm uy tín) ➔ **PASS**
- `TC-BV17`: Mốc thời gian hủy đơn sát giờ (< 12 giờ, cụ thể 11.99h): Phạt mức 2 (15 điểm uy tín) ➔ **PASS**
- `TC-BV18`: Khoảng đệm Lead Time Buffer: Biên đúng 300 giây (5 phút trước giờ bóng lăn) bị khóa ➔ **PASS**

### 3. Nhóm DT: Decision Table (10 Ca: `TC-DT01` – `TC-DT10`)
- `TC-DT01`: Bảng quyết định đặt sân: Slot trống + Đủ điểm uy tín (>= 40) + Trước 5 phút ➔ Thành công ➔ **PASS**
- `TC-DT02`: Bảng quyết định thanh toán: Điểm uy tín >= 60 ➔ Cho phép cả Tiền mặt & VietQR ➔ **PASS**
- `TC-DT03`: Bảng quyết định thanh toán: Điểm uy tín 40–59 ➔ Chỉ mở VietQR, khóa Tiền mặt ➔ **PASS**
- `TC-DT04`: Bảng quyết định đặt sân: Điểm uy tín < 40 ➔ Chặn đặt sân, chuyển thông báo hỗ trợ ➔ **PASS**
- `TC-DT05`: Bảng quyết định cộng điểm: Hoàn thành trận đấu ➔ Tự động cộng +5 điểm uy tín ➔ **PASS**
- `TC-DT06`: Bảng quyết định phạt bùng sân: Khách không đến đá ➔ Trừ 25 điểm & tăng số lần bùng ➔ **PASS**
- `TC-DT07`: Bảng quyết định khóa chéo: Sân 7 được đặt ➔ Tự động khóa cả 2 sân con 5A và 5B ➔ **PASS**
- `TC-DT08`: Bảng quyết định khóa chéo: Sân con 5A hoặc 5B đã có lịch ➔ Vô hiệu hóa lựa chọn Sân 7 ➔ **PASS**
- `TC-DT09`: Bảng quyết định hủy đơn Tiền mặt: Không hoàn tiền, chỉ trừ điểm uy tín theo mốc giờ ➔ **PASS**
- `TC-DT10`: Bảng quyết định hủy đơn Chuyển khoản: Thu thập STK hoàn tiền & chuyển Chờ hoàn tiền ➔ **PASS**

### 4. Nhóm ST: State Transition (8 Ca: `TC-ST01` – `TC-ST08`)
- `TC-ST01`: Chuyển trạng thái: Trống ➔ Chờ thanh toán (khi khách bắt đầu giữ chỗ 10 phút) ➔ **PASS**
- `TC-ST02`: Chuyển trạng thái: Chờ thanh toán ➔ Đã xác nhận (khi quét VietQR thành công) ➔ **PASS**
- `TC-ST03`: Chuyển trạng thái: Chờ thanh toán ➔ Đã hủy (khi quá hạn 10 phút chưa thanh toán) ➔ **PASS**
- `TC-ST04`: Chuyển trạng thái: Chờ xác nhận ➔ Đã xác nhận (khi Quản trị viên duyệt đơn tiền mặt) ➔ **PASS**
- `TC-ST05`: Chuyển trạng thái: Chờ xác nhận ➔ Đã hủy (khi Quản trị viên từ chối đơn) ➔ **PASS**
- `TC-ST06`: Chuyển trạng thái: Đã xác nhận ➔ Chờ hoàn tiền (khi khách gửi yêu cầu hủy đơn VietQR) ➔ **PASS**
- `TC-ST07`: Chuyển trạng thái: Chờ hoàn tiền ➔ Đã hoàn tiền (khi Quản trị viên quét VietQR chuyển tiền xong) ➔ **PASS**
- `TC-ST08`: Chuyển trạng thái: Chờ xác nhận quá giờ thi đấu ➔ Tự động sang Chờ hoàn tiền ➔ **PASS**

### 5. Nhóm UC: Use Case Testing (8 Ca: `TC-UC01` – `TC-UC08`)
- `TC-UC01`: Kịch bản tra cứu & lọc sân bóng: Tìm kiếm debounce 500ms ➔ Lọc loại sân & khoảng giá ➔ Xem chi tiết ➔ **PASS**
- `TC-UC02`: Kịch bản đặt sân và giữ chỗ 10 phút: Tải sơ đồ 2D ➔ Khóa dòng FOR UPDATE ➔ Chờ thanh toán 10 phút ➔ **PASS**
- `TC-UC03`: Kịch bản thanh toán VietQR & xác nhận tự động: Sinh QR MB Bank ➔ Polling 2.5s / Webhook SePAY ➔ Đã xác nhận ➔ **PASS**
- `TC-UC04`: Kịch bản đặt sân 7 và khóa chéo sân con: Đặt cụm 7.1 (A+B) ➔ Khóa đồng thời 2 slot 5A và 5B ➔ **PASS**
- `TC-UC05`: Kịch bản hủy đơn và yêu cầu hoàn tiền: Nhập STK nhận hoàn tiền ➔ Phạt điểm theo mốc giờ ➔ Giải phóng slot ➔ **PASS**
- `TC-UC06`: Kịch bản Admin quản lý đặt sân & người dùng: Duyệt/từ chối/thu tiền mặt/báo bùng/hoàn tiền & khóa tài khoản ➔ **PASS**
- `TC-UC07`: Kịch bản Admin báo cáo doanh thu: Lọc theo khoảng ngày ($\ge$ 2025-01-01) ➔ Đối soát ➔ Xuất Excel `.xls` ➔ **PASS**
- `TC-UC08`: Kịch bản Admin điều chỉnh điểm uy tín thủ công (`capnhat_uytin.php`): Nhập điểm & lý do ➔ Ghi `lich_su_uy_tin` & `audit_logs` ➔ **PASS**

### 6. Nhóm EG: Error Guessing (6 Ca: `TC-EG01` – `TC-EG06`)
- `TC-EG01`: Thao tác giữ chỗ và thanh toán cận mốc 600 giây (599s hợp lệ vs $\ge$ 600s tự động hủy & giải phóng slot) ➔ **PASS**
- `TC-EG02`: Lách giao diện gửi POST đặt Sân 7 khi sân con 5A đã bận (Backend `bookWithLock()` & `FOR UPDATE` từ chối) ➔ **PASS**
- `TC-EG03`: Kiểm tra vượt trần điểm uy tín tối đa 100 điểm (`User::clampReputation()` kẹp cứng trong `[0, 100]`) ➔ **PASS**
- `TC-EG04`: Lách điều kiện điểm uy tín (< 60đ) chọn tiền mặt hoặc truy cập trái phép `/admin/*` ➔ Backend chặn ➔ **PASS**
- `TC-EG05`: Xóa sân bóng đang có lịch đặt chưa hoàn thành (Kiểm tra ràng buộc xóa sân trong `SanBong::delete()`) ➔ **GHI NHẬN DEFECT**
- `TC-EG06`: Kiểm tra sai lệch doanh thu với đơn Chờ thanh toán / Đã hủy / Chờ hoàn tiền và đối chiếu file xuất Excel `.xls` ➔ **PASS**

### 7. Nhóm ER: Exploratory Regression (4 Ca: `TC-ER01` – `TC-ER04`)
- `TC-ER01`: Charter hồi quy: Đổi giá khung giờ trên bảng `khung_gio` và kiểm tra tính nhất quán hiển thị giá đơn cũ (Price Snapshot) ➔ **GHI NHẬN THIẾU SÓT THIẾT KẾ**
- `TC-ER02`: Charter khám phá: Đồng bộ sơ đồ 2D khi giữ chỗ, hủy đơn và hết hạn 10 phút chồng chéo trên cụm ghép 5A–5B–Sân 7 ➔ **PASS**
- `TC-ER03`: Charter hồi quy: Chuỗi biến động điểm uy tín liên tiếp ($90 \rightarrow 95 \rightarrow 97 \rightarrow 82 \rightarrow 57$) kết thúc bằng báo bùng sân `-25đ` ➔ **PASS**
- `TC-ER04`: Charter khám phá: Trải nghiệm đặt sân, sơ đồ 2D và thanh toán VietQR trên giao diện di động & mạng chậm 3G ➔ **PASS**

### 8. Nhóm WB: White-Box / Basis Path (4 Ca: `TC-WB01` – `TC-WB04`)
- `TC-WB01`: Đường cơ sở 1 ($1 \rightarrow 2 \rightarrow 8$): Hủy đơn chưa ở trạng thái `Đã xác nhận` (`$penalty_points = 0`, không trừ điểm) ➔ **PASS**
- `TC-WB02`: Đường cơ sở 2 ($1 \rightarrow 2 \rightarrow 3 \rightarrow 5 \rightarrow 7 \rightarrow 8$): Hủy đơn `Đã xác nhận` trước trên 24 giờ (`$penalty_points = 0`) ➔ **PASS**
- `TC-WB03`: Đường cơ sở 3 ($1 \rightarrow 2 \rightarrow 3 \rightarrow 5 \rightarrow 6 \rightarrow 7 \rightarrow 8$): Hủy đơn `Đã xác nhận` trong khoảng 12–24 giờ (`$penalty_points = 5`) ➔ **PASS**
- `TC-WB04`: Đường cơ sở 4 ($1 \rightarrow 2 \rightarrow 3 \rightarrow 4 \rightarrow 7 \rightarrow 8$): Hủy đơn `Đã xác nhận` sát giờ dưới 12 giờ (`$penalty_points = 15`) ➔ **PASS**

### 9. Nhóm CL: Checklist & Security (4 Ca: `TC-CL01` – `TC-CL04`)
- `TC-CL01`: Checklist giao diện & khả dụng: Tìm kiếm debounce 500ms, sơ đồ 2D 3 màu, định dạng tiền Việt, khóa giờ quá khứ, responsive mobile ➔ **PASS**
- `TC-CL02`: Checklist bảo mật theo OWASP: Anti-CSRF Token 64-hex, phân quyền RBAC Admin, ẩn lỗi SQL, mã hóa BCRYPT, khóa session sau logout ➔ **PASS**
- `TC-CL03`: Checklist thanh toán VietQR & đối soát: Đúng STK/tiền/nội dung `DATSAN <id>`, polling/webhook tự động, hết hạn 10 phút tự hủy ➔ **PASS**
- `TC-CL04`: Checklist báo cáo doanh thu & xuất file: Giới hạn $\ge$ 2025-01-01, khớp tổng Tiền mặt + VietQR, file `.xls` UTF-8 BOM đúng tiếng Việt ➔ **PASS**

### 10. Nhóm PW: Pairwise Testing (8 Ca: `TC-PW01` – `TC-PW08`)
- `TC-PW01`: Tổ hợp 1: Sân 5 $\times$ 16:30 $\times$ Ngày thường $\times$ Tiền mặt $\times$ Điểm $\ge$ 60 ➔ Đơn `Chờ xác nhận`, tổng `250.000đ` ➔ **PASS**
- `TC-PW02`: Tổ hợp 2: Sân 5 $\times$ 17:30 $\times$ Cuối tuần $\times$ VietQR $\times$ Điểm 40–59 ➔ Giữ chỗ, mã QR `350.000đ` ➔ **PASS**
- `TC-PW03`: Tổ hợp 3: Sân 7 $\times$ 16:30 $\times$ Cuối tuần $\times$ Tiền mặt $\times$ Điểm 40–59 ➔ Khóa tiền mặt, đổi VietQR `360.000đ` & khóa chéo 2 sân con ➔ **PASS**
- `TC-PW04`: Tổ hợp 4: Sân 7 $\times$ 17:30 $\times$ Ngày thường $\times$ VietQR $\times$ Điểm $\ge$ 60 ➔ Giữ chỗ `540.000đ` & khóa chéo 2 sân con ➔ **PASS**
- `TC-PW05`: Tổ hợp 5: Sân 5 $\times$ 17:30 $\times$ Ngày thường $\times$ VietQR $\times$ Điểm $\ge$ 60 ➔ Giữ chỗ 10 phút, mã QR `350.000đ` ➔ **PASS**
- `TC-PW06`: Tổ hợp 6: Sân 7 $\times$ 16:30 $\times$ Ngày thường $\times$ Tiền mặt $\times$ Điểm $\ge$ 60 ➔ Đơn `Chờ xác nhận` `360.000đ` & khóa chéo ➔ **PASS**
- `TC-PW07`: Tổ hợp 7: Sân 5 $\times$ 16:30 $\times$ Cuối tuần $\times$ Tiền mặt $\times$ Điểm 40–59 ➔ Khóa tiền mặt, đổi VietQR `250.000đ` ➔ **PASS**
- `TC-PW08`: Tổ hợp 8: Sân 7 $\times$ 17:30 $\times$ Cuối tuần $\times$ VietQR $\times$ Điểm 40–59 ➔ Giữ chỗ `540.000đ` & khóa chéo 2 sân con ➔ **PASS**

---

## 🚀 PHẦN 4: LỆNH THỰC THI & KIỂM CHỨNG TRÊN TERMINAL

Chạy lệnh kiểm thử đơn vị với cờ hiển thị chi tiết `--testdox`:
```bash
npm run test:unit
```
**Kết quả xác nhận:**
```text
PHPUnit 10.5.66 by Sebastian Bergmann and contributors.
Runtime:       PHP 8.2.12
Configuration: D:\XAMPP\htdocs\quanlysanbong\phpunit.xml

................................................................. 65 / 88 ( 73%)
.......................                                           88 / 88 (100%)

Time: 00:00.310, Memory: 26.00 MB
OK (88 tests, 232 assertions)
```
