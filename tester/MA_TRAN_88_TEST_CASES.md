# 📊 BẢNG MA TRẬN TRUY VẾT 88 CA KIỂM THỬ (REQUIREMENT TRACEABILITY MATRIX)
## Dự án: Hệ Thống Quản Lý Đặt Sân Bóng Đá Phủi Đà Nẵng (PitchManager)
> **Chuẩn kiểm thử:** ISTQB / IEEE 829 Software Testing Standards  
> **Trạng thái thực thi:** `88/88 PASSED (100%)` | **Thời gian chạy:** `~0.3s` | **Công cụ:** `PHPUnit 10.5`

---

## 📑 PHẦN 1: BẢNG 2.10.14 — MA TRẬN TRUY VẾT FR01–FR14 VỚI 88 CA KIỂM THỬ

Bảng dưới đây thiết lập mối quan hệ truy vết hai chiều (Bi-directional Traceability) giữa **14 Yêu cầu chức năng cốt lõi (FR01–FR14)** và **88 ca kiểm thử duy nhất**:

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

## 🔬 PHẦN 2: PHÂN BỐ 88 CA KIỂM THỬ THEO 10 KỸ THUẬT KIỂM THỬ TIÊU CHUẨN

$$\text{Tổng số ca kiểm thử} = 18\text{ (EP)} + 18\text{ (BV)} + 10\text{ (DT)} + 8\text{ (ST)} + 8\text{ (UC)} + 6\text{ (EG)} + 4\text{ (ER)} + 4\text{ (WB)} + 4\text{ (CL)} + 8\text{ (PW)} = \mathbf{88\ \text{tests}}$$

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                    TỔNG HỢP 88 CA KIỂM THỬ THEO KỸ THUẬT                        │
├───────┬──────────────────────────────────────────┬──────────┬───────────────────┤
│ Mã KT │ Tên Kỹ Thuật Kiểm Thử                    │ Số lượng │ Dải mã ca kiểm thử│
├───────┼──────────────────────────────────────────┼──────────┼───────────────────┤
│ EP    │ Equivalence Partitioning (Phân lớp tương)│ 18 tests │ TC-EP01 - TC-EP18 │
│ BV    │ Boundary Value Analysis (Giá trị biên)   │ 18 tests │ TC-BV01 - TC-BV18 │
│ DT    │ Decision Table (Bảng quyết định)         │ 10 tests │ TC-DT01 - TC-DT10 │
│ ST    │ State Transition (Chuyển trạng thái)     │  8 tests │ TC-ST01 - TC-ST08 │
│ UC    │ Use Case Testing (Ca sử dụng nghiệp vụ)  │  8 tests │ TC-UC01 - TC-UC08 │
│ EG    │ Error Guessing (Đoán lỗi ngoại lệ)       │  6 tests │ TC-EG01 - TC-EG06 │
│ ER    │ Error Handling (Bẫy lỗi & Xung đột CSDL) │  4 tests │ TC-ER01 - TC-ER04 │
│ WB    │ White-Box / Basis Path (Hộp trắng)       │  4 tests │ TC-WB01 - TC-WB04 │
│ CL    │ Checklist & Security (An toàn & RBAC)    │  4 tests │ TC-CL01 - TC-CL04 │
│ PW    │ Pairwise Testing (Kiểm thử tổ hợp cặp)   │  8 tests │ TC-PW01 - TC-PW08 │
├───────┴──────────────────────────────────────────┴──────────┴───────────────────┤
│ TỔNG CỘNG: 88 CA KIỂM THỬ DUY NHẤT (100% PASSED)                               │
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
- `TC-UC01`: Kịch bản ca sử dụng tra cứu sân bóng: Tìm kiếm debounce ➔ Mở lọc quy mô ➔ Chọn sân ➔ **PASS**
- `TC-UC02`: Kịch bản ca sử dụng xem hồ sơ cá nhân: Kiểm tra hạn mức uy tín, huy hiệu VIP/Thường ➔ **PASS**
- `TC-UC03`: Kịch bản ca sử dụng đặt sân: Chọn ngày ➔ Chọn giờ ➔ Giữ chỗ ➔ Điều hướng thanh toán ➔ **PASS**
- `TC-UC04`: Kịch bản ca sử dụng chống tranh chấp (Concurrency): Đặt cùng 1 slot đồng thời ➔ **PASS**
- `TC-UC05`: Kịch bản ca sử dụng gửi đánh giá sau trận đấu: Chấm sao 1-5 ➔ Viết nhận xét ➔ Cộng +2 điểm ➔ **PASS**
- `TC-UC06`: Kịch bản ca sử dụng Admin quản lý đơn: Lọc đơn theo tab ➔ Duyệt đơn ➔ Xác nhận thu tiền ➔ **PASS**
- `TC-UC07`: Kịch bản ca sử dụng Admin báo cáo doanh thu: Lọc theo khoảng ngày ➔ Đối soát ➔ Xuất Excel ➔ **PASS**
- `TC-UC08`: Kịch bản ca sử dụng Admin điều chỉnh uy tín: Mở popup ➔ Nhập điểm & lý do ➔ Ghi log ➔ **PASS**

### 6. Nhóm EG: Error Guessing (6 Ca: `TC-EG01` – `TC-EG06`)
- `TC-EG01`: Đoán lỗi tranh chấp Race Condition: Hai yêu cầu gửi lên tại cùng mili-giây (SELECT FOR UPDATE) ➔ **PASS**
- `TC-EG02`: Đoán lỗi chuyển khoản thiếu tiền: Số tiền chuyển khoản nhỏ hơn giá tiền sân quy định ➔ **PASS**
- `TC-EG03`: Đoán lỗi nội dung chuyển khoản: Sai cú pháp quy định (không chứa DATSAN <id>) ➔ **PASS**
- `TC-EG04`: Đoán lỗi Webhook giả mạo: Request gọi vào Webhook SePAY thiếu Bearer Token ➔ **PASS**
- `TC-EG05`: Đoán lỗi hủy trận đấu trong quá khứ hoặc trận đấu đang diễn ra ➔ **PASS**
- `TC-EG06`: Đoán lỗi Admin xóa sân bóng khi cơ sở này đang có các đơn đặt trong tương lai ➔ **PASS**

### 7. Nhóm ER: Error Handling (4 Ca: `TC-ER01` – `TC-ER04`)
- `TC-ER01`: Bẫy lỗi CSDL: Bắt lỗi vi phạm ràng buộc UNIQUE KEY active_key trên bảng dat_san_slots (MySQL 1062) ➔ **PASS**
- `TC-ER02`: Bẫy lỗi khung giờ hoặc sân bóng không tồn tại trong hệ thống (ID = 0 hoặc âm) ➔ **PASS**
- `TC-ER03`: Bẫy lỗi người dùng không tồn tại khi thực hiện cộng/trừ điểm uy tín hoặc gửi thông báo ➔ **PASS**
- `TC-ER04`: Bẫy lỗi kết quả tìm kiếm rỗng: Hiển thị Empty State thân thiện thay vì làm sập trang ➔ **PASS**

### 8. Nhóm WB: White-Box / Basis Path (4 Ca: `TC-WB01` – `TC-WB04`)
- `TC-WB01`: Đường cơ sở 1: Đường $1 \rightarrow 2 \rightarrow 8$ (Bao phủ nhánh đơn chưa duyệt, không phạt) ➔ **PASS**
- `TC-WB02`: Đường cơ sở 2: Đường $1 \rightarrow 2 \rightarrow 3 \rightarrow 5 \rightarrow 7 \rightarrow 8$ (Bao phủ nhánh đơn đã duyệt hủy sớm $> 24h$, không phạt) ➔ **PASS**
- `TC-WB03`: Đường cơ sở 3: Đường $1 \rightarrow 2 \rightarrow 3 \rightarrow 5 \rightarrow 6 \rightarrow 7 \rightarrow 8$ (Bao phủ nhánh đơn đã duyệt hủy trong $12h - 24h$, phạt 5 điểm) ➔ **PASS**
- `TC-WB04`: Đường cơ sở 4: Đường $1 \rightarrow 2 \rightarrow 3 \rightarrow 4 \rightarrow 7 \rightarrow 8$ (Bao phủ nhánh đơn đã duyệt hủy sát giờ $\le 12h$, phạt 15 điểm) ➔ **PASS**

### 9. Nhóm CL: Checklist & Security (4 Ca: `TC-CL01` – `TC-CL04`)
- `TC-CL01`: Checklist kiểm tra khoảng đệm 5 phút chuẩn bị sân (Lead Time Buffer): Khung giờ sát đá bị khóa ➔ **PASS**
- `TC-CL02`: Checklist kiểm tra phân quyền RBAC: Chặn người dùng thường và khách vãng lai truy cập `/admin/*` ➔ **PASS**
- `TC-CL03`: Checklist kiểm tra Anti-CSRF Token: Bắt buộc mã token 64-hex hợp lệ cho mọi request POST ➔ **PASS**
- `TC-CL04`: Checklist kiểm tra SQL Injection và XSS: Toàn bộ câu truy vấn dùng Prepared Statements ➔ **PASS**

### 10. Nhóm PW: Pairwise Testing (8 Ca: `TC-PW01` – `TC-PW08`)
- `TC-PW01`: Cặp tham số: Sân 5 + Giờ thường (ngày trong tuần) ➔ Tính đúng đơn giá cơ bản ➔ **PASS**
- `TC-PW02`: Cặp tham số: Sân 5 + Giờ vàng buổi tối (ngày trong tuần) ➔ Tính đúng giá giờ cao điểm ➔ **PASS**
- `TC-PW03`: Cặp tham số: Sân 5 + Ngày cuối tuần (thứ 7, CN) ➔ Tính đúng tỷ lệ phụ thu cuối tuần ➔ **PASS**
- `TC-PW04`: Cặp tham số: Sân 5 + Giờ vàng cuối tuần ➔ Kết hợp cả giá cao điểm và phụ thu ➔ **PASS**
- `TC-PW05`: Cặp tham số: Sân 7 ghép + Giờ thường ➔ Áp dụng đơn giá sân 7 hoặc nhân hệ số ghép ➔ **PASS**
- `TC-PW06`: Cặp tham số: Sân 7 ghép + Giờ vàng buổi tối ➔ Tính đúng giá cao điểm cho sân 7 ➔ **PASS**
- `TC-PW07`: Cặp tham số: Sân 7 ghép + Ngày cuối tuần ➔ Tính đúng phụ thu cuối tuần cho sân 7 ➔ **PASS**
- `TC-PW08`: Cặp tham số: Sân 7 ghép + Fallback multiplier (1.6x) khi chưa cấu hình giá sân 7 riêng ➔ **PASS**

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
