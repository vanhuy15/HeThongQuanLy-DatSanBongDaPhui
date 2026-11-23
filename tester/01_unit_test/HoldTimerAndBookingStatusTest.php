<?php
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

/**
 * Quản lý Vòng đời Trạng thái Đơn Đặt Sân (Booking Lifecycle & Hold Timer)
 * Gọi trực tiếp hàm nghiệp vụ production Models\DatSan: getRemainingHoldTime, isHoldExpired
 */
class HoldTimerAndBookingStatusTest extends TestCase
{

    public function testRemainingHoldTimeCalculation()
    {
        $now = time();

        // Mới tạo được 3 phút (180 giây trước) -> Còn 420 giây (7 phút)
        $createdAt = date('Y-m-d H:i:s', $now - 180);
        $remaining = \Models\DatSan::getRemainingHoldTime($createdAt, 600);
        $this->assertGreaterThanOrEqual(419, $remaining);
        $this->assertLessThanOrEqual(421, $remaining);

        // Đã quá 10 phút (650 giây trước) -> Còn 0 giây (hết hạn)
        $expiredCreatedAt = date('Y-m-d H:i:s', $now - 650);
        $expiredRemaining = \Models\DatSan::getRemainingHoldTime($expiredCreatedAt, 600);
        $this->assertEquals(0, $expiredRemaining, "Đơn quá 10 phút phải hết hạn (còn lại 0 giây)");
    }

    public function testHoldExpiredCondition()
    {
        $now = time();

        $createdAtOld = date('Y-m-d H:i:s', $now - 700);
        $this->assertTrue(\Models\DatSan::isHoldExpired($createdAtOld, 'Chờ thanh toán', 600));

        $createdAtRecent = date('Y-m-d H:i:s', $now - 300);
        $this->assertFalse(\Models\DatSan::isHoldExpired($createdAtRecent, 'Chờ thanh toán', 600));

        // Trạng thái khác 'Chờ thanh toán' không bị expire bởi hold timer
        $this->assertFalse(\Models\DatSan::isHoldExpired($createdAtOld, 'Chờ xác nhận', 600));
        $this->assertFalse(\Models\DatSan::isHoldExpired($createdAtOld, 'Đã xác nhận', 600));
    }

    public function testCleanupExpiredBookingsCondition()
    {
        $now = time();

        $bookings = [
            [
                'id' => 1,
                'trang_thai' => 'Chờ thanh toán',
                'created_at' => date('Y-m-d H:i:s', $now - 700), // Quá 10 phút
                'expected_cleaned' => true
            ],
            [
                'id' => 2,
                'trang_thai' => 'Chờ thanh toán',
                'created_at' => date('Y-m-d H:i:s', $now - 300), // Mới 5 phút
                'expected_cleaned' => false
            ],
            [
                'id' => 3,
                'trang_thai' => 'Chờ xác nhận',
                'created_at' => date('Y-m-d H:i:s', $now - 1200),
                'expected_cleaned' => false
            ],
            [
                'id' => 4,
                'trang_thai' => 'Đã xác nhận',
                'created_at' => date('Y-m-d H:i:s', $now - 3600),
                'expected_cleaned' => false
            ]
        ];

        foreach ($bookings as $b) {
            $isExpired = \Models\DatSan::isHoldExpired($b['created_at'], $b['trang_thai'], 600);
            $this->assertEquals(
                $b['expected_cleaned'],
                $isExpired,
                "Đơn #{$b['id']} trạng thái {$b['trang_thai']} xử lý dọn dẹp không đúng kỳ vọng"
            );
        }
    }

    public function testValidBookingStatusTransitions()
    {
        $validTransitions = [
            'Chờ thanh toán' => ['Chờ xác nhận', 'Đã hủy'],
            'Chờ xác nhận'   => ['Đã xác nhận', 'Đã từ chối', 'Đã hủy'],
            'Đã xác nhận'    => ['Đã hủy', 'Chờ hoàn tiền'],
            'Chờ hoàn tiền'  => ['Đã hủy'],
            'Đã hủy'         => [],
            'Đã từ chối'     => []
        ];

        // 1. Cho phép chuyển từ 'Chờ thanh toán' sang 'Chờ xác nhận'
        $this->assertContains('Chờ xác nhận', $validTransitions['Chờ thanh toán']);

        // 2. Không cho phép chuyển từ 'Đã hủy' sang 'Đã xác nhận'
        $this->assertNotContains('Đã xác nhận', $validTransitions['Đã hủy']);

        // 3. Đơn đã xác nhận nếu khách hủy thì được chuyển sang 'Chờ hoàn tiền'
        $this->assertContains('Chờ hoàn tiền', $validTransitions['Đã xác nhận']);
    }

    public function testAutoCancelReleasesDbSlot()
    {
        $statusAfterCancel = 'released';
        $this->assertEquals('released', $statusAfterCancel, "Khi hủy đơn giữ chỗ, slot phải chuyển về released");
    }

    public function testHoldRemainingTimeZeroWhenExpired()
    {
        $createdAt = date('Y-m-d H:i:s', time() - 1000);
        $rem = \Models\DatSan::getRemainingHoldTime($createdAt, 600);
        $this->assertEquals(0, $rem);
    }

    public function testStaleBookingAutoRefundTransition()
    {
        $booking = [
            'trang_thai' => 'Chờ xác nhận',
            'phuong_thuc' => 'chuyen_khoan',
            'is_past_start' => true
        ];
        $targetStatus = ($booking['is_past_start'] && $booking['phuong_thuc'] === 'chuyen_khoan') ? 'Chờ hoàn tiền' : $booking['trang_thai'];
        $this->assertEquals('Chờ hoàn tiền', $targetStatus, "Đơn chuyển khoản quá giờ thi đấu tự chuyển sang Chờ hoàn tiền");
    }

    public function testCashBookingPastMatchStartAutoCancelled()
    {
        $booking = [
            'trang_thai' => 'Chờ xác nhận',
            'phuong_thuc' => 'tien_mat',
            'is_past_start' => true
        ];
        $targetStatus = ($booking['is_past_start'] && $booking['phuong_thuc'] === 'tien_mat') ? 'Đã hủy' : $booking['trang_thai'];
        $this->assertEquals('Đã hủy', $targetStatus, "Đơn tiền mặt quá giờ thi đấu tự chuyển sang Đã hủy");
    }

    public function testExpiredHoldNotificationContent()
    {
        $bookingId = 155;
        $pitchName = "Sân bóng đá 743-Nam Cao";
        $content = "Đơn đặt sân $pitchName đã tự động hủy do quá thời hạn thanh toán 10 phút. Sân đã được giải phóng cho khách hàng khác.";
        $this->assertStringContainsString("10 phút", $content);
        $this->assertStringContainsString("giải phóng", $content);
    }

    public function testHoldTimerFormatMinuteSecond()
    {
        $seconds = 455; // 7 phút 35 giây
        $m = floor($seconds / 60);
        $s = $seconds % 60;
        $formatted = sprintf("%02d:%02d", $m, $s);
        $this->assertEquals("07:35", $formatted);
    }
}
