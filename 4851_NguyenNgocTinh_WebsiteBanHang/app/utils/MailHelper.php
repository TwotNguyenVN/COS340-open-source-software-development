<?php
class MailHelper {
    public static function sendOtp($toEmail, $otp) {
        // Mock gửi mail cho môi trường dev. Bạn có thể thay bằng thư viện PHPMailer sau này.
        $logFile = __DIR__ . '/../../otp_log.txt';
        $message = "[" . date('Y-m-d H:i:s') . "] OTP for $toEmail is $otp\n";
        file_put_contents($logFile, $message, FILE_APPEND);
        error_log("OTP for $toEmail is $otp");
        return true; 
    }
}
?>
