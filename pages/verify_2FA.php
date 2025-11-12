<?php 
require_once '../includes/header.php'; 
require_once '../Backend/database.php'; 

session_start(); 


if (!isset($_SESSION['pending_email'])) {
    header("Location: login.php");
    exit();
}

$db = Database::getInstance(); 
$conn = $db->getConnection(); 
$message = ""; 
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $otp_input = trim($_POST['otp'] ?? '');
    $pending_email = $_SESSION['pending_email'];

    if (!empty($otp_input)) { 
        // Get user with OTP
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1"); 
        $stmt->bindParam(':email', $pending_email); 
        $stmt->execute(); 

        if ($stmt->rowCount() > 0) { 
            $user = $stmt->fetch(PDO::FETCH_ASSOC); 
            
            // Check if OTP is valid and not expired
            $current_time = date("Y-m-d H:i:s");
            
            if ($user['otp_code'] == $otp_input) {
                if ($user['otp_expires'] > $current_time) {
                    // OTP is valid - Log the user in
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = $user['role'] ?? 'user';
                    
                    // Clear OTP from database
                    $clearStmt = $conn->prepare("UPDATE users SET otp_code = NULL, otp_expires = NULL WHERE user_id = :id"); 
                    $clearStmt->execute([':id' => $user['user_id']]); 
                    
                    // Clear pending session
                    unset($_SESSION['pending_email']);
                    unset($_SESSION['pending_user_id']);
                    
                    $message = "✅ Login successful! Redirecting...";
                    $messageType = "success";
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = '../index.php';
                        }, 1500);
                    </script>";
                } else {
                    $message = "❌ OTP has expired. Please login again.";
                    $messageType = "danger";
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 3000);
                    </script>";
                }
            } else {
                $message = "❌ Invalid OTP. Please try again.";
                $messageType = "danger";
            }
        } else { 
            $message = "❌ Session expired. Please login again.";
            $messageType = "danger";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 2000);
            </script>";
        } 
    } else { 
        $message = "⚠️ Please enter the OTP code.";
        $messageType = "warning";
    } 
} 
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Mental Health Support</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        .otp-input {
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            text-align: center;
            font-weight: 700;
        }
        
        .otp-input::-webkit-outer-spin-button,
        .otp-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>
<body class="bg-primary bg-gradient">

<div class="min-vh-100 d-flex align-items-center justify-content-center p-3 p-md-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                
                <!-- Verify Card -->
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-4 p-md-5">
                        
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-envelope-check fs-1"></i>
                            </div>
                            <h2 class="fw-bold text-primary mb-2">Verify Your OTP</h2>
                            <p class="text-muted mb-0">Enter the code sent to your email</p>
                        </div>

                        <!-- Email Display -->
                        <div class="alert alert-primary border-0 text-center mb-3">
                            <i class="bi bi-envelope-at me-2"></i>
                            <strong><?= htmlspecialchars($_SESSION['pending_email']) ?></strong>
                        </div>

                        <!-- Info Box -->
                        <div class="alert alert-info border-0 border-start border-4 mb-4">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-clock-history fs-5 me-2 mt-1"></i>
                                <div class="small">
                                    <strong>Time Sensitive</strong>
                                    <p class="mb-0">Your OTP code will expire in 5 minutes</p>
                                </div>
                            </div>
                        </div>

                        <!-- Message Alert -->
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                                <i class="bi bi-<?= $messageType === 'success' ? 'check-circle' : ($messageType === 'warning' ? 'exclamation-triangle' : 'x-circle') ?> me-2"></i>
                                <?= $message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Verify Form -->
                        <form method="POST" action="">
                            
                            <!-- OTP Input -->
                            <div class="mb-4">
                                <label for="otp" class="form-label fw-semibold text-primary text-center d-block">
                                    <i class="bi bi-shield-lock me-2"></i>Enter 6-Digit OTP Code
                                </label>
                                <input 
                                    type="text" 
                                    name="otp" 
                                    id="otp"
                                    class="form-control form-control-lg otp-input" 
                                    placeholder="000000" 
                                    maxlength="6" 
                                    pattern="[0-9]{6}"
                                    inputmode="numeric"
                                    required
                                    autofocus
                                >
                                <div class="form-text text-center">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Enter the 6-digit code from your email
                                </div>
                            </div>

                            <!-- Verify Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3 fw-semibold">
                                <i class="bi bi-check-circle me-2"></i>Verify & Login
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="text-center text-muted my-3">
                            <small>─────── or ───────</small>
                        </div>

                        <!-- Back Link -->
                        <p class="text-center text-muted mb-0">
                            Didn't receive the code? 
                            <a href="login.php" class="text-primary fw-semibold text-decoration-none">
                                <i class="bi bi-arrow-clockwise me-1"></i>Try again
                            </a>
                        </p>

                        <!-- Security Notice -->
                        <div class="alert alert-light border mt-4 mb-0">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-shield-check text-success fs-5 me-2 mt-1"></i>
                                <div class="small text-muted">
                                    <strong>Security Tip:</strong> Never share your OTP code with anyone. Our team will never ask for this code.
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

<script>
// Auto-focus on OTP input
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp');
    
    // Only allow numbers
    otpInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    // Auto-submit when 6 digits are entered (optional)
    otpInput.addEventListener('input', function(e) {
        if (this.value.length === 6) {
            // You can auto-submit here if desired
            // this.form.submit();
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>