<?php 
require_once '../includes/header.php'; 
require_once '../Backend/database.php'; 
require_once '../config.php'; // Load email function

session_start(); 

$db = Database::getInstance(); 
$conn = $db->getConnection(); 
$message = ""; 
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) { 
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1"); 
        $stmt->bindParam(':email', $email); 
        $stmt->execute(); 

        if ($stmt->rowCount() > 0) { 
            $user = $stmt->fetch(PDO::FETCH_ASSOC); 

            if (password_verify($password, $user['password'])) { 
                // Generate OTP
                $otp = rand(100000, 999999); 
                $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes")); 

                // Save OTP to database
                $updateStmt = $conn->prepare("UPDATE users SET otp_code = :otp, otp_expires = :expiry WHERE user_id = :id"); 
                $updateStmt->execute([ 
                    ':otp' => $otp, 
                    ':expiry' => $expiry, 
                    ':id' => $user['user_id'] 
                ]); 

                // Send OTP via email using the function from config.php
                $emailSent = sendOTPEmail($user['email'], $user['fullname'], $otp);

                if ($emailSent) {
                    $_SESSION['pending_email'] = $user['email'];
                    $_SESSION['pending_user_id'] = $user['user_id'];
                    $message = "✅ OTP sent to your email. Please verify.";
                    $messageType = "success";
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = 'verify_2FA.php';
                        }, 2000);
                    </script>";
                } else {
                    $message = "❌ Failed to send OTP. Please try again.";
                    $messageType = "danger";
                }

            } else { 
                $message = "❌ Incorrect password.";
                $messageType = "danger";
            } 
        } else { 
            $message = "❌ No user found with that email.";
            $messageType = "danger";
        } 
    } else { 
        $message = "⚠️ Please fill in all fields.";
        $messageType = "warning";
    } 
} 
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mental Health Support</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-primary bg-gradient">

<div class="min-vh-100 d-flex align-items-center justify-content-center p-3 p-md-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                
                <!-- Login Card -->
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-4 p-md-5">
                        
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-lock-fill fs-1"></i>
                            </div>
                            <h2 class="fw-bold text-primary mb-2">Welcome Back</h2>
                            <p class="text-muted mb-0">Log in to access your account</p>
                        </div>

                        
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                                <i class="bi bi-<?= $messageType === 'success' ? 'check-circle' : ($messageType === 'warning' ? 'exclamation-triangle' : 'x-circle') ?> me-2"></i>
                                <?= $message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form method="POST" action="">
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-primary">
                                    <i class="bi bi-envelope me-2"></i>Email Address
                                </label>
                                <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="your.email@example.com" required>
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold text-primary">
                                    <i class="bi bi-key me-2"></i>Password
                                </label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Enter your password" required>
                            </div>

                            <!-- Login Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3 fw-semibold">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </form>

                        <!-- Additional Links -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <a href="forgot_password.php" class="text-primary text-decoration-none small">
                                <i class="bi bi-question-circle me-1"></i>Forgot Password?
                            </a>
                            <a href="help.php" class="text-primary text-decoration-none small">
                                <i class="bi bi-info-circle me-1"></i>Need Help?
                            </a>
                        </div>

                        
                        <div class="text-center text-muted my-3">
                            <small>─────── or ───────</small>
                        </div>

                        <!-- Register Link -->
                        <p class="text-center text-muted mb-0">
                            Don't have an account? 
                            <a href="register.php" class="text-primary fw-semibold text-decoration-none">
                                Create one
                            </a>
                        </p>

                        <!-- Security Notice -->
                        <div class="alert alert-info border-0 mt-4 mb-0">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-shield-check fs-5 me-2 mt-1"></i>
                                <div class="small">
                                    <strong>Two-Factor Authentication Enabled</strong>
                                    <p class="mb-0">For your security, you'll receive a verification code via email after login.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>