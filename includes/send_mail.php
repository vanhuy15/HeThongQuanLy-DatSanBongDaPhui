<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

function sendEmail($to, $subject, $content, $bcc = null) {
    // Tự động nạp .env nếu chưa được nạp
    if (getenv('MAIL_USERNAME') === false && file_exists(__DIR__ . '/../.env')) {
        if (!class_exists('\\Core\\DotEnv')) {
            require_once __DIR__ . '/../core/DotEnv.php';
        }
        (new \Core\DotEnv(__DIR__ . '/../.env'))->load();
    }

    $mail = new PHPMailer(true);

    try {
        $mailHost = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
        $mailUser = getenv('MAIL_USERNAME') ?: '';
        $mailPass = getenv('MAIL_PASSWORD') ?: '';
        $mailPort = intval(getenv('MAIL_PORT')) ?: 587;

        if (empty($mailUser) || empty($mailPass) || strpos($mailUser, 'your_email') !== false || strpos($mailPass, 'your_app_password') !== false) {
            error_log("PHPMailer Warning: SMTP credentials in .env are not configured! (MAIL_USERNAME/MAIL_PASSWORD)");
            return false;
        }

        // 1. Cấu hình Server từ biến môi trường
        $mail->isSMTP();                                            
        $mail->Host       = $mailHost;                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = $mailUser;
        $mail->Password   = $mailPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $mailPort;
        $mail->Timeout    = 2; // Giảm timeout xuống 2s để phản hồi tức thì
        $mail->SMTPAutoTLS = true;

        // 2. Cấu hình người gửi/nhận
        $fromEmail = getenv('MAIL_FROM') ?: (getenv('MAIL_USERNAME') ?: 'no-reply@quanlysanbong.com');
        $fromName  = getenv('MAIL_FROM_NAME') ?: 'He Thong Dat San Bong';
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);

        if (!empty($bcc) && filter_var($bcc, FILTER_VALIDATE_EMAIL) && $bcc !== $to) {
            $mail->addBCC($bcc);
        }

        // 3. Nội dung
        $mail->isHTML(true);                                  
        $mail->Subject = $subject;
        $mail->Body    = $content;
        $mail->CharSet = 'UTF-8';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending error: " . $mail->ErrorInfo);
        return false;
    }
}
?>