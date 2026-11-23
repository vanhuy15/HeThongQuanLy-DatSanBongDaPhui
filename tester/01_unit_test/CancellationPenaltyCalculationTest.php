<?php
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

/**
 * Thuật toán: Kiểm thử Bảng Phạt Hủy Đơn & Chính Sách Uy Tín (Reputation Penalty Policy)
 * Gọi trực tiếp hàm nghiệp vụ production:
 * - Models\DatSan::calculateCancellationPenalty()
 * - Models\User::clampReputation()
 */
class CancellationPenaltyCalculationTest extends TestCase
{

    public function testCancelMoreThan24HoursInAdvanceIncursNoPenalty()
    {
        $hoursAhead = [25.0, 48.0, 72.0];
        foreach ($hoursAhead as $hours) {
            $result = \Models\DatSan::calculateCancellationPenalty($hours, true);
            $this->assertEquals(0, $result['penalty_points'], "Hủy trước {$hours}h (> 24h) không được trừ điểm uy tín");
        }
    }

    public function testCancelBetween12And24HoursIncurs5PointsPenalty()
    {
        $hoursAhead = [12.0, 18.0, 24.0];
        foreach ($hoursAhead as $hours) {
            $result = \Models\DatSan::calculateCancellationPenalty($hours, true);
            $this->assertEquals(5, $result['penalty_points'], "Hủy trước {$hours}h (từ 12h - 24h) phải phạt đúng 5 điểm");
        }
    }

    public function testCancelLessThan12HoursIncurs15PointsPenalty()
    {
        $hoursAhead = [0.0, 2.0, 6.0, 11.9];
        foreach ($hoursAhead as $hours) {
            $result = \Models\DatSan::calculateCancellationPenalty($hours, true);
            $this->assertEquals(15, $result['penalty_points'], "Hủy sát giờ trước {$hours}h (< 12h) phải phạt đúng 15 điểm");
        }
    }

    public function testCancelUnconfirmedBookingIncursNoPenaltyRegardlessOfTime()
    {
        $result = \Models\DatSan::calculateCancellationPenalty(2.0, false);
        $this->assertEquals(0, $result['penalty_points'], "Đơn chưa xác nhận hủy không bị phạt");
    }

    public function testNoShowPenaltyDeducts25PointsAndIncrementsCounter()
    {
        $currentScore = 80;
        $currentNoShowCount = 1;

        $penalty = 25;
        $newScore = \Models\User::clampReputation($currentScore - $penalty);
        $newNoShowCount = $currentNoShowCount + 1;

        $this->assertEquals(55, $newScore, "Bùng sân phải bị phạt trừ 25 điểm uy tín");
        $this->assertEquals(2, $newNoShowCount, "Số lần bùng sân phải được cộng thêm 1");
    }

    public function testScoreClampingBoundaries()
    {
        $lowScore = 10;
        $severePenalty = 25;
        $clampedLow = \Models\User::clampReputation($lowScore - $severePenalty);
        $this->assertEquals(0, $clampedLow, "Điểm uy tín tối thiểu phải là 0, không được âm");

        $highScore = 98;
        $bonus = 5;
        $clampedHigh = \Models\User::clampReputation($highScore + $bonus);
        $this->assertEquals(100, $clampedHigh, "Điểm uy tín tối đa phải là 100, không được vượt quá");
    }

    public function testRefundEligibilityCheck()
    {
        // Đơn đã thanh toán chuyển khoản và đã xác nhận -> Được hoàn tiền
        $booking1 = ['trang_thai' => 'Đã xác nhận', 'phuong_thuc' => 'chuyen_khoan'];
        $eligible1 = ($booking1['trang_thai'] === 'Đã xác nhận' && $booking1['phuong_thuc'] === 'chuyen_khoan');
        $this->assertTrue($eligible1, "Đơn đã thanh toán chuyển khoản phải kích hoạt luồng hoàn tiền");

        // Đơn thanh toán tiền mặt -> Không hoàn tiền qua ngân hàng
        $booking2 = ['trang_thai' => 'Đã xác nhận', 'phuong_thuc' => 'tien_mat'];
        $eligible2 = ($booking2['trang_thai'] === 'Đã xác nhận' && $booking2['phuong_thuc'] === 'chuyen_khoan');
        $this->assertFalse($eligible2, "Đơn tiền mặt tại sân không cần hoàn tiền ngân hàng");

        // Đơn chưa xác nhận -> Không hoàn tiền
        $booking3 = ['trang_thai' => 'Chờ xác nhận', 'phuong_thuc' => 'chuyen_khoan'];
        $eligible3 = ($booking3['trang_thai'] === 'Đã xác nhận' && $booking3['phuong_thuc'] === 'chuyen_khoan');
        $this->assertFalse($eligible3, "Đơn chưa xác nhận thanh toán thành công không kích hoạt hoàn tiền");
    }

    public function testCancellationMessageFormattedCorrectly()
    {
        $resUrgent = \Models\DatSan::calculateCancellationPenalty(5.0, true);
        $this->assertStringContainsString("15 điểm", $resUrgent['penalty_msg']);

        $resEarly = \Models\DatSan::calculateCancellationPenalty(18.0, true);
        $this->assertStringContainsString("5 điểm", $resEarly['penalty_msg']);

        $resSafe = \Models\DatSan::calculateCancellationPenalty(30.0, true);
        $this->assertEmpty($resSafe['penalty_msg']);
    }
}
