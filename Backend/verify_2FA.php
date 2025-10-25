<?php 
require_once '../includes/header.php'; 
require_once '../Backend/database.php'; 

session_start(); 

// Check if user came from login page
if (!isset($_SESSION['pending_email'])) {
    header("Location: login.php");
    exit();
}

$db = Database::getInstance(); 
$conn = $db->getConnection(); 
$message = ""; 

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
                    
                    $message = "<p style='color:green;'>✅ Login successful! Redirecting...</p>";
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = '../index.php';
                        }, 1500);
                    </script>";
                } else {
                    $message = "<p style='color:red;'>❌ OTP has expired. Please login again.</p>";
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 3000);
                    </script>";
                }
            } else {
                $message = "<p style='color:red;'>❌ Invalid OTP. Please try again.</p>"; 
            }
        } else { 
            $message = "<p style='color:red;'>❌ Session expired. Please login again.</p>"; 
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 2000);
            </script>";
        } 
    } else { 
        $message = "<p style='color:red;'>⚠ Please enter the OTP code.</p>"; 
    } 
} 
?> 

<style> 
  :root { 
    --brand-01: #3d70b2; 
    --brand-02: #5596e6; 
    --brand-03: #41d6c3; 
    --ui-01: #ffffff; 
    --ui-02: #f4f7fb; 
    --ui-05: #5a6872; 
  } 

  body { 
    background: linear-gradient(135deg, var(--brand-03), var(--brand-02), var(--brand-01)); 
    font-family: 'Segoe UI', Roboto, sans-serif; 
    min-height: 100vh; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    margin: 0;
    padding: 0;
  } 

  .verify-container { 
    width: 100%; 
    max-width: 500px; 
    padding: 40px; 
  } 

  .verify-card { 
    background: var(--ui-01); 
    border-radius: 20px; 
    padding: 50px 40px; 
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25); 
  } 

  .verify-header { 
    text-align: center; 
    margin-bottom: 40px; 
  } 

  .verify-icon { 
    width: 80px; 
    height: 80px; 
    background: linear-gradient(135deg, var(--brand-03), var(--brand-02)); 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    margin: 0 auto 20px; 
    font-size: 2.5rem; 
  } 

  .page-title { 
    color: var(--brand-01); 
    font-size: 2rem; 
    font-weight: 700; 
  } 

  .page-subtitle { 
    color: var(--ui-05); 
    font-size: 1rem; 
    margin-top: 5px; 
  } 

  .email-display {
    background: var(--ui-02);
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    color: var(--brand-01);
    font-weight: 600;
    margin: 15px 0;
    font-size: 0.95rem;
  }

  .form-group { 
    margin-bottom: 25px; 
  } 

  .form-label { 
    font-weight: 600; 
    color: var(--brand-01); 
    display: block; 
    margin-bottom: 8px; 
    text-align: center;
  } 

  .otp-input { 
    width: 100%; 
    padding: 20px 18px; 
    border: 2px solid var(--ui-02); 
    border-radius: 10px; 
    font-size: 1.5rem; 
    background-color: var(--ui-02); 
    transition: all 0.3s ease;
    text-align: center;
    letter-spacing: 8px;
    font-weight: 700;
  } 

  .otp-input:focus { 
    border-color: var(--brand-03); 
    background-color: var(--ui-01); 
    box-shadow: 0 0 0 3px rgba(65, 214, 195, 0.15); 
    outline: none; 
  } 

  .btn-verify { 
    width: 100%; 
    padding: 15px; 
    background: linear-gradient(135deg, var(--brand-03), var(--brand-02)); 
    color: white; 
    border: none; 
    border-radius: 10px; 
    font-size: 1.1rem; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.3s ease; 
  } 

  .btn-verify:hover { 
    background: linear-gradient(135deg, var(--brand-02), var(--brand-01)); 
    transform: translateY(-2px); 
    box-shadow: 0 6px 18px rgba(65, 214, 195, 0.3); 
  } 

  .back-link { 
    text-align: center; 
    color: var(--ui-05); 
    font-size: 0.95rem; 
    margin-top: 25px; 
  } 

  .back-link a { 
    color: var(--brand-01); 
    font-weight: 600; 
    text-decoration: none; 
  } 

  .back-link a:hover { 
    color: var(--brand-03); 
    text-decoration: underline; 
  } 

  .message { 
    text-align: center; 
    margin-bottom: 15px;
    font-size: 0.95rem;
  }

  .info-box {
    background: #e3f2fd;
    border-left: 4px solid var(--brand-02);
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
    font-size: 0.9rem;
    color: #1565c0;
  }
</style> 

<div class="verify-container"> 
  <div class="verify-card"> 
    <div class="verify-header"> 
      <div class="verify-icon">📧</div> 
      <h2 class="page-title">Verify Your OTP</h2> 
      <p class="page-subtitle">Enter the code sent to your email</p>
    </div> 

    <div class="email-display">
      <?= htmlspecialchars($_SESSION['pending_email']) ?>
    </div>

    <div class="info-box">
      ⏱ Your OTP code will expire in 5 minutes
    </div>

    <div class="message"><?= $message ?></div> 

    <form method="POST" action=""> 
      <div class="form-group"> 
        <label class="form-label">Enter 6-Digit OTP Code</label> 
        <input 
          type="text" 
          name="otp" 
          class="otp-input" 
          placeholder="000000" 
          maxlength="6" 
          pattern="[0-9]{6}"
          inputmode="numeric"
          required
          autofocus
        > 
      </div> 

      <button type="submit" class="btn-verify">Verify & Login</button> 
    </form> 

    <p class="back-link"> 
      Didn't receive the code? <a href="login.php">Try again</a> 
    </p> 
  </div> 
</div> 

<?php require_once '../includes/footer.php'; ?>