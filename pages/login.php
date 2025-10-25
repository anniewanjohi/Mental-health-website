<?php 
require_once '../Layout/header.php'; 
require_once '../Database/database.php'; 
require '../vendor/autoload.php';  
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start(); 

$db = Database::getInstance(); 
$conn = $db->getConnection(); 
$message = ""; 

$config = require '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $email = trim($_POST['email']); 
    $password = trim($_POST['password']); 

    if (!empty($email) && !empty($password)) { 
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1"); 
        $stmt->bindParam(':email', $email); 
        $stmt->execute(); 

        if ($stmt->rowCount() > 0) { 
            $user = $stmt->fetch(PDO::FETCH_ASSOC); 

            if (password_verify($password, $user['password'])) { 
                $otp = rand(100000, 999999); 
                $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes")); 

                $updateStmt = $conn->prepare("UPDATE users SET otp_code = :otp, otp_expires = :expiry WHERE user_id = :id"); 
                $updateStmt->execute([ 
                    ':otp' => $otp, 
                    ':expiry' => $expiry, 
                    ':id' => $user['user_id'] 
                ]); 

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $config['email'];   
                    $mail->Password   = $config['password']; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom($config['email'], 'HealthSite');
                    $mail->addAddress($user['email']);

                    $mail->isHTML(true);
                    $mail->Subject = 'Your HealthSite Login OTP Code';
                    $mail->Body    = "Dear {$user['fullname']},<br><br>Your OTP code is: <b>$otp</b><br>It will expire in 5 minutes.<br><br>- HealthSite Team";
                    $mail->AltBody = "Your OTP code is: $otp (expires in 5 minutes)";

                    $mail->send();

                    $_SESSION['pending_email'] = $user['email'];
                    $message = "<p style='color:green;'>✅ OTP sent to your email. Please verify.</p>";
                    header("refresh:2;url=verify_otp.php");
                } catch (Exception $e) {
                    $message = "<p style='color:red;'>❌ Failed to send OTP. Mailer Error: {$mail->ErrorInfo}</p>";
                }

            } else { 
                $message = "<p style='color:red;'>❌ Incorrect password.</p>"; 
            } 
        } else { 
            $message = "<p style='color:red;'>❌ No user found with that email.</p>"; 
        } 
    } else { 
        $message = "<p style='color:red;'>⚠️ Please fill in all fields.</p>"; 
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

  .login-container { 
    width: 100%; 
    max-width: 500px; 
    padding: 40px; 
  } 

  .login-card { 
    background: var(--ui-01); 
    border-radius: 20px; 
    padding: 50px 40px; 
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25); 
  } 

  .login-header { 
    text-align: center; 
    margin-bottom: 40px; 
  } 

  .login-icon { 
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

  .form-group { 
    margin-bottom: 25px; 
  } 

  .form-label { 
    font-weight: 600; 
    color: var(--brand-01); 
    display: block; 
    margin-bottom: 8px; 
  } 

  .form-control { 
    width: 100%; 
    padding: 15px 18px; 
    border: 2px solid var(--ui-02); 
    border-radius: 10px; 
    font-size: 1rem; 
    background-color: var(--ui-02); 
    transition: all 0.3s ease; 
  } 

  .form-control:focus { 
    border-color: var(--brand-03); 
    background-color: var(--ui-01); 
    box-shadow: 0 0 0 3px rgba(65, 214, 195, 0.15); 
    outline: none; 
  } 

  .btn-login { 
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

  .btn-login:hover { 
    background: linear-gradient(135deg, var(--brand-02), var(--brand-01)); 
    transform: translateY(-2px); 
    box-shadow: 0 6px 18px rgba(65, 214, 195, 0.3); 
  } 

  .register-link { 
    text-align: center; 
    color: var(--ui-05); 
    font-size: 0.95rem; 
    margin-top: 25px; 
  } 

  .register-link a { 
    color: var(--brand-01); 
    font-weight: 600; 
    text-decoration: none; 
  } 

  .register-link a:hover { 
    color: var(--brand-03); 
    text-decoration: underline; 
  } 

  .message { 
    text-align: center; 
    margin-bottom: 15px; 
  } 
</style> 

<div class="login-container"> 
  <div class="login-card"> 
    <div class="login-header"> 
      <div class="login-icon">🔐</div> 
      <h2 class="page-title">Welcome Back</h2> 
      <p class="page-subtitle">Log in to access your account</p> 
    </div> 

    <div class="message"><?= $message ?></div> 

    <form method="POST" action=""> 
      <div class="form-group"> 
        <label class="form-label">Email Address</label> 
        <input type="email" name="email" class="form-control" placeholder="your.email@example.com" required> 
      </div> 

      <div class="form-group"> 
        <label class="form-label">Password</label> 
        <input type="password" name="password" class="form-control" placeholder="Enter your password" required> 
      </div> 

      <button type="submit" class="btn-login">Login</button> 
    </form> 

    <p class="register-link"> 
      Don’t have an account? <a href="register.php">Create one</a> 
    </p> 
  </div> 
</div> 

<?php require_once '../Layout/footer.php'; ?>
