<?php

use PHPUnit\Framework\TestCase;

/**
 * Kiểm thử chính sách điểm uy tín (Reputation Policy)
 * Gọi trực tiếp hàm nghiệp vụ production Models\User: canBook, canPayCash, clampReputation
 */
class ReputationPolicyTest extends TestCase
{

    /**
     * Quy tắc 1: Khách hàng có điểm uy tín < 40 bị KHÓA quyền đặt sân
     */
    public function testBookingBlockedWhenReputationBelow40()
    {
        $blockedScores = [0, 15, 39];
        foreach ($blockedScores as $score) {
            $canBook = \Models\User::canBook($score);
            $this->assertFalse($canBook, "Điểm {$score} < 40 phải bị khóa quyền đặt sân");
        }
    }

    public function testBookingAllowedWhenReputationAtLeast40()
    {
        $allowedScores = [40, 50, 80, 100];
        foreach ($allowedScores as $score) {
            $canBook = \Models\User::canBook($score);
            $this->assertTrue($canBook, "Điểm {$score} >= 40 được phép đặt sân bình thường");
        }
    }

    /**
     * Quy tắc 2: Khách hàng có điểm uy tín < 60 không được chọn Tiền mặt
     */
    public function testCashPaymentBlockedWhenReputationBelow60()
    {
        $lowScores = [40, 50, 59];
        foreach ($lowScores as $score) {
            $canUseCash = \Models\User::canPayCash($score);
            $this->assertFalse($canUseCash, "Điểm {$score} < 60 không được phép chọn Tiền mặt");
        }
    }

    public function testCashPaymentAllowedWhenReputationAtLeast60()
    {
        $goodScores = [60, 85, 100];
        foreach ($goodScores as $score) {
            $canUseCash = \Models\User::canPayCash($score);
            $this->assertTrue($canUseCash, "Điểm {$score} >= 60 được phép chọn Tiền mặt");
        }
    }

    /**
     * Quy tắc 3: Đánh giá sân (Review) được cộng 2 điểm nhưng không vượt quá 100
     */
    public function testReviewBonusCappedAt100()
    {
        $scoreBefore = 99;
        $scoreAfter = \Models\User::clampReputation($scoreBefore + 2);
        $this->assertEquals(100, $scoreAfter, "Cộng điểm review không được vượt quá 100");

        $scoreNormal = 80;
        $scoreNormalAfter = \Models\User::clampReputation($scoreNormal + 2);
        $this->assertEquals(82, $scoreNormalAfter, "Cộng điểm review hợp lệ");
    }

    public function testCompleteMatchBonusCappedAt100() {
        $scoreBefore = 98;
        $scoreAfter = \Models\User::clampReputation($scoreBefore + 5);
        $this->assertEquals(100, $scoreAfter, "Hoàn thành trận +5 điểm tối đa 100");
    }
}
