# 🏟️ Hệ Thống Quản Lý Sân Bóng Đá Phủi Đà Nẵng (Football Pitch Booking & Management System)

> **Giải pháp chuyển đổi số toàn diện cho chuỗi sân bóng đá mini & phủi tại Thành phố Đà Nẵng.**  
> Xây dựng theo kiến trúc **Pure MVC (Mô hình MVC Thuần không phụ thuộc framework)**, tối ưu hiệu năng cao, bảo mật đa tầng và tích hợp sẵn **5 bộ công cụ kiểm thử phần mềm chuyên nghiệp** (PHPUnit, Postman, Playwright, Apache JMeter, OWASP ZAP).

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-10.5-377755?style=for-the-badge&logo=php&logoColor=white)](https://phpunit.de/)
[![Playwright](https://img.shields.io/badge/Playwright-E2E-2EAD33?style=for-the-badge&logo=playwright&logoColor=white)](https://playwright.dev/)
[![Postman](https://img.shields.io/badge/Postman-API_Tests-FF6C37?style=for-the-badge&logo=postman&logoColor=white)](https://www.postman.com/)
[![Apache JMeter](https://img.shields.io/badge/Apache_JMeter-Concurrency_Test-D22128?style=for-the-badge&logo=apache&logoColor=white)](https://jmeter.apache.org/)
[![OWASP ZAP](https://img.shields.io/badge/OWASP-Security_Audit-005E8A?style=for-the-badge&logo=owasp&logoColor=white)](https://www.zaproxy.org/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

---

## 🎯 BỐI CẢNH THỰC TẾ & BÀI TOÁN GIẢI QUYẾT (PROBLEM STATEMENT & OBJECTIVES)

### 1. Bối cảnh thực tế tại TP. Đà Nẵng
Thành phố Đà Nẵng là một trong những trung tâm thể thao phong trào sôi động nhất miền Trung, nơi bóng đá phủi (sân cỏ nhân tạo 5 người và 7 người) trở thành hoạt động rèn luyện quen thuộc hàng ngày của học sinh, sinh viên, cán bộ công nhân viên và các câu lạc bộ bóng đá phong trào.

Tuy nhiên, quy trình vận hành và đặt sân tại phần lớn các cụm sân bóng hiện nay (khu vực Liên Chiểu, Cẩm Lệ, Hải Châu, Ngũ Hành Sơn...) vẫn dựa trên phương thức gọi điện thoại, nhắn tin Zalo hoặc ghi chép sổ sách thủ công, dẫn đến nhiều bất cập nhức nhối:
* **Khách hàng gặp nhiều trở ngại:** Không thể biết trước khung giờ nào còn trống nếu không gọi hỏi trực tiếp chủ sân; không nắm rõ biểu giá các khung giờ vàng hoặc ngày cuối tuần; không có sơ đồ trực quan để biết vị trí sân con mình sẽ thi đấu; chuyển khoản ngân hàng thủ công dễ nhầm số tiền hoặc sai lệch nội dung đối soát.
* **Chủ sân đối mặt với rủi ro vận hành & thất thoát doanh thu:**
  * **Vấn nạn "Bùng sân" (No-show):** Khách đặt miệng qua điện thoại nhưng sát giờ không đến đá, khiến sân bị bỏ trống trong khung giờ vàng mà không kịp cho đội khác thuê, gây thiệt hại tài chính nặng nề.
  * **Trùng lịch (Double-Booking):** Khi nhiều khách cùng gọi điện hoặc nhắn tin đặt một khung giờ, việc ghi chép sổ tay dễ dẫn đến nhầm lẫn, đặt trùng một sân cho 2 đội bóng, gây tranh cãi và ảnh hưởng uy tín sân.
  * **Bài toán ghép sân 5 & sân 7 phức tạp:** Đa số các sân 7 người tại Đà Nẵng được ghép từ 2 sân 5 con cạnh nhau (ngăn bởi lưới kéo di động). Khi quản lý thủ công, nhân viên rất dễ nhầm lẫn: cho thuê sân 7 nhưng vẫn lỡ nhận cọc sân 5 con thành phần, dẫn đến xung đột không thể giải quyết khi các đội cùng ra sân.
  * **Quản trị tài chính rời rạc:** Việc thu tiền mặt hoặc nhận chuyển khoản rải rác khiến việc tổng kết, đối soát doanh thu cuối ngày/cuối tháng tốn nhiều thời gian và tiềm ẩn nguy cơ sai lệch, thất thoát.

### 2. Mục tiêu & Giải pháp của Dự Án
Dự án **Hệ Thống Quản Lý Sân Bóng Đá Phủi Đà Nẵng** ra đời nhằm số hóa toàn diện quy trình kết nối giữa người chơi bóng và ban quản lý cụm sân:
- 🌐 **Minh bạch hóa thông tin 24/7:** Cho phép người chơi tra cứu trực tuyến tình trạng sân trống theo ngày, theo từng khung giờ và xem bảng giá minh bạch trong tích tắc.
- 🗺️ **Sơ đồ 2D trực quan:** Người chơi tự do lựa chọn chính xác sân con (Sân con A, Sân con B, hoặc Cụm sân 7 ghép) trên giao diện trực quan trước khi xác nhận.
- ⚡ **Khóa giao dịch chống trùng sân tự động:** Thuật toán hàng đợi kết hợp MySQL Transaction `SELECT ... FOR UPDATE` đảm bảo không bao giờ xảy ra tình trạng 2 khách đặt trùng cùng 1 vị trí sân.
- 🔄 **Tự động hóa khóa chéo sân ghép đa năng:** Khi có đơn đặt Sân 7, hệ thống lập tức khóa tự động 2 sân 5 tương ứng và ngược lại, giải phóng hoàn toàn sức lao động cho nhân viên quản lý.
- ⏱️ **Cơ chế giữ chỗ 10 phút & Tự động nhả slot:** Đảm bảo tính công bằng, giữ slot cho khách hàng thao tác thanh toán trong 10 phút, nếu quá hạn hệ thống sẽ tự động giải phóng vị trí cho người khác.
- 🛡️ **Hệ thống Điểm Uy Tín & Chế tài bùng sân:** Tự động tính điểm tín nhiệm khách hàng dựa trên lịch sử đặt/hủy. Khách có hành vi bùng sân sẽ bị trừ điểm nặng (-25đ), điểm dưới 60đ bị cấm trả tiền mặt (buộc chuyển khoản trước), và dưới 40đ bị khóa quyền đặt sân, bảo vệ 100% doanh thu cho chủ sân.
- 💳 **Thanh toán VietQR chuẩn Napas 247:** Tự động tạo mã QR chính xác từng đồng, tự điền nội dung chuyển khoản định danh đơn đặt, loại bỏ hoàn toàn sai sót khi giao dịch.
- 📊 **Số hóa nghiệp vụ Quản trị (Admin Portal):** Cung cấp Dashboard theo dõi KPI, biểu đồ doanh thu trực quan, quản lý khung giờ vàng linh hoạt và hỗ trợ xuất báo cáo Excel chỉ với một cú nhấp chuột.

---

## 🌟 ĐIỂM SÁNG KỸ THUẬT & THUẬT TOÁN CỐT LÕI

1. ⚡ **Khóa dòng chống Đặt trùng sân (Race Condition Prevention):**  
   Ứng dụng cơ chế **MySQL Transaction** kết hợp khóa mức hàng **`SELECT ... FOR UPDATE`**. Khi có hàng chục khách hàng cùng chọn 1 sân trong cùng 1 khung giờ, hệ thống đảm bảo chính xác tuyệt đối **chỉ duy nhất 1 đơn đặt thành công**, triệt tiêu 100% rủi ro Double-Booking.
2. 🔄 **Thuật toán Ghép Sân Đa Năng (Flexible Compound Pitch Merging):**  
   Hỗ trợ cấu hình sân 7 người ghép từ các sân 5 người thành phần. Khi sân 7 được đặt, toàn bộ các sân 5 tương ứng tự động chuyển sang trạng thái bị khóa (`san_con_khoa = 'A,B'`) và ngược lại.
3. ⏱️ **Bộ đếm giữ chỗ 10 phút & Tự động nhả slot (10-Minute Hold & Auto-Release):**  
   Tự động khóa tạm thời slot sân trong 10 phút khi khách hàng chuyển sang bước thanh toán. Nếu quá thời gian mà chưa hoàn tất, tiến trình nền tự động giải phóng vị trí cho khách hàng khác.
4. 🛡️ **Hệ thống Điểm Uy Tín Khách Hàng (Customer Credit Score System):**  
   Tự động tính điểm uy tín dựa trên hành vi: cộng điểm khi hoàn thành trận/đánh giá (+2đ), trừ điểm khi hủy sát giờ (-5đ đến -15đ), chế tài nặng khi bùng sân (-25đ). Hệ thống tự động khóa phương thức Tiền mặt nếu điểm $< 60$đ và khóa quyền đặt sân nếu điểm $< 40$đ.
5. 💳 **Thanh toán Tự Động VietQR Chuẩn Napas 247:**  
   Sinh mã QR thanh toán động theo chuẩn Napas 247 ngân hàng đối tác, tự động điền sẵn số tiền và nội dung chuyển khoản `DATSAN <id>` hỗ trợ nút sao chép thông minh (Cấu hình STK và ngân hàng linh hoạt qua biến môi trường `VIETQR_ACCOUNT_NO` và `VIETQR_BANK_ID` trong `.env`).
6. 🧩 **Kiến trúc Modular MVC Thuần:**  
   Toàn bộ mã nguồn được tách biệt nghiêm ngặt: 0% inline CSS/JS trong View, giao diện Admin và Client được tổ chức thành các module stylesheet (`admin/css/`, `assets/css/modules/`) và file JavaScript chuyên biệt.

---

## 📋 TỔNG HỢP 14 CHỨC NĂNG HỆ THỐNG

Dự án được xây dựng với đầy đủ 14 chức năng chuẩn đáp ứng toàn diện nhu cầu của Khách hàng và Ban quản lý:

### 👥 Phân Hệ Khách Hàng (Client Portal)
1. **Chức năng Đăng ký / Đăng nhập / Đăng xuất:** Xác thực người dùng an toàn, mã hóa mật khẩu BCRYPT, so sánh nhị phân `BINARY` phân biệt chính xác chữ hoa/thường (`admin15 !== ADMIN15`), Anti-CSRF Token 64-hex chống giả mạo request.
2. **Tìm kiếm và Lọc sân:** Tìm kiếm thời gian thực (Debounce Search) theo tên sân hoặc địa chỉ không cần tải lại trang; bộ lọc nâng cao đa tiêu chí theo quy mô (Sân 5 / Sân 7) và thanh trượt khoảng giá thuê.
3. **Xem sân, Tra cứu lịch và Sơ đồ 2D:** Bản đồ sân bóng 2D trực quan với 3 trạng thái màu (xanh: trống, vàng: giữ chỗ, đỏ: đã đặt); tra cứu lịch AJAX theo ngày (giới hạn từ 2025); khoảng đệm 5 phút chuẩn bị sân (Lead Time Buffer) tự động làm mờ và khóa các khung giờ đã qua hoặc sát giờ đá.
4. **Đặt sân và Chống trùng (Double-Booking & Giữ chỗ 10 phút):** Giao dịch CSDL kết hợp khóa dòng `SELECT ... FOR UPDATE` và ràng buộc duy nhất `active_key` triệt tiêu 100% rủi ro đặt trùng; bộ đếm giữ chỗ 10 phút với cơ chế tự động hủy và giải phóng slot CSDL (`released`) để nhường sân cho người khác.
5. **Ghép sân 5 và 7 (Khóa chéo đa năng):** Thuật toán khóa chéo hai chiều linh hoạt (`san_con_khoa = 'A,B'`); khi sân 7 được đặt, toàn bộ các sân 5 con thành phần tự động khóa và ngược lại.
6. **Thanh toán Tiền mặt và VietQR (Tích hợp Webhook SePAY):** Tự động sinh mã VietQR Napas 247 MB Bank điền sẵn số tiền và nội dung `DATSAN <id>`; tích hợp Webhook SePAY tự động nhận diện giao dịch và duyệt đơn tức thì theo thời gian thực (hỗ trợ cả môi trường Live và Sandbox Mock 0đ); ẩn nút xác nhận thủ công ở tab VietQR.
7. **Điểm uy tín và Chế tài bùng sân:** Thang điểm tín nhiệm chuẩn [0, 100]; tự động cộng điểm khi hoàn thành trận (+5đ) hoặc gửi đánh giá (+2đ); trừ 25đ khi bùng sân; tự động khóa phương thức tiền mặt nếu điểm $< 60$đ và khóa quyền đặt sân nếu điểm $< 40$đ.
8. **Hủy đơn và Hoàn tiền:** Quy tắc phạt điểm hủy sân minh bạch theo mốc giờ (>24h: 0đ, 12-24h: -5đ, <12h: -15đ); bắt buộc cung cấp STK ngân hàng trước khi hủy đơn chuyển khoản; lập tức giải phóng slot CSDL; tự động chuyển các đơn VietQR chưa duyệt quá giờ thi đấu sang `Chờ hoàn tiền`.

### 🛠️ Phân Hệ Quản Trị Viên (Admin Portal)
9. **ADM Đăng nhập và Phân quyền (RBAC):** Cơ chế kiểm soát truy cập phân quyền nghiêm ngặt, chặn đứng mọi truy cập trái phép từ khách vãng lai và người dùng thông thường vào phân hệ quản trị `/admin/`.
10. **ADM Quản lý Sân bóng và Cấu hình ghép:** Thêm mới, chỉnh sửa thông tin và xóa sân bóng; kiểm soát ràng buộc toàn vẹn ngăn xóa sân đang có đơn đặt; cấu hình số lượng sân con và quan hệ ghép sân 5 thành sân 7 linh hoạt.
11. **ADM Khung giờ và Bảng giá:** Quản lý danh mục khung giờ hoạt động; cấu hình bảng giá riêng cho các khung giờ vàng, tỷ lệ phụ thu ngày cuối tuần và biểu giá riêng cho sân 7 ghép.
12. **ADM Quản lý Đặt sân & Hoàn tiền:** Giao diện quản lý đặt sân tinh gọn, tối ưu trải nghiệm; duyệt đơn, từ chối đơn giải phóng lịch, xác nhận thu tiền mặt tại sân, báo bùng sân trừ điểm; hiển thị mã VietQR chứa sẵn STK của khách để quét chuyển khoản hoàn tiền một chạm.
13. **ADM Quản lý Người dùng:** Tra cứu thông tin khách hàng, kiểm tra chi tiết nhật ký biến động điểm uy tín; can thiệp điều chỉnh điểm thủ công kèm lý do; khóa và mở khóa tài khoản người dùng vi phạm.
14. **ADM Báo cáo Doanh thu và Thống kê (Xuất Excel):** Lọc doanh thu theo ngày, tuần, tháng (chuẩn hóa mốc $\ge$ 2025-01-01); thống kê tổng doanh thu, lượt đặt, tỷ lệ lấp đầy; phân tách rõ tiền mặt và VietQR; hỗ trợ xuất báo cáo bảng tính Excel (.csv UTF-8 BOM kèm hộp thoại xác nhận).

---

## 🧪 MÔ-ĐUN KIỂM THỬ PHẦN MỀM (`tester/`)

Phục vụ học phần **Kiểm thử phần mềm**, toàn bộ tài liệu kiểm thử, kịch bản kiểm thử 14 chức năng, ma trận test case và 5 công cụ kiểm thử tự động chuyên nghiệp (PHPUnit, Postman, Playwright, Apache JMeter, OWASP ZAP) được tổ chức và quản lý hoàn toàn độc lập trong thư mục `tester/`:

```text
tester/
├── 01_unit_test/              # Kiểm thử Hộp Trắng (PHPUnit 10.5): 88 Tests, 232 Assertions (100% Pass)
├── 02_api_test/                # Kiểm thử API & Tích hợp (Postman & Newman): 25 API Requests, 34 Assertions (100% Pass)
├── 03_e2e_ui_test/             # Kiểm thử Tự động Giao diện (Playwright E2E): 12 Luồng nghiệp vụ (100% Pass)
├── 04_load_concurrency_test/   # Kiểm thử Tải & Race Condition (Apache JMeter 5.6): 3 Test Plans + One-Click Runner
└── 05_security_test/           # Kiểm thử An toàn Thông tin (OWASP ZAP & Automated Auditor): 9/9 Chốt bảo mật (100% Pass)
```

👉 **Xem chi tiết Kịch bản kiểm thử 14 chức năng, ma trận 88 test cases và hướng dẫn chạy kiểm thử tại:**  
📖 **[Tài liệu kiểm thử phần mềm chi tiết (tester/README.md)](tester/README.md)**

---

## 📂 KIẾN TRÚC MÃ NGUỒN (DIRECTORY STRUCTURE)

```text
quanlysanbong/
├── index.php                          # Entry point duy nhất, Autoloader, Global Middleware
├── .env.example                       # Biến môi trường mẫu (Database, Mail, VietQR)
├── .gitignore                         # Bảo vệ thông tin bí mật, loại bỏ .env & artifacts
├── config/
│   └── Database.php                   # Kết nối CSDL MySQL Singleton & dọn dẹp đơn hết hạn
├── core/
│   ├── Router.php                     # Bộ định tuyến request an toàn, lọc ký tự độc
│   ├── Controller.php                 # Base Controller cung cấp render & jsonResponse
│   └── DotEnv.php                     # Bộ phân tích nạp file cấu hình .env
├── controllers/                       # 8 Controllers quản lý toàn bộ phân hệ Client & Admin
├── models/                            # 7 Models chuẩn hóa truy vấn CSDL qua Prepared Statements
├── views/                             # Giao diện người dùng thuần HTML/PHP, phân chia theo module
├── admin/                             # Phân hệ Quản trị viên (Dashboard, Đặt sân, Sân, Khách hàng)
│   ├── css/                           # 7 file CSS modular tách biệt (base, components, tables...)
│   └── js/                            # JavaScript điều khiển AJAX, biểu đồ Chart.js, Modal
├── assets/                            # Tài nguyên tĩnh phía Client
│   ├── css/modules/                   # 6 file CSS modular cho giao diện Client
│   └── js/                            # Script xử lý tìm kiếm debounce, chọn lịch 2D, thanh toán
├── includes/                          # Header, Footer, Menu, CSRF Guard, PHPMailer
└── tester/                            # 5 Công cụ kiểm thử phần mềm chuyên nghiệp
```

---

## 🚀 HƯỚNG DẪN CÀI ĐẶT & CHẠY DỰ ÁN

### 1. Yêu Cầu Môi Trường
* **PHP**: `>= 8.2` (Bật các extension: `mysqli`, `curl`, `mbstring`, `openssl`).
* **Cơ sở dữ liệu**: MySQL / MariaDB (XAMPP khuyến nghị).
* **Node.js**: `>= 18.x` (Dùng để chạy Playwright và Newman).

### 2. Các Bước Cài Đặt

1. **Clone mã nguồn về máy:**
   ```bash
   git clone https://github.com/vanhuy15/quanLySanBongDaPhuiDaNang.git
   cd quanLySanBongDaPhuiDaNang
   ```

2. **Cấu hình môi trường (.env):**
   Copy file `.env.example` thành `.env` và điền cấu hình CSDL của bạn:
   ```bash
   cp .env.example .env
   ```

3. **Cài đặt thư viện kiểm thử (Playwright & Newman):**
   ```bash
   npm install
   npx playwright install chromium
   ```

4. **Khởi chạy ứng dụng & Dữ liệu mẫu:**
   * Mở XAMPP và Start **Apache** + **MySQL**.
   * Chạy lệnh nạp schema & migration: `npm run db:migrate`
   * Nạp tài khoản mẫu kiểm thử: `npm run db:seed`
   * Truy cập ứng dụng trên trình duyệt: `http://localhost/quanlysanbong/`
   * Tài khoản Quản trị mẫu: `admin15` (Mật khẩu cấu hình bảo mật qua `.env` / `SEED_ADMIN_PASS`)
   * Tài khoản Khách hàng thử nghiệm: `testuser` (Mật khẩu cấu hình bảo mật qua `.env` / `SEED_TEST_PASS`)

---

## 👨‍💻 TÁC GIẢ & BẢN QUYỀN

* **Tác giả:** Hoàng Văn Huy ([@vanhuy15](https://github.com/vanhuy15))
* **GitHub Repository:** [quanLySanBongDaPhuiDaNang](https://github.com/vanhuy15/quanLySanBongDaPhuiDaNang)
* **Giấy phép:** [MIT License](LICENSE)
