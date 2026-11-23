<?php
namespace Services;

class VietQR {
    public const DEFAULT_BANK_ID = 'MB';
    public const DEFAULT_TEMPLATE = 'compact2';

    /**
     * Sinh link ảnh mã VietQR thanh toán tự động
     */
    public static function generateUrl(
        int $bookingId,
        int $amount,
        ?string $bankId = null,
        ?string $accountNo = null,
        ?string $accountName = null,
        ?string $template = null
    ): string {
        $bankId = $bankId ?: ($_ENV['VIETQR_BANK_ID'] ?? getenv('VIETQR_BANK_ID') ?: self::DEFAULT_BANK_ID);
        $accountNo = $accountNo ?: ($_ENV['VIETQR_ACCOUNT_NO'] ?? getenv('VIETQR_ACCOUNT_NO') ?: '000000000000');
        $accountName = $accountName ?: ($_ENV['VIETQR_ACCOUNT_NAME'] ?? getenv('VIETQR_ACCOUNT_NAME') ?: 'SAN BONG PITCH');
        $template = $template ?: ($_ENV['VIETQR_TEMPLATE'] ?? getenv('VIETQR_TEMPLATE') ?: self::DEFAULT_TEMPLATE);

        $addInfo = urlencode("DATSAN " . $bookingId);
        $encodedName = urlencode($accountName);

        return "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png"
            . "?amount={$amount}"
            . "&addInfo={$addInfo}"
            . "&accountName={$encodedName}";
    }
}

