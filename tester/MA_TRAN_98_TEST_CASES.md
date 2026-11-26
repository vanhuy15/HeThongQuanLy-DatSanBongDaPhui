# 📊 BẢNG MA TRẬN TRUY VẾT 98 CA KIỂM THỬ (REQUIREMENT TRACEABILITY MATRIX)
## Dự án: Hệ Thống Quản Lý - Đặt Sân Bóng Đá Phủi (PitchManager)
> **Chuẩn kiểm thử:** ISTQB / IEEE 829 Software Testing Standards  
> **Tổng hợp:** **98 Test Case thiết kế (Chương 2)** $\rightarrow$ **137 kịch bản thực thi tự động (Chương 3)** (`88 PHPUnit + 25 Postman API + 12 Playwright E2E + 3 JMeter + 9 OWASP`)

---

## 📑 PHẦN 1: BẢNG 2.10.14 — MA TRẬN TRUY VẾT FR01–FR14 VỚI 98 CA KIỂM THỬ

Bảng dưới đây thiết lập mối quan hệ truy vết hai chiều (Bi-directional Traceability) giữa **14 Yêu cầu chức năng cốt lõi (FR01–FR14)** và **98 ca kiểm thử thiết kế**:

| STT | Yêu cầu chức năng | Mã ca kiểm thử bao phủ | File thực thi tương ứng trong `tester/01_unit_test/` & tự động hóa |
| :---: | :--- | :--- | :--- |
| **FR01** | Đăng ký / Đăng nhập / Đăng xuất | `TC-EP01`–`TC-EP09`, `TC-BV01`–`TC-BV03`, `TC-CL02` | `InputValidationAndSanitizationTest.php`, `BoundaryAndSecurityTest.php`, `CsrfTest.php`, Postman & Playwright |
| **FR02** | Tìm kiếm và lọc sân bóng | `TC-EP10`–`TC-EP11`, `TC-BV04`–`TC-BV06`, `TC-DT01`–`TC-DT02`, `TC-UC01`, `TC-CL01`, `TC-ER04` | Postman (`TC06`–`TC09`), Playwright (`TC05`), JMeter (`03_HighConcurrency_ReadSearch_Test.jmx`) |
| **FR03** | Xem sân, tra cứu lịch và sơ đồ 2D | `TC-EP12`–`TC-EP13`, `TC-BV07`–`TC-BV09`, `TC-ST01`–`TC-ST02`, `TC-CL01`, `TC-ER02` | `LeadTimeBufferTest.php`, `BoundaryAndSecurityTest.php`, `InputValidationAndSanitizationTest.php`, Playwright (`TC04`) |
| **FR04** | Đặt sân và chống trùng lịch | `TC-BV10`–`TC-BV12`, `TC-ST01`, `TC-ST03`–`TC-ST04`, `TC-UC02`, `TC-EG01`, `TC-ER01`–`TC-ER02`, `TC-PW01`–`TC-PW08` | `HoldTimerAndBookingStatusTest.php`, `PricingCalculationTest.php`, JMeter (`RaceCondition_Booking_Test.jmx`), Playwright (`TC11`) |
| **FR05** | Ghép sân 5 và 7 (khóa chéo) | `TC-DT03`–`TC-DT04`, `TC-UC04`, `TC-EG02`, `TC-ER02`, `TC-PW03`–`TC-PW04`, `TC-PW06`, `TC-PW08` | `CompoundPitchLockTest.php`, JMeter (`02_CompoundPitch_Conflict_Test.jmx`), Postman (`TC12`) |
| **FR06** | Thanh toán tiền mặt và VietQR | `TC-EP14`–`TC-EP15`, `TC-BV13`–`TC-BV19`, `TC-DT05`–`TC-DT06`, `TC-ST03`, `TC-UC03`, `TC-EG04`, `TC-CL03`, `TC-PW01`–`TC-PW08` | `VietQRFormatTest.php`, `ReputationPolicyTest.php`, Postman (`TC13`–`TC14`), Playwright (`TC12`) |
| **FR07** | Điểm uy tín và chế tài bùng sân | `TC-EP16`–`TC-EP17`, `TC-BV13`–`TC-BV22`, `TC-DT05`, `TC-ST05`, `TC-EG03`, `TC-ER03` | `ReputationPolicyTest.php`, `BoundaryAndSecurityTest.php`, `CancellationPenaltyCalculationTest.php` |
| **FR08** | Hủy đơn và hoàn tiền | `TC-EP18`–`TC-EP21`, `TC-BV23`–`TC-BV28`, `TC-ST06`, `TC-ST08`, `TC-UC05`, `TC-WB01`–`TC-WB04` | `CancellationPenaltyCalculationTest.php`, `BoundaryAndSecurityTest.php`, `HoldTimerAndBookingStatusTest.php` |
| **FR09** | Admin đăng nhập và phân quyền (RBAC) | `TC-EP22`, `TC-EG04`, `TC-CL02`, `TC-ST07`, `TC-UC06` | `BoundaryAndSecurityTest.php`, `CsrfTest.php`, `security_audit_runner.php`, Postman (`TC00_B`, `TC18_B`), Playwright (`TC08`) |
| **FR10** | Admin quản lý sân bóng và cấu hình ghép | `TC-EG05`, `TC-DT03`–`TC-DT04` | `CompoundPitchLockTest.php`, Postman (`TC21`) |
| **FR11** | Admin khung giờ và bảng giá | `TC-DT07`, `TC-PW01`–`TC-PW08`, `TC-ER01` | `PricingCalculationTest.php`, Postman (`TC22`) |
| **FR12** | Admin quản lý đặt sân và hoàn tiền | `TC-ST07`–`TC-ST08`, `TC-UC06` | `HoldTimerAndBookingStatusTest.php`, `VietQRFormatTest.php`, Postman (`TC20`), Playwright (`TC09`) |
| **FR13** | Admin quản lý người dùng | `TC-EP17`, `TC-UC06`, `TC-ER03` | `ReputationPolicyTest.php`, `BoundaryAndSecurityTest.php`, Postman (`TC23`) |
| **FR14** | Admin báo cáo doanh thu và thống kê | `TC-DT08`, `TC-EG06`, `TC-CL04` | Postman (`TC19`), Playwright (`TC08`) |

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
│ WB    │ White-Box / Basis Path (Hộp trắng)       │  4 ca    │ TC-WB01 - TC-WB04 │
│ PW    │ Pairwise Testing (Kiểm thử tổ hợp cặp)   │  8 ca    │ TC-PW01 - TC-PW08 │
│ CL    │ Checklist & Security (An toàn & RBAC)    │  4 ca    │ TC-CL01 - TC-CL04 │
│ ER    │ Exploratory Regression (Khám phá & Hồi quy)│  4 ca    │ TC-ER01 - TC-ER04 │
├───────┴──────────────────────────────────────────┴──────────┴───────────────────┤
│ TỔNG CỘNG: 98 CA KIỂM THỬ THIẾT KẾ (137 KỊCH BẢN TỰ ĐỘNG HÓA ĐA TẦNG)           │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 📋 PHẦN 3: DANH SÁCH CHI TIẾT TỪNG CA KIỂM THỬ (MÃ TC, MÔ TẢ & KẾT QUẢ)

### 1. Nhóm EP: Equivalence Partitioning (22 Ca: `TC-EP01` – `TC-EP22`)
- `TC-EP01`: Đăng ký hợp lệ (`nguyenvan_a`, mật khẩu khớp, SĐT `0935123456`, email hợp lệ) ➔ **PASS**
- `TC-EP02`: Tên đăng nhập ngắn hơn 3 ký tự (`ab`) bị từ chối ➔ **PASS**
- `TC-EP03`: Tên đăng nhập chứa ký tự đặc biệt (`user@# 123`) bị từ chối ➔ **PASS**
- `TC-EP04`: Mật khẩu ngắn hơn 6 ký tự (`1234`) bị từ chối ➔ **PASS**
- `TC-EP05`: Mật khẩu xác nhận không khớp bị từ chối ➔ **PASS**
- `TC-EP06`: Số điện thoại 9 chữ số (`091234567`) bị từ chối ➔ **PASS**
- `TC-EP07`: Số điện thoại sai đầu số VN (`0123456789`) bị từ chối ➔ **PASS**
- `TC-EP08`: Số điện thoại đã tồn tại (`0905123456`) bị chặn đăng ký trùng (đã sửa `BUG02`) ➔ **PASS**
- `TC-EP09`: Email sai định dạng (`username@.com`) bị từ chối ➔ **PASS**
- `TC-EP10`: Lọc sân 5 theo khoảng giá hợp lệ (`150.000đ` – `350.000đ`) ➔ **PASS**
- `TC-EP11`: Khoảng giá lọc không hợp lệ (`min > max`: `500.000đ` – `200.000đ`) ➔ **GHI NHẬN `BUG03` (Chưa validate `min > max`)**
- `TC-EP12`: Xem lịch ngày tương lai (`T+1`) qua AJAX và hiển thị sơ đồ 2D đủ 4 màu trạng thái ➔ **PASS**
- `TC-EP13`: Xem lịch ngày quá khứ (`T-3`) bị vô hiệu hóa / chuẩn hóa về ngày hôm nay ➔ **PASS**
- `TC-EP14`: Điểm uy tín `85` cho phép chọn cả Tiền mặt tại sân và VietQR ➔ **PASS**
- `TC-EP15`: Điểm uy tín `50` khóa tùy chọn Tiền mặt, bắt buộc chuyển khoản VietQR ➔ **PASS**
- `TC-EP16`: Điểm uy tín `15` (`< 40đ`) khóa hoàn toàn quyền đặt sân ➔ **PASS**
- `TC-EP17`: Điều chỉnh điểm uy tín ngoài khoảng `[0, 100]` (`-10` / `120`) tự động kẹp về `[0, 100]` ➔ **PASS**
- `TC-EP18`: Hủy đơn trước giờ bóng lăn `48` giờ (`> 24h`) phạt `0` điểm và giải phóng slot ➔ **PASS**
- `TC-EP19`: Hủy đơn trước giờ bóng lăn `18` giờ (`12h–24h`) trừ đúng `5` điểm ➔ **PASS**
- `TC-EP20`: Hủy đơn trước giờ bóng lăn `6` giờ (`< 12h`) trừ đúng `15` điểm ➔ **PASS**
- `TC-EP21`: Hủy đơn đã qua giờ thi đấu (`-2h`) bị ẩn nút và từ chối ở Backend ➔ **PASS**
- `TC-EP22`: Truy cập `/admin/index.php` bằng `admin15` thành công, `testuser` bị chặn chuyển hướng ➔ **PASS**

### 2. Nhóm BV: Boundary Value Analysis (28 Ca: `TC-BV01` – `TC-BV28`)
- `TC-BV01`: Biên SĐT `9` chữ số bị từ chối ➔ **PASS**
- `TC-BV02`: Biên SĐT `10` chữ số hợp lệ, đăng ký thành công ➔ **PASS**
- `TC-BV03`: Biên SĐT `11` chữ số bị từ chối ➔ **PASS**
- `TC-BV04`: Biên debounce tìm kiếm `499 ms`: hủy request `"s"`, chỉ gửi `"sa"` ➔ **PASS**
- `TC-BV05`: Biên debounce tìm kiếm `500 ms`: gửi đủ 2 request (`"s"` và `"sa"`) ➔ **PASS**
- `TC-BV06`: Biên debounce tìm kiếm `501 ms`: `"s"` gửi trước khi nhập `"sa"` ➔ **PASS**
- `TC-BV07`: Biên ngày xem lịch `D-1` (hôm qua) bị vô hiệu hóa / chuẩn hóa về hôm nay ➔ **PASS**
- `TC-BV08`: Biên ngày xem lịch `D` (hôm nay): tải thành công, khóa slot quá giờ hoặc còn `<= 5` phút ➔ **PASS**
- `TC-BV09`: Biên ngày xem lịch `D+1` (ngày mai): tải thành công qua AJAX ➔ **PASS**
- `TC-BV10`: Biên giữ chỗ `599` giây: đơn vẫn `Chờ thanh toán`, slot còn `active` ➔ **PASS**
- `TC-BV11`: Biên giữ chỗ `600` giây: tự động hủy (`Đã hủy`), giải phóng `released` ➔ **PASS**
- `TC-BV12`: Biên giữ chỗ `601` giây: tự động hủy, slot giải phóng về màu xanh ➔ **PASS**
- `TC-BV13`: Biên điểm uy tín `39` điểm: từ chối đặt sân (`< 40đ`) ➔ **PASS**
- `TC-BV14`: Biên điểm uy tín `40` điểm: đặt được sân, khóa Tiền mặt, chỉ VietQR ➔ **PASS**
- `TC-BV15`: Biên điểm uy tín `41` điểm: đặt được sân, khóa Tiền mặt, chỉ VietQR ➔ **PASS**
- `TC-BV16`: Biên điểm uy tín `58` điểm: đặt được sân, khóa Tiền mặt, chỉ VietQR ➔ **PASS**
- `TC-BV17`: Biên điểm uy tín `59` điểm: đặt được sân, khóa Tiền mặt, từ chối POST `tien_mat` ➔ **PASS**
- `TC-BV18`: Biên điểm uy tín `60` điểm: mở chọn cả Tiền mặt và VietQR ➔ **PASS**
- `TC-BV19`: Biên điểm uy tín `61` điểm: mở chọn cả Tiền mặt và VietQR ➔ **PASS**
- `TC-BV20`: Biên cộng điểm `94` điểm + `5` ➔ `99` điểm ➔ **PASS**
- `TC-BV21`: Biên cộng điểm `95` điểm + `5` ➔ `100` điểm (đúng trần) ➔ **PASS**
- `TC-BV22`: Biên cộng điểm `96` điểm + `5` ➔ `100` điểm (kẹp trần, không vượt `100`) ➔ **PASS**
- `TC-BV23`: Biên hủy đơn còn `11:59:59` (`< 12h`): trừ `15` điểm ➔ **PASS**
- `TC-BV24`: Biên hủy đơn còn đúng `12:00:00`: trừ `5` điểm (đã sửa `BUG01` từ `<= 12` thành `< 12.0`) ➔ **PASS**
- `TC-BV25`: Biên hủy đơn còn `12:00:01`: trừ `5` điểm ➔ **PASS**
- `TC-BV26`: Biên hủy đơn còn `23:59:59`: trừ `5` điểm ➔ **PASS**
- `TC-BV27`: Biên hủy đơn còn đúng `24:00:00`: vẫn trừ `5` điểm ➔ **PASS**
- `TC-BV28`: Biên hủy đơn còn `24:00:01` (`> 24h`): không trừ điểm (`0đ`) ➔ **PASS**

### 3. Nhóm DT: Decision Table (8 Ca: `TC-DT01` – `TC-DT08`)
- `TC-DT01`: Bảng quyết định lọc sân theo loại sân và khoảng giá (ghi nhận hành vi thiếu kiểm tra `min > max`) ➔ **PASS (kèm ghi chú `BUG03`)**
- `TC-DT02`: Bảng quyết định tìm kiếm từ khóa kết hợp bộ lọc loại sân và khoảng giá (Debounce `500 ms`) ➔ **PASS**
- `TC-DT03`: Bảng quyết định khóa chéo chiều đặt Sân 7 (khi 5A hoặc 5B đã đặt/đang giữ ➔ chặn Sân 7) ➔ **PASS**
- `TC-DT04`: Bảng quyết định khóa chéo chiều đặt Sân 5 con (khi Sân 7 đã đặt/đang giữ ➔ chặn 5A, 5B) ➔ **PASS**
- `TC-DT05`: Bảng quyết định phương thức thanh toán theo điểm uy tín (`< 40` chặn đặt; `40–59` chỉ VietQR; `>= 60` cả hai) ➔ **PASS**
- `TC-DT06`: Bảng quyết định xác nhận đơn VietQR (Webhook chuyển `Đã xác nhận` khi tiền `>=` giá sân) và Tiền mặt (Admin thu tiền) ➔ **PASS**
- `TC-DT07`: Bảng quyết định tính giá hiển thị và giá đơn (ưu tiên `gia_tien_san_7`, fallback nhân hệ số ghép) ➔ **PASS**
- `TC-DT08`: Bảng quyết định ghi nhận doanh thu (chỉ tính `Đã xác nhận` & `deleted_at IS NULL`, tách tiền mặt / VietQR) ➔ **PASS**

### 4. Nhóm ST: State Transition (8 Ca: `TC-ST01` – `TC-ST08`)
- `TC-ST01`: Chuyển trạng thái ô sân trên sơ đồ 2D (Trống/Xanh, Giữ chỗ/Vàng, Đã đặt/Đỏ, Quá giờ/Xám) ➔ **PASS**
- `TC-ST02`: Chuyển trạng thái khung giờ theo thời gian thực & Lead Time Buffer `5` phút (`<= time() + 300`) ➔ **PASS**
- `TC-ST03`: Vòng đời đơn đặt sân (`Chờ thanh toán` ➔ `Chờ xác nhận` ➔ `Đã xác nhận` / `Đã hủy` / `Chờ hoàn tiền`) ➔ **PASS**
- `TC-ST04`: Máy trạng thái giữ chỗ 10 phút và tự động hủy (`TIMESTAMPDIFF(SECOND, ..., NOW()) >= 600`) ➔ **PASS**
- `TC-ST05`: Chuyển trạng thái vùng điểm uy tín (`>= 60` ➔ `40–59` ➔ `< 40`) qua các sự kiện `+5`, `+2`, `-15`, `-25` ➔ **PASS**
- `TC-ST06`: Chuyển trạng thái hủy đơn và hoàn tiền (VietQR ➔ `Chờ hoàn tiền` ➔ `Đã hoàn tiền`; Tiền mặt ➔ `Đã hủy`) ➔ **PASS**
- `TC-ST07`: Chuyển trạng thái xử lý đơn phía Admin (`xacnhan`, `thutien_mat`, `huy`, `xacnhan_all`) + ghi `audit_logs` ➔ **PASS**
- `TC-ST08`: Chuyển trạng thái đơn `Chờ xác nhận` quá giờ thi đấu chưa duyệt (VietQR ➔ `Chờ hoàn tiền`, Tiền mặt ➔ `Đã hủy`) ➔ **PASS**

### 5. Nhóm UC: Use Case Testing (6 Ca: `TC-UC01` – `TC-UC06`)
- `TC-UC01`: Kịch bản tìm kiếm debounce `500 ms` kết hợp lọc loại sân & khoảng giá ➔ **PASS**
- `TC-UC02`: Kịch bản đặt sân, giữ chỗ 10 phút (`bookWithLock()` + `FOR UPDATE`) và tự hủy hết hạn ➔ **PASS**
- `TC-UC03`: Kịch bản thanh toán Tiền mặt / VietQR Napas 247 (MB Bank) và tự động xác nhận qua Webhook/Polling ➔ **PASS**
- `TC-UC04`: Kịch bản đặt Sân 7 và khóa chéo hai chiều hai sân con `5A`, `5B` ➔ **PASS**
- `TC-UC05`: Kịch bản hủy đơn và yêu cầu hoàn tiền (phạt mốc giờ, bắt buộc STK đối với VietQR, giải phóng slot) ➔ **PASS**
- `TC-UC06`: Kịch bản Admin quản lý đơn đặt sân (duyệt, thu tiền, báo bùng, hoàn tiền) và quản lý người dùng (nhật ký điểm, điều chỉnh thủ công, khóa/mở khóa) ➔ **PASS**

### 6. Nhóm EG: Error Guessing (6 Ca: `TC-EG01` – `TC-EG06`)
- `TC-EG01`: Thao tác giữ chỗ và thanh toán cận mốc `600` giây (`599s` hợp lệ vs `>= 600s` tự hủy & `released`) ➔ **PASS**
- `TC-EG02`: Lách giao diện gửi POST đặt Sân 7 khi sân con `5A` đã bận (Backend `bookWithLock()` & `FOR UPDATE` từ chối) ➔ **PASS**
- `TC-EG03`: Kiểm tra vượt trần điểm uy tín tối đa `100` điểm (`User::clampReputation()` kẹp cứng `[0, 100]`) ➔ **PASS**
- `TC-EG04`: Lách điều kiện điểm uy tín (`< 60đ`) POST `phuong_thuc = 'tien_mat'` tới `PaymentController::index()` hoặc truy cập trái phép `/admin/*` ➔ **PASS**
- `TC-EG05`: Xóa sân bóng đang có lịch đặt chưa hoàn thành (`SanBong::delete()` thực thi `DELETE CASCADE` trực tiếp) ➔ **GHI NHẬN DEFECT (`BUG04`)**
- `TC-EG06`: Kiểm tra sai lệch doanh thu với đơn `Chờ thanh toán` / `Đã hủy` / `Chờ hoàn tiền` và đối chiếu file xuất Excel `.xls` ➔ **PASS**

### 7. Nhóm WB: White-Box / Basis Path (4 Ca: `TC-WB01` – `TC-WB04`)
- `TC-WB01`: Đường cơ sở 1 ($1 \rightarrow 2 \rightarrow 8$): Hủy đơn chưa ở trạng thái `Đã xác nhận` (`$penalty_points = 0`) ➔ **PASS**
- `TC-WB02`: Đường cơ sở 2 ($1 \rightarrow 2 \rightarrow 3 \rightarrow 5 \rightarrow 7 \rightarrow 8$): Hủy đơn `Đã xác nhận` trước trên 24 giờ (`$penalty_points = 0`) ➔ **PASS**
- `TC-WB03`: Đường cơ sở 3 ($1 \rightarrow 2 \rightarrow 3 \rightarrow 5 \rightarrow 6 \rightarrow 7 \rightarrow 8$): Hủy đơn `Đã xác nhận` trong khoảng `12–24` giờ (`$penalty_points = 5`) ➔ **PASS**
- `TC-WB04`: Đường cơ sở 4 ($1 \rightarrow 2 \rightarrow 3 \rightarrow 4 \rightarrow 7 \rightarrow 8$): Hủy đơn `Đã xác nhận` sát giờ dưới `12` giờ (`$penalty_points = 15`) ➔ **PASS**

### 8. Nhóm PW: Pairwise Testing (8 Ca: `TC-PW01` – `TC-PW08`)
- `TC-PW01`: Tổ hợp 1: Sân 5 $\times$ 16:30 $\times$ Tiền mặt $\times$ Điểm $\ge$ 60 ➔ Đơn `Chờ xác nhận`, tổng `250.000đ` ➔ **PASS**
- `TC-PW02`: Tổ hợp 2: Sân 5 $\times$ 17:30 $\times$ VietQR $\times$ Điểm 40–59 ➔ Giữ chỗ, mã QR `350.000đ` ➔ **PASS**
- `TC-PW03`: Tổ hợp 3: Sân 7 $\times$ 16:30 $\times$ Tiền mặt $\times$ Điểm 40–59 ➔ Khóa tiền mặt, đổi VietQR `360.000đ` & khóa chéo 2 sân con ➔ **PASS**
- `TC-PW04`: Tổ hợp 4: Sân 7 $\times$ 17:30 $\times$ VietQR $\times$ Điểm $\ge$ 60 ➔ Giữ chỗ `540.000đ` & khóa chéo 2 sân con ➔ **PASS**
- `TC-PW05`: Tổ hợp 5: Sân 5 $\times$ 17:30 $\times$ Tiền mặt $\times$ Điểm $\ge$ 60 ➔ Đơn `Chờ xác nhận`, tổng `350.000đ` ➔ **PASS**
- `TC-PW06`: Tổ hợp 6: Sân 7 $\times$ 16:30 $\times$ VietQR $\times$ Điểm $\ge$ 60 ➔ Giữ chỗ `360.000đ` & khóa chéo 2 sân con ➔ **PASS**
- `TC-PW07`: Tổ hợp 7: Sân 5 $\times$ 16:30 $\times$ Tiền mặt $\times$ Điểm 40–59 ➔ Khóa tiền mặt, đổi VietQR `250.000đ` ➔ **PASS**
- `TC-PW08`: Tổ hợp 8: Sân 7 $\times$ 17:30 $\times$ VietQR $\times$ Điểm 40–59 ➔ Giữ chỗ `540.000đ` & khóa chéo 2 sân con ➔ **PASS**

### 9. Nhóm CL: Checklist & Security (4 Ca: `TC-CL01` – `TC-CL04`)
- `TC-CL01`: Checklist giao diện & khả dụng: Tìm kiếm debounce 500ms, sơ đồ 2D **4 màu trạng thái** (Xanh, Vàng, Đỏ, Xám), định dạng tiền Việt, khóa giờ quá khứ, responsive mobile ➔ **PASS**
- `TC-CL02`: Checklist bảo mật theo OWASP: Anti-CSRF Token 64-hex, phân quyền RBAC Admin, ẩn lỗi SQL, mã hóa BCRYPT, khóa session sau logout ➔ **PASS**
- `TC-CL03`: Checklist thanh toán VietQR & đối soát: Đúng STK/tiền/nội dung `DATSAN <id>`, polling/webhook tự động, hết hạn 10 phút tự hủy ➔ **PASS**
- `TC-CL04`: Checklist báo cáo doanh thu & xuất file: Giới hạn $\ge$ 2025-01-01, khớp số liệu giao dịch Tiền mặt + VietQR, file `.xls` UTF-8 BOM đúng tiếng Việt ➔ **PASS**

### 10. Nhóm ER: Exploratory Regression (4 Ca: `TC-ER01` – `TC-ER04`)
- `TC-ER01`: Charter hồi quy: Đổi giá khung giờ trên bảng `khung_gio` và kiểm tra tính nhất quán hiển thị giá đơn cũ (Price Snapshot) ➔ **GHI NHẬN THIẾU SÓT THIẾT KẾ (`BUG05`)**
- `TC-ER02`: Charter khám phá: Đồng bộ sơ đồ 2D khi giữ chỗ, hủy đơn và hết hạn 10 phút chồng chéo trên cụm ghép 5A–5B–Sân 7 ➔ **PASS**
- `TC-ER03`: Charter hồi quy: Chuỗi biến động điểm uy tín liên tiếp ($90 \rightarrow 95 \rightarrow 97 \rightarrow 82 \rightarrow 57$) kết thúc bằng Admin ghi nhận bùng sân `-25đ` (`khach_bung_san`) ➔ **PASS**
- `TC-ER04`: Charter khám phá: Trải nghiệm đặt sân, sơ đồ 2D và thanh toán VietQR trên giao diện di động & mạng chậm 3G ➔ **PASS**

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

Time: 00:00.314, Memory: 26.00 MB
OK (88 tests, 232 assertions)
```
