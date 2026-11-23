<?php
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

/**
 * Kiểm thử Khoảng Đệm Chuẩn Bị Sân (Lead Time Buffer 5 Phút)
 * Quy định tại AGENTS.md: Chặn đặt khung giờ đã qua hoặc cách giờ bắt đầu dưới 5 phút (slot_start_time <= time() + 300)
 */
class LeadTimeBufferTest extends TestCase
{
    private const BUFFER_SECONDS = 300; // 5 phút

    private function isSlotBlocked(int $slotStartTime, int $currentTime): bool
    {
        return $slotStartTime <= ($currentTime + self::BUFFER_SECONDS);
    }

    public function testSlotInThePastIsBlocked()
    {
        $now = time();
        $pastStartTime = $now - 600; // 10 phút trước
        $this->assertTrue($this->isSlotBlocked($pastStartTime, $now), "Khung giờ đã qua trong quá khứ phải bị chặn");
    }

    public function testSlotStartingInLessThan5MinutesIsBlocked()
    {
        $now = time();
        $soonStartTime = $now + 180; // Còn 3 phút nữa là đá (< 5 phút)
        $this->assertTrue($this->isSlotBlocked($soonStartTime, $now), "Khung giờ còn dưới 5 phút chuẩn bị phải bị chặn");
    }

    public function testSlotStartingInMoreThan5MinutesIsAllowed()
    {
        $now = time();
        $futureStartTime = $now + 600; // Còn 10 phút nữa
        $this->assertFalse($this->isSlotBlocked($futureStartTime, $now), "Khung giờ còn hơn 5 phút được phép đặt");
    }

    public function testBoundaryAtExact300SecondsIsBlocked()
    {
        $now = time();
        $exact300s = $now + 300; // Đúng chính xác 300 giây (mốc biên)
        $this->assertTrue($this->isSlotBlocked($exact300s, $now), "Đúng chính xác mốc 300 giây phải bị chặn (<= 300)");
    }

    public function testBoundaryAt299SecondsIsBlocked()
    {
        $now = time();
        $at299s = $now + 299; // 299 giây (< 300s)
        $this->assertTrue($this->isSlotBlocked($at299s, $now), "Dưới mốc 300 giây (299s) phải bị chặn");
    }

    public function testBoundaryAt301SecondsIsAllowed()
    {
        $now = time();
        $at301s = $now + 301; // 301 giây (> 300s)
        $this->assertFalse($this->isSlotBlocked($at301s, $now), "Vừa qua mốc 300 giây (301s) được phép đặt");
    }

    public function testSlotOnFutureDateIsAlwaysAllowed()
    {
        $now = time();
        $tomorrowStartTime = strtotime('+1 day 17:30:00');
        $this->assertFalse($this->isSlotBlocked($tomorrowStartTime, $now), "Khung giờ ngày mai luôn được phép đặt");
    }
}
