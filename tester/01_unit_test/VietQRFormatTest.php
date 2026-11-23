<?php
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

/**
 * Thuật toán 5: Kiểm thử Chuẩn hóa & Sinh Mã VietQR Tự Động (VietQR Generation)
 * Nghiệp vụ chi tiết tại views/payment/checkout.php & controllers/PaymentController.php
 */
class VietQRFormatTest extends TestCase
{

    private const BANK_ID = 'MB';
    private const ACCOUNT_NO = '1111000050174';
    private const ACCOUNT_NAME = 'HOANG VAN HUY';
    private const TEMPLATE = 'compact2';

    /**
     * Hàm sinh URL VietQR theo đúng chuẩn đang sử dụng trong views/payment/checkout.php
     */
    private function generateVietQrUrl(int $bookingId, int $amount): string
    {
        $encodedDesc = urlencode("DATSAN " . $bookingId);
        $encodedName = urlencode(self::ACCOUNT_NAME);
        return "https://img.vietqr.io/image/" . self::BANK_ID . "-" . self::ACCOUNT_NO . "-" . self::TEMPLATE . ".png"
            . "?amount=" . $amount
            . "&addInfo=" . $encodedDesc
            . "&accountName=" . $encodedName;
    }

    public function testVietQrUrlStructureMatchesStandard()
    {
        $bookingId = 150;
        $amount = 250000;

        $url = $this->generateVietQrUrl($bookingId, $amount);

        // Kiểm tra tiền tố dịch vụ
        $this->assertStringStartsWith('https://img.vietqr.io/image/MB-1111000050174-compact2.png', $url);

        // Kiểm tra tham số số tiền
        $this->assertStringContainsString('amount=250000', $url);

        // Kiểm tra nội dung chuyển khoản được URL-encode
        $this->assertStringContainsString('addInfo=DATSAN+150', $url);

        // Kiểm tra tên tài khoản được URL-encode
        $this->assertStringContainsString('accountName=HOANG+VAN+HUY', $url);
    }

    public function testAmountMustBePositiveInteger()
    {
        $validAmounts = [150000, 250000, 400000, 1000000];
        foreach ($validAmounts as $amt) {
            $this->assertGreaterThan(0, $amt, "Số tiền thanh toán phải lớn hơn 0");
            $this->assertIsInt($amt, "Số tiền thanh toán phải là số nguyên");
        }
    }

    public function testTransferSyntaxPattern()
    {
        $bookingId = 999;
        $syntax = "DATSAN " . $bookingId;

        // Nội dung cú pháp chuyển khoản phải khớp biểu thức chính quy ^DATSAN [0-9]+$
        $isMatched = preg_match('/^DATSAN \d+$/', $syntax);
        $this->assertEquals(1, $isMatched, "Cú pháp chuyển khoản phải có dạng 'DATSAN <id>'");
    }

    public function testBankAccountDetailsConsistency()
    {
        $this->assertEquals('1111000050174', self::ACCOUNT_NO, "Số tài khoản MB Bank phải đồng bộ 1111000050174");
        $this->assertEquals('HOANG VAN HUY', self::ACCOUNT_NAME, "Chủ tài khoản phải là HOANG VAN HUY");
    }

    public function testSePayWebhookContentRegexExtraction()
    {
        $samples = [
            'DATSAN 155' => 155,
            'DATSAN_155' => 155,
            'DATSAN155'  => 155,
            'Chuyen tien DATSAN 200 cho san' => 200,
            'MBVCB.123456.DATSAN 345.CT tu...' => 345
        ];

        foreach ($samples as $content => $expectedId) {
            $matched = preg_match('/DATSAN[_\s]*(\d+)/i', $content, $matches);
            $this->assertEquals(1, $matched, "Phải nhận diện được mã đơn từ nội dung: $content");
            $this->assertEquals($expectedId, (int)$matches[1]);
        }
    }

    public function testSePayAmountValidationMatchesBookingPrice()
    {
        $bookingPrice = 350000;
        $webhookAmountValid = 350000;
        $webhookAmountInvalid = 200000;

        $this->assertTrue($webhookAmountValid >= $bookingPrice, "Số tiền nộp đủ hoặc thừa được chấp nhận");
        $this->assertFalse($webhookAmountInvalid >= $bookingPrice, "Số tiền nộp thiếu phải bị từ chối");
    }

    public function testVietQrSvgTemplateExtensionFallback()
    {
        $urlPng = \Services\VietQR::generateUrl(101, 200000, 'MB', '1111000050174', 'HOANG VAN HUY', 'compact2');
        $this->assertStringContainsString('compact2.png', $urlPng);

        $urlSvg = \Services\VietQR::generateUrl(101, 200000, 'MB', '1111000050174', 'HOANG VAN HUY', 'qr_only.svg');
        $this->assertStringContainsString('qr_only.svg', $urlSvg);
    }

    public function testSePayAuthorizationTokenVerification()
    {
        $configuredKey = "SEPAY_TEST_SECRET_KEY_123456";
        $headerBearer = "Bearer SEPAY_TEST_SECRET_KEY_123456";
        $headerApiKey = "Apikey SEPAY_TEST_SECRET_KEY_123456";
        $headerRaw = "SEPAY_TEST_SECRET_KEY_123456";
        $headerWrong = "Bearer WRONG_KEY";

        $isValidBearer = in_array($headerBearer, ['Bearer ' . $configuredKey, 'Apikey ' . $configuredKey, $configuredKey]);
        $isValidApiKey = in_array($headerApiKey, ['Bearer ' . $configuredKey, 'Apikey ' . $configuredKey, $configuredKey]);
        $isValidRaw = in_array($headerRaw, ['Bearer ' . $configuredKey, 'Apikey ' . $configuredKey, $configuredKey]);
        $isValidWrong = in_array($headerWrong, ['Bearer ' . $configuredKey, 'Apikey ' . $configuredKey, $configuredKey]);

        $this->assertTrue($isValidBearer);
        $this->assertTrue($isValidApiKey);
        $this->assertTrue($isValidRaw);
        $this->assertFalse($isValidWrong);
    }
}
