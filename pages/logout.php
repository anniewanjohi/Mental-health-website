<?php
session_start();

$userName = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'User';

$_SESSION = array();
// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
// Destroy the session
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out - Mental Health Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-primary bg-gradient min-vh-100 d-flex align-items-center justify-content-center">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <!-- Main Logout Card -->
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-5 text-center">
                        <!-- Logout Icon -->
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 100px; height: 100px;">
                            <i class="bi bi-box-arrow-right display-4"></i>
                        </div>
                        
                        <!-- Success Message -->
                        <h2 class="fw-bold text-dark mb-3">Successfully Logged Out!</h2>
                        <p class="text-muted fs-5 mb-4">
                            Thank you for using our platform, 
                            <span class="fw-semibold text-primary"><?= htmlspecialchars($userName) ?></span>. 
                            You have been safely logged out.
                        </p>
                        
                        <!-- Action Buttons -->
                        <div class="d-grid gap-3 mb-4">
                            <a href="login.php" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login Again
                            </a>
                            <a href="../index.php" class="btn btn-outline-secondary btn-lg rounded-pill">
                                <i class="bi bi-house me-2"></i>Go to Homepage
                            </a>
                        </div>
                        
                        <!-- Countdown Alert -->
                        <div class="alert alert-light border mb-0">
                            <small class="text-muted d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock-history me-2"></i>
                                Redirecting to login in 
                                <span class="badge bg-primary fw-bold mx-2 fs-6" id="countdown">5</span> 
                                seconds...
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-white mb-2">
                        <i class="bi bi-shield-check me-2"></i>
                        <small>Your session has been securely terminated</small>
                    </p>
                    <p class="text-white-50 mb-0">
                        <small>&copy; 2025 Mental Health Platform. All rights reserved.</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Countdown Script -->
    <script>
        let seconds = 5;
        const countdownElement = document.getElementById('countdown');
        
        const countdown = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = 'login.php';
            }
        }, 1000);
    </script>
</body>
</html>