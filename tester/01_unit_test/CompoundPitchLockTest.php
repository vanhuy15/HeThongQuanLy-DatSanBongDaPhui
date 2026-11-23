<?php

use PHPUnit\Framework\TestCase;

/**
 * Kiểm thử Logic Ghép/Tách Sân Đa Năng (Compound Pitch Merging)
 * Gọi trực tiếp hàm nghiệp vụ production Models\DatSan::checkCompoundPitchConflict()
 */
class CompoundPitchLockTest extends TestCase
{

    public function testSan7BlockedWhenSanConAIsAlreadyBooked()
    {
        $existingBookings = [
            ['ten_san_con' => 'Sân con A', 'san_con_khoa' => null]
        ];

        // Khách muốn đặt Sân 7 (khóa A,B)
        $hasConflict = \Models\DatSan::checkCompoundPitchConflict('Sân 7 (A+B)', 'A,B', $existingBookings);
        $this->assertTrue($hasConflict, "Không được phép đặt Sân 7 khi Sân con A đã có khách đặt");
    }

    public function testSan7BlockedWhenSanConBIsAlreadyBooked()
    {
        $existingBookings = [
            ['ten_san_con' => 'Sân con B', 'san_con_khoa' => null]
        ];

        $hasConflict = \Models\DatSan::checkCompoundPitchConflict('Sân 7 (A+B)', 'A,B', $existingBookings);
        $this->assertTrue($hasConflict, "Không được phép đặt Sân 7 khi Sân con B đã có khách đặt");
    }

    public function testSanConABlockedWhenSan7IsAlreadyBooked()
    {
        $existingBookings = [
            ['ten_san_con' => 'Sân 7 (A+B)', 'san_con_khoa' => 'A,B']
        ];

        $hasConflict = \Models\DatSan::checkCompoundPitchConflict('Sân con A', null, $existingBookings);
        $this->assertTrue($hasConflict, "Không được phép đặt Sân con A khi cụm Sân 7 đang được giữ chỗ");
    }

    public function testSanConBBlockedWhenSan7IsAlreadyBooked()
    {
        $existingBookings = [
            ['ten_san_con' => 'Sân 7 (A+B)', 'san_con_khoa' => 'A,B']
        ];

        $hasConflict = \Models\DatSan::checkCompoundPitchConflict('Sân con B', null, $existingBookings);
        $this->assertTrue($hasConflict, "Không được phép đặt Sân con B khi cụm Sân 7 đang được giữ chỗ");
    }

    public function testIndependentSanConCAllowedWhenSan7IsBooked() {
        $existingBookings = [
            ['ten_san_con' => 'Sân 7 (A+B)', 'san_con_khoa' => 'A,B']
        ];

        $hasConflict = \Models\DatSan::checkCompoundPitchConflict('Sân con C', null, $existingBookings);
        $this->assertFalse($hasConflict, "Sân con C độc lập không bị ảnh hưởng bởi cụm Sân 7 (A+B)");
    }

    public function testBothSanConFreeWhenSan7IsReleased() {
        $existingBookings = []; // Giả lập sau khi hủy/giải phóng

        $conflictA = \Models\DatSan::checkCompoundPitchConflict('Sân con A', null, $existingBookings);
        $conflictB = \Models\DatSan::checkCompoundPitchConflict('Sân con B', null, $existingBookings);

        $this->assertFalse($conflictA, "Sân con A phải khả dụng sau khi Sân 7 giải phóng");
        $this->assertFalse($conflictB, "Sân con B phải khả dụng sau khi Sân 7 giải phóng");
    }
}
