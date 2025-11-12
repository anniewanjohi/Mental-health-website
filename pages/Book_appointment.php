<?php 
require_once '../includes/header.php'; 
require_once '../Backend/database.php'; 

session_start(); 


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = Database::getInstance(); 
$conn = $db->getConnection(); 
$message = ""; 
$messageType = "";

$patient_id = $_SESSION['user_id'];
$user_name = $_SESSION['fullname'] ?? 'User';


$mentorsStmt = $conn->prepare("
    SELECT m.*, u.fullname, u.email 
    FROM mentors m
    JOIN users u ON m.user_id = u.user_id
    WHERE u.role = 'mentor'
    ORDER BY u.fullname ASC
");
$mentorsStmt->execute();
$mentors = $mentorsStmt->fetchAll(PDO::FETCH_ASSOC);


$appointmentsStmt = $conn->prepare("
    SELECT a.*, u.fullname as mentor_name, u.email as mentor_email, m.specialization
    FROM appointments a
    JOIN mentors m ON a.mentor_id = m.mentor_id
    JOIN users u ON m.user_id = u.user_id
    WHERE a.patient_id = :patient_id AND DATE(a.appointment_date) >= CURDATE()
    ORDER BY a.appointment_date ASC
    LIMIT 10
");
$appointmentsStmt->execute([':patient_id' => $patient_id]);
$upcoming_appointments = $appointmentsStmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_appointment'])) {
    $mentor_id = $_POST['mentor_id'] ?? '';
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';
    $session_type = $_POST['session_type'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if (!empty($mentor_id) && !empty($appointment_date) && !empty($appointment_time)) {
        $full_datetime = $appointment_date . ' ' . $appointment_time;
        
        $checkStmt = $conn->prepare("
            SELECT * FROM appointments 
            WHERE mentor_id = :mentor_id 
            AND appointment_date = :datetime
            AND status != 'cancelled'
        ");
        $checkStmt->execute([
            ':mentor_id' => $mentor_id,
            ':datetime' => $full_datetime
        ]);

        if ($checkStmt->rowCount() == 0) {
            $full_notes = "Session Type: " . ucfirst($session_type) . "\n" . $notes;
            
            $insertStmt = $conn->prepare("
                INSERT INTO appointments (patient_id, mentor_id, appointment_date, status, notes)
                VALUES (:patient_id, :mentor_id, :datetime, 'pending', :notes)
            ");
            
            $success = $insertStmt->execute([
                ':patient_id' => $patient_id,
                ':mentor_id' => $mentor_id,
                ':datetime' => $full_datetime,
                ':notes' => $full_notes
            ]);

            if ($success) {
                $message = "✅ Appointment booked successfully! Your mentor will confirm shortly.";
                $messageType = "success";
                $appointmentsStmt->execute([':patient_id' => $patient_id]);
                $upcoming_appointments = $appointmentsStmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $message = "❌ Failed to book appointment. Please try again.";
                $messageType = "danger";
            }
        } else {
            $message = "⚠ This time slot is already booked. Please choose another time.";
            $messageType = "warning";
        }
    } else {
        $message = "⚠ Please fill in all required fields.";
        $messageType = "warning";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_appointment'])) {
    $appointment_id = $_POST['appointment_id'] ?? '';
    
    $cancelStmt = $conn->prepare("
        UPDATE appointments 
        SET status = 'cancelled' 
        WHERE appointment_id = :id AND patient_id = :patient_id
    ");
    
    if ($cancelStmt->execute([':id' => $appointment_id, ':patient_id' => $patient_id])) {
        $message = "✅ Appointment cancelled successfully.";
        $messageType = "success";
        $appointmentsStmt->execute([':patient_id' => $patient_id]);
        $upcoming_appointments = $appointmentsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Mental Health Support</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container my-4">
    <!-- Header -->
    <div class="bg-primary bg-gradient text-white rounded-4 p-4 p-md-5 mb-4 shadow">
        <h1 class="display-5 fw-bold mb-2">
            <i class="bi bi-calendar-check me-2"></i>Book Your Appointment
        </h1>
        <p class="lead mb-0">Schedule online or physical therapy sessions with certified mental health professionals</p>
    </div>

    <!-- Message Alert -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-<?= $messageType === 'success' ? 'check-circle' : ($messageType === 'warning' ? 'exclamation-triangle' : 'x-circle') ?> me-2"></i>
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Booking Form -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h4 fw-bold text-primary mb-0">
                        <i class="bi bi-calendar-plus me-2"></i>New Appointment
                    </h2>
                </div>
                <div class="card-body p-4">
                    <?php if (count($mentors) > 0): ?>
                    <form method="POST" action="" id="bookingForm">
                        <!-- Select Mentor -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary">
                                <i class="bi bi-person-heart me-2"></i>Select Mental Health Mentor *
                            </label>
                            <select name="mentor_id" class="form-select form-select-lg" required id="mentorSelect">
                                <option value="">Choose a mentor...</option>
                                <?php foreach ($mentors as $mentor): ?>
                                    <option value="<?= $mentor['mentor_id'] ?>">
                                        <?= htmlspecialchars($mentor['fullname']) ?> 
                                        <?php if (!empty($mentor['specialization'])): ?>
                                            - <?= htmlspecialchars($mentor['specialization']) ?>
                                        <?php endif; ?>
                                        <?php if (!empty($mentor['experience_years'])): ?>
                                            (<?= $mentor['experience_years'] ?> years exp.)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Session Type -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary">
                                <i class="bi bi-laptop me-2"></i>Session Type *
                            </label>
                            <select name="session_type" class="form-select form-select-lg" required>
                                <option value="">Select session type...</option>
                                <option value="online">💻 Online Session (Video Call)</option>
                                <option value="physical">🏥 Physical Session (In-Person)</option>
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary">
                                <i class="bi bi-calendar-event me-2"></i>Appointment Date *
                            </label>
                            <input type="date" name="appointment_date" class="form-control form-control-lg" 
                                   min="<?= date('Y-m-d') ?>" 
                                   max="<?= date('Y-m-d', strtotime('+60 days')) ?>"
                                   required>
                        </div>

                        <!-- Time -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary">
                                <i class="bi bi-clock me-2"></i>Preferred Time *
                            </label>
                            <select name="appointment_time" class="form-select form-select-lg" required>
                                <option value="">Select time slot...</option>
                                <option value="08:00:00">08:00 AM</option>
                                <option value="09:00:00">09:00 AM</option>
                                <option value="10:00:00">10:00 AM</option>
                                <option value="11:00:00">11:00 AM</option>
                                <option value="12:00:00">12:00 PM</option>
                                <option value="13:00:00">01:00 PM</option>
                                <option value="14:00:00">02:00 PM</option>
                                <option value="15:00:00">03:00 PM</option>
                                <option value="16:00:00">04:00 PM</option>
                                <option value="17:00:00">05:00 PM</option>
                                <option value="18:00:00">06:00 PM</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-primary">
                                <i class="bi bi-pencil me-2"></i>Additional Notes (Optional)
                            </label>
                            <textarea name="notes" class="form-control" rows="4"
                                      placeholder="Any specific concerns, topics you'd like to discuss, or questions for your mentor..."></textarea>
                        </div>

                        <button type="submit" name="book_appointment" class="btn btn-primary btn-lg w-100 fw-semibold">
                            <i class="bi bi-calendar-check me-2"></i>Book Appointment
                        </button>
                    </form>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-person-x display-1 text-muted opacity-25"></i>
                            <h3 class="mt-3 text-muted">No Mentors Available</h3>
                            <p class="text-muted">Please contact the administrator to add mentors to the system.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h4 fw-bold text-primary mb-0">
                        <i class="bi bi-list-check me-2"></i>Your Upcoming Appointments
                    </h2>
                </div>
                <div class="card-body p-4">
                    <?php if (count($upcoming_appointments) > 0): ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($upcoming_appointments as $apt): ?>
                                <div class="card border-start border-4 border-primary">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; min-width: 50px;">
                                                    <span class="fw-bold fs-5"><?= strtoupper(substr($apt['mentor_name'], 0, 1)) ?></span>
                                                </div>
                                                <div>
                                                    <h5 class="mb-1 fw-bold text-primary"><?= htmlspecialchars($apt['mentor_name']) ?></h5>
                                                    <?php if (!empty($apt['specialization'])): ?>
                                                        <p class="mb-0 small text-muted"><?= htmlspecialchars($apt['specialization']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <span class="badge bg-<?= $apt['status'] === 'pending' ? 'warning' : ($apt['status'] === 'confirmed' ? 'success' : ($apt['status'] === 'completed' ? 'primary' : 'danger')) ?> text-<?= $apt['status'] === 'pending' ? 'dark' : 'white' ?>">
                                                <?= ucfirst($apt['status']) ?>
                                            </span>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <div class="d-flex align-items-center gap-2 text-muted small">
                                                    <i class="bi bi-calendar-event"></i>
                                                    <strong><?= date('M d, Y', strtotime($apt['appointment_date'])) ?></strong>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex align-items-center gap-2 text-muted small">
                                                    <i class="bi bi-clock"></i>
                                                    <strong><?= date('h:i A', strtotime($apt['appointment_date'])) ?></strong>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (!empty($apt['notes'])): ?>
                                            <div class="alert alert-info border-0 border-start border-4 mb-3">
                                                <div class="small">
                                                    <strong><i class="bi bi-sticky me-1"></i>Notes:</strong><br>
                                                    <?= nl2br(htmlspecialchars($apt['notes'])) ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($apt['status'] == 'pending'): ?>
                                            <form method="POST" action="" 
                                                  onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                                <input type="hidden" name="appointment_id" value="<?= $apt['appointment_id'] ?>">
                                                <button type="submit" name="cancel_appointment" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-x-circle me-1"></i>Cancel Appointment
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x display-1 text-muted opacity-25"></i>
                            <h3 class="mt-3 text-muted">No Upcoming Appointments</h3>
                            <p class="text-muted">Book your first appointment to get started with your mental health journey</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Form validation
document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
    const date = document.querySelector('input[name="appointment_date"]').value;
    const time = document.querySelector('select[name="appointment_time"]').value;
    const mentor = document.querySelector('select[name="mentor_id"]').value;
    
    if (!date || !time || !mentor) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        return false;
    }
    
    
    const selectedDate = new Date(date + ' ' + time);
    const now = new Date();
    
    if (selectedDate < now) {
        e.preventDefault();
        alert('Please select a future date and time for your appointment.');
        return false;
    }
    
    return true;
});
</script>

<?php require_once '../Layout/footer.php'; ?>
</body>
</html>
