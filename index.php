<?php require_once 'includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mental Health Support - Home</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<!-- Hero Section -->
<div class="bg-primary bg-gradient text-white py-5 position-relative overflow-hidden" style="min-height: 500px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('data:image/svg+xml,<svg width=\'100\' height=\'100\' xmlns=\'http://www.w3.org/2000/svg\'><circle cx=\'50\' cy=\'50\' r=\'2\' fill=\'white\' opacity=\'0.1\'/></svg>'); opacity: 0.3;"></div>
    
    <div class="container position-relative" style="z-index: 1;">
        <div class="row justify-content-center text-center py-5">
            <div class="col-lg-8">
                <h1 class="display-2 fw-bold mb-4">Welcome to Mental Health Support</h1>
                <p class="lead fs-3 mb-5 fw-light">Professional mental health care at your fingertips</p>
                <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                    <a href="pages/register.php" class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-semibold shadow">
                        Get Started
                    </a>
                    <a href="pages/Articles.php" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3 fw-semibold">
                        Browse Articles
                    </a>
                    <a href="pages/book_appointment.php" class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-semibold shadow">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<div class="py-5 bg-body-secondary">
    <div class="container py-5">
        <h2 class="text-center display-5 fw-bold text-primary mb-5">Our Services</h2>
        
        <div class="row g-4">
            <!-- Book Appointments Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="card-body p-5">
                        <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="bi bi-calendar-check fs-1"></i>
                        </div>
                        <h3 class="card-title h4 fw-bold text-primary mb-3">Book Appointments</h3>
                        <p class="card-text text-muted mb-4">
                            Schedule online or physical therapy sessions with certified professionals. Get matched with the right therapist for your needs.
                        </p>
                        <a href="pages/book_appointment.php" class="btn btn-primary btn-lg rounded-pill px-4">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>

            <!-- Read Articles Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="card-body p-5">
                        <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="bi bi-book fs-1"></i>
                        </div>
                        <h3 class="card-title h4 fw-bold text-primary mb-3">Read Articles</h3>
                        <p class="card-text text-muted mb-4">
                            Access helpful resources and articles about mental health. Stay informed with expert insights and wellness tools.
                        </p>
                        <a href="pages/Articles.php" class="btn btn-primary btn-lg rounded-pill px-4">
                            Browse
                        </a>
                    </div>
                </div>
            </div>

            <!-- Secure & Private Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="card-body p-5">
                        <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="bi bi-shield-lock fs-1"></i>
                        </div>
                        <h3 class="card-title h4 fw-bold text-primary mb-3">Secure & Private</h3>
                        <p class="card-text text-muted mb-4">
                            Your data is protected with 2-factor authentication. We prioritize your privacy and confidentiality at every step.
                        </p>
                        <a href="pages/register.php" class="btn btn-primary btn-lg rounded-pill px-4">
                            Sign Up
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Why Choose Us Section -->
<div class="py-5 bg-white">
    <div class="container py-5">
        <h2 class="text-center display-5 fw-bold text-primary mb-5">Why Choose Us?</h2>
        
        <div class="row g-4">
            <!-- Expert Network -->
            <div class="col-sm-6 col-lg-3">
                <div class="text-center p-4 rounded-3 bg-light h-100">
                    <div class="mb-3">
                        <i class="bi bi-people-fill text-primary fs-1"></i>
                    </div>
                    <h4 class="h5 fw-bold text-primary mb-3">Expert Network</h4>
                    <p class="text-muted mb-0">Connect with licensed therapists and counselors across the nation</p>
                </div>
            </div>

            <!-- Flexible Options -->
            <div class="col-sm-6 col-lg-3">
                <div class="text-center p-4 rounded-3 bg-light h-100">
                    <div class="mb-3">
                        <i class="bi bi-toggles text-primary fs-1"></i>
                    </div>
                    <h4 class="h5 fw-bold text-primary mb-3">Flexible Options</h4>
                    <p class="text-muted mb-0">Choose between online sessions or in-person appointments</p>
                </div>
            </div>

            <!-- 24/7 Resources -->
            <div class="col-sm-6 col-lg-3">
                <div class="text-center p-4 rounded-3 bg-light h-100">
                    <div class="mb-3">
                        <i class="bi bi-clock-fill text-primary fs-1"></i>
                    </div>
                    <h4 class="h5 fw-bold text-primary mb-3">24/7 Resources</h4>
                    <p class="text-muted mb-0">Access mental health articles and tools anytime, anywhere</p>
                </div>
            </div>

            <!-- Confidential Care -->
            <div class="col-sm-6 col-lg-3">
                <div class="text-center p-4 rounded-3 bg-light h-100">
                    <div class="mb-3">
                        <i class="bi bi-lock-fill text-primary fs-1"></i>
                    </div>
                    <h4 class="h5 fw-bold text-primary mb-3">Confidential Care</h4>
                    <p class="text-muted mb-0">Your privacy is our top priority with secure, encrypted sessions</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>