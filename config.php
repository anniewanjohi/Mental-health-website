<?php
// Mental Health Website Email Configuration
require_once __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendOTPEmail($recipientEmail, $recipientName, $otp) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = 0; // Set to 2 for debugging
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mental_health.team@gmail.com'; // Your Gmail
        $mail->Password = 'ykbx evhk ucog qala'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Remove SSL verification issues (if needed)
        $mail->SMTPOptions = array(
          
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Recipients
        $mail->setFrom('smartevent.reservation.team@gmail.com', 'Mental Health Support');
        $mail->addAddress($recipientEmail);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Login OTP Code - Mental Health Website';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #3d70b2;'>Mental Health Support - Login Verification</h2>
                <p>Dear $recipientName,</p>
                <p>Your One-Time Password (OTP) for login is:</p>
                <div style='background: linear-gradient(135deg, #41d6c3, #5596e6); padding: 20px; text-align: center; border-radius: 10px; margin: 20px 0;'>
                    <strong style='font-size: 32px; color: white; letter-spacing: 5px;'>$otp</strong>
                </div>
                <p>Enter this code on the verification page to complete your login.</p>
                <p><strong>This code expires in 5 minutes.</strong></p>
                <p style='color: #666; font-size: 12px; margin-top: 30px;'>
                    If you did not request this code, please ignore this email or contact support.
                </p>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='color: #999; font-size: 11px;'>
                    © 2025 Mental Health Support Website. All rights reserved.
                </p>
            </div>
        ";
        
        $mail->AltBody = "Your OTP Code: $otp - Enter this on the verification page. This code expires in 5 minutes.";
        
        if($mail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}

// Return config array for other uses
return [
    'email' => 'mental_health.team@gmail.com',
    'password' => 'ykbx evhk ucog qala',
    'app_name' => 'Mental Health Support'
];
?>