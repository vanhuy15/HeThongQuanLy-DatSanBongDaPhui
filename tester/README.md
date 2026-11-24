# 🧪 Bộ Tài Liệu & Kịch Bản Kiểm Thử Phần Mềm Toàn Diện (Full Software Testing Suite)

> Hệ thống kiểm thử tự động, tải đồng thời, API và an ninh mạng bao phủ **100% chức năng & thuật toán cốt lõi** cho dự án **Quản Lý Đặt Sân Bóng Đá Phủi Đà Nẵng** (Mô hình Pure MVC Thuần - PHP 8.2 + MySQL + Vanilla JS + Modern Modular CSS).

---

## 📋 DANH SÁCH 14 CHỨC NĂNG CHUẨN

1. Chức năng đăng kí/ đăng nhập/ đăng xuất
2. Tìm kiếm và lọc sân
3. Xem sân, tra cứu lịch và sơ đồ 2D
4. Đặt sân và chống trùng
5. Ghép sân 5 và 7 (khóa chéo)
6. Thanh toán tiền mặt và VietQR
7. Điểm uy tín và chế tài bùng sân
8. Hủy đơn và hoàn tiền
9. ADM Đăng nhập và phân quyền
10. ADM Quản lí sân bóng và cấu hình ghép
11. ADM Khung giờ và bảng giá
12. ADM Quản lí đặt sân
13. ADM Quản lí người dùng
14. ADM Báo cáo doanh thu và thống kê

---

## 📝 KỊCH BẢN KIỂM THỬ 14 CHỨC NĂNG

Kiểm thử viên đăng ký tài khoản mới bằng số điện thoại Việt Nam hợp lệ, kiểm tra mật khẩu được mã hóa BCRYPT trong cơ sở dữ liệu. Tiến hành đăng nhập với tên đăng nhập phân biệt chính xác chữ hoa/thường bằng so sánh nhị phân BINARY (admin15 khác ADMIN15), xác nhận hệ thống cấp phát phiên và mã Anti-CSRF mới (64-hex). Cuối cùng, thực hiện đăng xuất để hủy phiên làm việc hoàn toàn và chặn truy cập lại qua nút quay lại của trình duyệt.

Kiểm thử viên sử dụng thanh tìm kiếm thời gian thực Debounce để tra cứu sân bóng theo tên hoặc địa chỉ mà không cần bấm Enter. Đồng thời thao tác bộ lọc nâng cao theo quy mô (sân 5 hoặc sân 7) và thanh trượt khoảng giá thuê để xác nhận danh sách sân bóng được cập nhật động tức thì và lọc dữ liệu chính xác theo yêu cầu.

Khi truy cập trang chi tiết sân bóng, kiểm thử viên kiểm tra bộ chọn ngày để xác nhận các ngày trong quá khứ bị vô hiệu hóa (giới hạn từ năm 2025) và dữ liệu lịch tải động mượt mà qua AJAX. Sơ đồ 2D trực quan hiển thị đúng 3 trạng thái màu: xanh (sân trống), vàng (đang giữ chỗ) và đỏ (đã đặt). Đồng thời xác minh khoảng đệm 5 phút chuẩn bị sân (Lead Time Buffer): các khung giờ đã trôi qua hoặc cách giờ bắt đầu dưới 5 phút tự động bị làm mờ xám và vô hiệu hóa nút đặt sân.

Kiểm thử viên chọn khung giờ trống và đặt sân nhưng chưa thanh toán để xác nhận hệ thống chuyển ô sân sang màu vàng (giữ chỗ trong 10 phút). Nếu quá 10 phút chưa thanh toán, hệ thống tự động hủy đơn, lập tức giải phóng slot CSDL (released) về lại màu xanh cho người khác đặt và gửi thông báo chuông cho khách. Sử dụng công cụ giả lập gửi 2 yêu cầu đặt cùng 1 slot trong cùng 1 mili-giây để xác minh cơ chế khóa dòng SELECT ... FOR UPDATE chỉ cho phép duy nhất 1 đơn thành công, ngăn chặn tuyệt đối lỗi đặt trùng.

Kiểm thử viên đặt thành công sân 7 để xác nhận hệ thống tự động khóa chéo cả 2 sân con 5A và 5B ở cùng khung giờ đó trên sơ đồ 2D (san_con_khoa = 'A,B') nhằm ngăn người khác đặt lẻ. Ngược lại, nếu sân 5A hoặc sân 5B đã có lịch đặt hoặc đang được giữ chỗ, hệ thống tự động vô hiệu hóa lựa chọn sân 7 tương ứng và từ chối mọi yêu cầu đặt gửi lên.

Kiểm thử viên dùng các tài khoản khác nhau để xác nhận điều kiện: điểm uy tín từ 60 trở lên mới được chọn tiền mặt, dưới 60 điểm bắt buộc chuyển khoản. Khi chọn chuyển khoản, hệ thống sinh mã VietQR Napas 247 MB Bank tự điền STK, số tiền và nội dung DATSAN <id>, đồng thời ẩn nút xác nhận thủ công (chỉ hiện nút xác nhận ở tab tiền mặt). Khi quét thanh toán hoặc mô phỏng qua Webhook SePAY, hệ thống tự động nhận diện giao dịch, chuyển đơn sang Đã xác nhận, phát âm thanh thông báo và tự động chuyển hướng về trang lịch sử mà không cần admin duyệt tay.

Kiểm thử viên xác nhận tài khoản được cộng +5 điểm uy tín khi hoàn thành trận đấu (dừng ở trần tối đa 100 điểm) và cộng +2 điểm khi gửi đánh giá 1-5 sao. Ngược lại, khi bị đánh dấu bùng sân, hệ thống trừ thẳng 25 điểm uy tín và tăng số lần vi phạm; nếu điểm tụt dưới 60 điểm sẽ khóa thanh toán tiền mặt, và dưới 40 điểm sẽ khóa hoàn toàn quyền đặt sân.

Kiểm thử viên thao tác hủy đơn ở 3 mốc thời gian để kiểm tra bộ quy tắc phạt điểm: trước > 24h không trừ điểm, từ 12-24h trừ 5 điểm, và dưới 12h trừ 15 điểm uy tín. Đối với đơn chuyển khoản, hệ thống yêu cầu nhập đầy đủ STK nhận hoàn tiền trước khi hủy, lập tức giải phóng slot CSDL (released) để mở lại sân cho khách khác; đồng thời tự động chuyển các đơn VietQR chưa duyệt quá giờ thi đấu sang Chờ hoàn tiền để bảo lưu quyền lợi cho khách.

Kiểm thử viên sử dụng tài khoản vãng lai chưa đăng nhập và tài khoản khách hàng thông thường để truy cập trực tiếp vào các đường dẫn quản trị (/admin/*). Hệ thống chặn đứng các truy cập trái phép này với mã lỗi 403 hoặc chuyển hướng đăng nhập, chỉ cho phép tài khoản có vai trò quản trị viên (admin) đăng nhập thành công vào trang quản trị.

Quản trị viên thực hiện thêm mới, chỉnh sửa thông tin và xóa sân bóng, kiểm tra ràng buộc ngăn chặn xóa các sân đang có lịch đặt chưa hoàn thành. Đồng thời cấu hình quy tắc ghép sân 7 từ 2 sân con tương ứng, đảm bảo dữ liệu được lưu chính xác và áp dụng tức thì lên thuật toán khóa chéo ở giao diện đặt sân của khách.

Kiểm thử viên quản lý danh mục khung giờ hoạt động, thiết lập mức giá riêng cho các khung giờ vàng và cấu hình tỷ lệ phụ thu vào ngày cuối tuần, cũng như giá thuê riêng cho sân 7 ghép. Sau đó đối chiếu lại giá hiển thị trên giao diện đặt sân ở cả ngày thường và cuối tuần để đảm bảo hệ thống tính toán chính xác tổng tiền thuê sân.

Trên bảng quản trị đặt sân đã được tối ưu giao diện tinh gọn, quản trị viên thực hiện duyệt đơn hợp lệ, từ chối đơn vi phạm để giải phóng lịch, xác nhận đã thu tiền mặt khi khách đến sân, và bấm báo bùng sân để kích hoạt trừ điểm phạt. Với các đơn yêu cầu hoàn tiền, hệ thống hiển thị mã VietQR chứa sẵn STK của khách để quản trị viên quét chuyển hoàn tiền nhanh chóng một chạm.

Kiểm thử viên tra cứu thông tin khách hàng, xem chi tiết nhật ký biến động điểm uy tín để đảm bảo mọi lần cộng trừ điểm đều được ghi nhận minh bạch. Quản trị viên thực hiện điều chỉnh điểm thủ công kèm lý do xử lý, đồng thời kiểm tra tính năng khóa và mở khóa tài khoản để xác nhận tài khoản bị khóa lập tức mất quyền đăng nhập và đặt sân.

Kiểm thử viên lọc số liệu doanh thu theo ngày, tuần, tháng (từ mốc 2025-01-01 trở đi) để kiểm tra độ chính xác của tổng doanh thu, số lượt đặt và tỷ lệ lấp đầy. Hệ thống chỉ ghi nhận doanh thu từ các đơn đã thanh toán hoặc hoàn thành, loại trừ đơn hủy/giữ chỗ, phân tách rõ tiền mặt và VietQR, đồng thời hỗ trợ xuất báo cáo doanh thu ra file Excel (.csv chuẩn UTF-8 BOM kèm hộp thoại xác nhận).

---

## 🏗️ Tổng Quan 5 Công Cụ Kiểm Thử Trong Thư Mục `tester/`

```text
tester/
├── README.md                                  # Hướng dẫn tổng hợp & quy trình thực thi
├── MA_TRAN_88_TEST_CASES.md                   # 📊 Bảng ma trận truy vết 88 ca kiểm thử FR01-FR14
├── 01_unit_test/                              # 🟢 TOOL 1: PHPUnit (88 Tests - 232 Assertions - 100% Pass)
│   ├── BoundaryAndSecurityTest.php            # 10 Tests: Giá trị biên BVA, BCRYPT, đăng nhập phân biệt hoa/thường BINARY
│   ├── CancellationPenaltyCalculationTest.php # 8 Tests: Thuật toán phạt điểm uy tín, hoàn tiền, format thông báo phạt
│   ├── CompoundPitchLockTest.php              # 8 Tests: Thuật toán ghép sân 5 & 7, khóa chéo đa năng hai chiều (san_con_khoa)
│   ├── CsrfTest.php                           # 8 Tests: Sinh & xác thực Anti-CSRF Token 64-hex, ngoại lệ Webhook SePAY
│   ├── HoldTimerAndBookingStatusTest.php      # 10 Tests: Bộ đếm 10 phút, tự động giải phóng slot CSDL dat_san_slots, stale refund
│   ├── InputValidationAndSanitizationTest.php # 8 Tests: SĐT VN, Email, ngày >= 2025, lọc XSS, validate đuôi ảnh upload
│   ├── PricingCalculationTest.php             # 6 Tests: Quy tắc tính giá sân 5, sân 7 ghép, định dạng VND, fallback 1.6x
│   ├── ReputationPolicyTest.php               # 8 Tests: Điểm uy tín [0, 100], khóa tiền mặt (<60đ), khóa đặt (<40đ), VIP tier
│   ├── VietQRFormatTest.php                   # 8 Tests: Chuẩn hóa VietQR MB Bank, SePAY Webhook regex DATSAN <id>, Bearer Token
│   ├── NotificationSystemTest.php             # 7 Tests: Hệ thống chuông thông báo, time-ago thân thiện, sanitize nội dung
│   ├── LeadTimeBufferTest.php                 # 7 Tests: Khoảng đệm 5 phút chuẩn bị sân (slot_start_time <= time() + 300)
│   └── phpunit.phar                           # File thực thi độc lập PHPUnit 10.5
├── 02_api_test/                               # 🟠 TOOL 2: Postman & Newman (25 Requests - 34 Assertions - 100% Pass)
│   ├── QuanLySanBong_API.postman_collection.json # 6 Phân hệ: Auth, Discovery, Booking, Checkout, User, Admin
│   ├── QuanLySanBong.postman_environment.json    # Môi trường cấu hình Local host, user, admin credentials
│   └── README.md                              # Hướng dẫn chạy CLI qua Newman hoặc Postman Desktop
├── 03_e2e_ui_test/                            # 🔵 TOOL 3: Playwright E2E UI (12 Flow Scenarios - 100% Pass)
│   ├── booking.spec.js                        # Kịch bản E2E kiểm tra toàn bộ luồng Client & Admin, đặt sân & copy STK
│   └── playwright-report/                     # Báo cáo HTML trực quan, screenshots và traces
├── 04_load_concurrency_test/                  # 🟣 TOOL 4: Apache JMeter (3 Load & Concurrency Plans)
│   ├── RaceCondition_Booking_Test.jmx         # 50 users tranh đặt cùng 1 slot (SELECT ... FOR UPDATE)
│   ├── 02_CompoundPitch_Conflict_Test.jmx     # Tranh chấp đồng thời Sân 7 ghép vs Sân con lẻ A
│   ├── 03_HighConcurrency_ReadSearch_Test.jmx # Tải đọc cao độ 200 CCU tìm kiếm debounce & bảng giá
│   ├── run_jmeter_test.bat                    # Script chạy 1 chạm tự động tạo báo cáo HTML Dashboard
│   ├── test_users_data.csv                    # Dữ liệu tài khoản tham số hóa (Parameterized Data)
│   └── README.md                              # Hướng dẫn chạy JMeter Non-GUI mode & phân tích biểu đồ
└── 05_security_test/                          # 🔴 TOOL 5: OWASP ZAP & Automated Auditor (9 Chốt Chặn - 100% Pass)
    ├── security_audit_runner.php              # Script PHP kiểm thử tự động 9/9 lỗ hổng an ninh mạng (OWASP Top 10)
    ├── quanlysanbong.context                  # File cấu hình Context nhập sẵn cho phần mềm OWASP ZAP GUI
    └── README.md                              # Báo cáo phân tích DAST, CVE và hướng dẫn quét bảo mật
```

---

## ⚡ Bảng Lệnh Terminal Chạy Nhanh Toàn Bộ Test Suite (NPM Scripts)

Toàn bộ các công cụ đã được tích hợp thành lệnh npm một chạm trong file `package.json`:

| Lệnh Terminal | Công cụ kiểm thử | Số ca kiểm thử | Kết quả bảo đảm |
| :--- | :--- | :---: | :---: |
| `npm run test:unit` | **PHPUnit 10.5** (Unit Test / Hộp trắng) | 88 tests / 232 assertions | ✅ **100% Passed (0.3s)** |
| `npm run test:api` | **Newman CLI** (API & Tích hợp) | 25 requests / 34 assertions | ✅ **100% Passed (8.3s)** |
| `npm run test:e2e` | **Playwright** (E2E UI Headless) | 12 kịch bản luồng | ✅ **100% Passed (34.3s)** |
| `npm run test:e2e:headed` | **Playwright** (Giao diện trực quan Chromium) | 12 kịch bản luồng | ✅ **100% Passed** |
| `npm run test:report` | **Playwright Report** (Xem báo cáo HTML & Trace) | - | 📊 **Mở trình duyệt báo cáo** |
| `npm run test:security` | **Automated Auditor** (9 Chốt bảo mật OWASP) | 9 chốt an toàn | ✅ **9/9 Passed (100% An toàn)** |
| `npm run test:all` | **Chạy liên hoàn cả 4 bộ test tự động** | Toàn bộ hệ thống | ✅ **Bảo đảm 100% Pass** |

---

## 📋 Hướng Dẫn Chi Tiết Từng Công Cụ

### 1️⃣ Tool 1: Unit Test (Kiểm thử Hộp Trắng - PHPUnit)
Bao phủ toàn bộ các thuật toán cốt lõi, công thức giá, phân tích giá trị biên (BVA) và bảo mật mật khẩu:
```bash
npm run test:unit
```
*11 Nhóm Kiểm Thử Hộp Trắng Bao Phủ 14 Chức Năng Cốt Lõi:*
1. **Khóa chéo ghép sân 5 và 7:** Thuật toán chống xung đột hai chiều (`CompoundPitchLockTest.php`, 8 tests).
2. **Bộ đếm giữ chỗ 10 phút & Tự động hủy:** Giải phóng slot CSDL `dat_san_slots` sang `released` (`HoldTimerAndBookingStatusTest.php`, 10 tests).
3. **Bảng phạt điểm uy tín & Hoàn tiền:** Thuật toán phạt $>24$h, $12-24$h, $<12$h, bùng sân (`CancellationPenaltyCalculationTest.php`, 8 tests).
4. **Phân tích giá trị biên BVA & An toàn:** Mốc 24h, 12h, ca đá tối muộn, đăng nhập phân biệt hoa/thường BINARY (`BoundaryAndSecurityTest.php`, 10 tests).
5. **Khoảng đệm 5 phút chuẩn bị sân:** Chặn đặt khung giờ trong quá khứ hoặc dưới 5 phút (`LeadTimeBufferTest.php`, 7 tests).
6. **Hệ thống Chuông thông báo đồng bộ:** Unread badge, định dạng time-ago, sanitize HTML (`NotificationSystemTest.php`, 7 tests).
7. **Chính sách Điểm uy tín:** Kẹp biên $[0, 100]$, khóa tiền mặt ($<60$đ), khóa đặt sân ($<40$đ), VIP tier (`ReputationPolicyTest.php`, 8 tests).
8. **Thanh toán VietQR & Webhook SePAY:** Chuẩn hóa Napas 247, regex mã đơn `DATSAN <id>`, xác thực Token (`VietQRFormatTest.php`, 8 tests).
9. **Quy tắc tính giá linh hoạt:** Đơn giá sân 5, sân 7 ghép, hệ số 1.6x fallback, định dạng VNĐ (`PricingCalculationTest.php`, 6 tests).
10. **Lọc dữ liệu đầu vào & Anti-CSRF:** Token 64-hex, ngoại lệ Webhook SePAY, regex SĐT/Email, ngày $\ge$ 2025, đuôi ảnh upload (`CsrfTest.php` 8 tests, `InputValidationAndSanitizationTest.php` 8 tests).

---

### 2️⃣ Tool 2: API & Integration Test (Postman / Newman)
Kiểm thử 25 endpoints xuyên suốt 6 phân hệ Client & Admin có kèm trích xuất biến động (Chained State Flow) và kiểm thử âm tính (Negative Testing):
```bash
npm run test:api
```
*Các nội dung đã kiểm thử:*
1. Khởi tạo phiên, trích xuất CSRF Token từ thẻ meta.
2. Chặn truy cập trang đặt sân khi chưa đăng nhập (Redirect 302).
3. Đăng nhập tài khoản Khách hàng (`testuser`) và xác lập cookie `PHPSESSID`.
4. Đăng nhập thất bại khi sai mật khẩu và hiển thị cảnh báo.
5. Chặn request POST khi thiếu hoặc sai CSRF Token (Trả về mã HTTP 403 Forbidden).
6. Tải trang chủ danh sách sân bóng (`san-bong-container`).
7. Tìm kiếm thời gian thực Debounce theo từ khóa ("Nam Cao").
8. Lọc sân theo quy mô (Sân 5 / Sân 7) và khoảng giá.
9. Lấy ma trận lịch sân bóng qua AJAX (`getSchedule`).
10. Kiểm tra slot sân 7 ghép (`mode=san_7`).
11. Gửi đơn đặt sân tạo booking mới và trích xuất động `latest_booking_id`.
12. Xem trang thanh toán kèm bộ đếm giữ chỗ 10 phút với `latest_booking_id`.
13. Xác nhận chọn phương thức thanh toán VietQR.
14. Kiểm thử âm tính: Truyền `dat_san_id = -1` xử lý an toàn không gây crash 500.
15. Xem lịch sử đặt sân của người dùng.
16. Xem hồ sơ cá nhân và bảng Điểm Uy Tín.
17. Lấy danh sách thông báo qua AJAX (`getLatest`).
18. Gửi đánh giá sao sân bóng và cộng điểm uy tín (`Review/submit`).
19. Đăng nhập Quản trị viên (`admin15`).
20. KPI Dashboard và biểu đồ doanh thu Admin.
21. Quản lý đặt sân: Lọc đơn theo trạng thái (Chờ xác nhận, Đã duyệt).
22. Danh sách sân bóng và cấu hình ghép sân 5 và 7.
23. Quản lý khung giờ và bảng giá sân.
24. Quản lý khách hàng và can thiệp điểm uy tín (`admin/user/index.php`).

---

### 3️⃣ Tool 3: E2E UI Test (Playwright)
Mô phỏng 100% thao tác thực tế của người dùng trên trình duyệt Chromium:
```bash
npm run test:e2e
```
*12 Kịch bản kiểm thử luồng giao diện:*
- `TC01`: Trang chủ tải thành công, hiển thị header, menu điều hướng và danh sách sân bóng.
- `TC02`: Đăng nhập thất bại khi nhập sai mật khẩu (Báo lỗi đỏ).
- `TC03`: Đăng nhập thành công với tài khoản khách hàng `testuser`.
- `TC04`: Xem ma trận lịch đặt sân bóng, chọn ngày và kiểm tra ô giờ.
- `TC05`: Tìm kiếm thời gian thực (Debounce Search) & mở drawer bộ lọc giá/loại sân.
- `TC06`: Kiểm tra trang Hồ sơ cá nhân (Profile) & Thang đo Điểm Uy Tín.
- `TC07`: Kiểm tra trang Lịch sử đặt sân & Phân loại trạng thái (Tất cả / Chờ duyệt / Đã hủy).
- `TC08`: Đăng nhập Quản trị viên & kiểm tra KPI Dashboard, sidebar admin.
- `TC09`: Quản lý đặt sân phía Admin & duyệt đơn trực quan.
- `TC10`: Đăng xuất an toàn và làm mới phiên làm việc (Session Invalidation).
- `TC11`: **Luồng Đặt Sân Trực Tuyến & Chuyển Sang Trang Thanh Toán Giữ Chỗ (Click sơ đồ 2D -> Chọn sân con A -> Đặt ngay -> Mở trang thanh toán giữ chỗ 10 phút)**.
- `TC12`: **Kiểm tra tương tác chọn phương thức thanh toán QR chuyển khoản & Phản hồi nút sao chép STK VietQR (Icon đổi thành checkmark)**.

---

### 4️⃣ Tool 4: Concurrency & Load Test (Apache JMeter)
Kiểm tra khả năng chịu tải và triệt tiêu Race Condition:
- Kịch bản 1 (`RaceCondition_Booking_Test.jmx`): 50 users tranh đặt cùng 1 slot sân con trong cùng 1 tích tắc. Kết quả: Duy nhất 1 đơn thành công, 49 đơn nhận thông báo xung đột.
- Kịch bản 2 (`02_CompoundPitch_Conflict_Test.jmx`): Tranh chấp giữa Sân 7 ghép và Sân 5 thành phần lẻ A. Kết quả: Khi sân 7 đặt thành công, toàn bộ sân 5 thành phần tự động bị khóa chéo.
- Kịch bản 3 (`03_HighConcurrency_ReadSearch_Test.jmx`): 200 CCU tải đọc tìm kiếm debounce và xem lịch AJAX liên tục. Kết quả: 0% lỗi, phản hồi trung bình $< 200$ms.

*Cách chạy:* Chạy file `run_jmeter_test.bat` bằng cách nhấp đúp chuột, hoặc chạy lệnh:
```bash
jmeter -n -t tester/04_load_concurrency_test/RaceCondition_Booking_Test.jmx -l tester/04_load_concurrency_test/results_race.jtl
```

---

### 5️⃣ Tool 5: Security Testing (OWASP ZAP & Automated Auditor)
Kiểm tra an toàn thông tin chuyên sâu theo danh mục OWASP Top 10:
```bash
npm run test:security
```
*9 Chốt chặn an ninh mạng được kiểm thử tự động:*
1. **[SEC-01] SQL Injection**: Prepared Statements tham số hóa 100%, triệt tiêu SQLi trên mọi trường form và query string.
2. **[SEC-02] Cross-Site Scripting (XSS)**: Hàm `htmlspecialchars()` và bộ lọc Router loại bỏ payload script nguy hiểm.
3. **[SEC-03] Anti-CSRF Token**: Toàn bộ form POST bắt buộc token 64-hex hợp lệ; từ chối với HTTP 403 Forbidden nếu thiếu.
4. **[SEC-04] Broken Authentication & RBAC**: Chặn khách vãng lai và tài khoản người dùng thường truy cập trái phép phân hệ Quản trị (`admin/`).
5. **[SEC-05] Security Response Headers**: Thiết lập đầy đủ `X-Frame-Options: SAMEORIGIN` (Chống Clickjacking), `X-Content-Type-Options: nosniff`, `Referrer-Policy` và `Content-Security-Policy`.
6. **[SEC-06] Session Security**: Cờ `HttpOnly: true` và `SameSite: Lax` bảo vệ cookie phiên `PHPSESSID` khỏi tấn công đánh cắp phiên qua JavaScript.
7. **[SEC-07] Information Disclosure**: Ẩn header `X-Powered-By` che giấu phiên bản PHP của máy chủ.
8. **[SEC-08] Insecure Direct Object Reference (IDOR)**: Kiểm tra quyền sở hữu `WHERE dat_san_id = ? AND nguoi_dung_id = ?`, chặn truy cập trái phép hóa đơn/đơn đặt sân của người dùng khác.
9. **[SEC-09] Malicious File Upload Prevention**: Chặn tải lên file webshell PHP giả danh ảnh đại diện (`webshell.php.png`), kiểm tra nghiêm ngặt phần mở rộng, MIME type và hàm `getimagesize`.

*File cấu hình OWASP ZAP GUI:* Nhập file `tester/05_security_test/quanlysanbong.context` vào OWASP ZAP để tự động nhận diện URL đăng nhập và loại trừ các endpoint đăng xuất khi quét tự động.
