<?php
require_once '../Backend/Database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

$message = '';
$messageType = '';

if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($fullname) || empty($email) || empty($password) || empty($role)) {
        $message = "❌ Please fill in all fields.";
        $messageType = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format.";
        $messageType = "danger";
    } elseif (strlen($password) < 8) {
        $message = "❌ Password must be at least 8 characters long.";
        $messageType = "danger";
    } else {
        try {
            
            $checkStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $checkStmt->execute([$email]);

            if ($checkStmt->rowCount() > 0) {
                $message = "❌ Email already registered. Try logging in.";
                $messageType = "danger";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user including role
                $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$fullname, $email, $hashedPassword, $role]);

                $message = "✅ Registration successful! You can now <a href='login.php' class='alert-link'>login</a>.";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            $message = "❌ Database error: " . htmlspecialchars($e->getMessage());
            $messageType = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Mental Health Support</title>
    
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
                
                <!-- Register Card -->
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-4 p-md-5">
                        
                        
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-person-plus fs-1"></i>
                            </div>
                            <h2 class="fw-bold text-primary mb-2">Create Account</h2>
                            <p class="text-muted mb-0">Join us for professional mental health support</p>
                        </div>

                        <!-- Message Alert -->
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                                <?= $message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Registration Form -->
                        <form method="POST" action="">
                            
                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="fullname" class="form-label fw-semibold text-primary">
                                    <i class="bi bi-person me-2"></i>Full Name
                                </label>
                                <input type="text" name="fullname" id="fullname" class="form-control form-control-lg" placeholder="Enter your full name" required>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-primary">
                                    <i class="bi bi-envelope me-2"></i>Email Address
                                </label>
                                <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="your.email@example.com" required>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold text-primary">
                                    <i class="bi bi-lock me-2"></i>Password
                                </label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Create a strong password" required>
                            </div>

                            <!-- Role -->
                            <div class="mb-4">
                                <label for="role" class="form-label fw-semibold text-primary">
                                    <i class="bi bi-person-badge me-2"></i>Role
                                </label>
                                <select name="role" id="role" class="form-select form-select-lg" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="patient">Patient</option>
                                    <option value="mentor">Mentor</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <!-- Password Requirements -->
                            <div class="alert alert-info border-0 mb-4">
                                <h6 class="alert-heading fw-bold mb-2">
                                    <i class="bi bi-info-circle me-2"></i>Password Requirements:
                                </h6>
                                <ul class="mb-0 small">
                                    <li>At least 8 characters</li>
                                    <li>One uppercase and one lowercase letter</li>
                                    <li>At least one number</li>
                                </ul>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" name="register" class="btn btn-primary btn-lg w-100 mb-3 fw-semibold">
                                <i class="bi bi-check-circle me-2"></i>Create Account
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="text-center text-muted my-3">
                            <small>─────── or ───────</small>
                        </div>

                        <!-- Login Link -->
                        <p class="text-center text-muted mb-0">
                            Already have an account? 
                            <a href="login.php" class="text-primary fw-semibold text-decoration-none">
                                Login here
                            </a>
                        </p>
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
