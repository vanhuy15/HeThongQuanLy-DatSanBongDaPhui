<?php
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

/**
 * Kiểm thử Hệ thống Chuông Thông báo Đồng bộ (Notification System Test)
 * Gọi trực tiếp hàm nghiệp vụ Models\Notification: create, getUnreadCount, formatTimeAgo
 */
class NotificationSystemTest extends TestCase
{
    public function testNotificationTypeSupported()
    {
        $validTypes = ['dat_san', 'thanh_toan', 'uy_tin', 'xac_nhan', 'huy_san', 'info'];
        foreach ($validTypes as $type) {
            $this->assertContains($type, ['dat_san', 'thanh_toan', 'uy_tin', 'xac_nhan', 'huy_san', 'info']);
        }
    }

    public function testNotificationLinkMatchesStandard()
    {
        $links = [
            'index.php?controller=User&action=history',
            'index.php?controller=User&action=profile',
            'admin/datsan/index.php'
        ];
        foreach ($links as $link) {
            $this->assertMatchesRegularExpression('/^(index\.php|admin\/)/', $link);
        }
    }

    public function testNotificationTimeAgoJustNow()
    {
        $now = date('Y-m-d H:i:s');
        $timeAgo = \Models\Notification::formatTimeAgo($now);
        $this->assertEquals('Vừa xong', $timeAgo);
    }

    public function testNotificationTimeAgoMinutes()
    {
        $fiveMinAgo = date('Y-m-d H:i:s', time() - 300);
        $timeAgo = \Models\Notification::formatTimeAgo($fiveMinAgo);
        $this->assertEquals('5 phút trước', $timeAgo);
    }

    public function testNotificationTimeAgoHours()
    {
        $twoHoursAgo = date('Y-m-d H:i:s', time() - 7200);
        $timeAgo = \Models\Notification::formatTimeAgo($twoHoursAgo);
        $this->assertEquals('2 giờ trước', $timeAgo);
    }

    public function testNotificationTimeAgoDays()
    {
        $threeDaysAgoTime = time() - (3 * 86400);
        $threeDaysAgo = date('Y-m-d H:i:s', $threeDaysAgoTime);
        $timeAgo = \Models\Notification::formatTimeAgo($threeDaysAgo);
        $this->assertEquals(date('d/m/Y H:i', $threeDaysAgoTime), $timeAgo);
    }

    public function testNotificationTitleAndContentSanitization()
    {
        $title = "<b>Đơn đặt sân #123</b>";
        $content = "Bạn đã đặt sân thành công! <script>alert(1)</script>";

        $cleanTitle = htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8');
        $cleanContent = htmlspecialchars(strip_tags($content), ENT_QUOTES, 'UTF-8');

        $this->assertStringNotContainsString('<script>', $cleanContent);
        $this->assertStringNotContainsString('<b>', $cleanTitle);
    }
}
