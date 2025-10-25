<?php
require_once 'Backend/database.php';


$db = Database::getInstance();
$conn = $db->getConnection();


if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    
    if (empty($fullname) || empty($email) || empty($password)) {
        echo "<p style='color:red;'>❌ Please fill in all fields.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>❌ Invalid email format.</p>";
    } elseif (strlen($password) < 8) {
        echo "<p style='color:red;'>❌ Password must be at least 8 characters long.</p>";
    } else {
        try {
            
            $checkStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $checkStmt->execute([$email]);

            if ($checkStmt->rowCount() > 0) {
                echo "<p style='color:red;'>❌ Email already registered. Try logging in.</p>";
            } else {
                // ✅ Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // ✅ Insert new user
                $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$fullname, $email, $hashedPassword]);

                echo "<p style='color:green;'>✅ Registration successful! You can now <a href='login.php'>login</a>.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
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
    background: linear-gradient(135deg, var(--brand-03) 0%, var(--brand-02) 50%, var(--brand-01) 100%);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .register-container {
    max-width: 520px;
    width: 100%;
  }

  .register-card {
    background: var(--ui-01);
    border-radius: 20px;
    padding: 50px 40px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  }

  .register-header {
    text-align: center;
    margin-bottom: 40px;
  }

  .register-icon {
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
    margin: 0 0 10px 0;
  }

  .page-subtitle {
    color: var(--ui-05);
    font-size: 0.95rem;
  }

  .form-group {
    margin-bottom: 25px;
  }

  .form-label {
    display: block;
    font-weight: 600;
    color: var(--brand-01);
    margin-bottom: 10px;
    font-size: 0.95rem;
  }

  .form-control {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid var(--ui-02);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background-color: var(--ui-02);
  }

  .form-control:focus {
    outline: none;
    border-color: var(--brand-03);
    background-color: var(--ui-01);
    box-shadow: 0 0 0 4px rgba(65, 214, 195, 0.1);
  }

  .btn-register {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, var(--brand-03), var(--brand-02));
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
  }

  .btn-register:hover {
    background: linear-gradient(135deg, var(--brand-02), var(--brand-01));
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(65, 214, 195, 0.4);
  }

  .divider {
    text-align: center;
    margin: 30px 0;
    color: var(--ui-05);
    font-size: 0.9rem;
  }

  .login-link {
    text-align: center;
    color: var(--ui-05);
    font-size: 0.95rem;
  }

  .login-link a {
    color: var(--brand-01);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
  }

  .login-link a:hover {
    color: var(--brand-03);
    text-decoration: underline;
  }

  .password-requirements {
    background-color: var(--ui-02);
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.85rem;
    color: var(--ui-05);
  }

  .password-requirements ul {
    margin: 5px 0 0 0;
    padding-left: 20px;
  }

  .password-requirements li {
    margin-bottom: 3px;
  }

  .message {
    text-align: center;
    margin-top: 20px;
    font-weight: 600;
    color: var(--brand-01);
  }

  @media (max-width: 768px) {
    .register-card {
      padding: 35px 25px;
    }

    .page-title {
      font-size: 1.75rem;
    }
  }
</style>

<div class="register-container">
  <div class="register-card">
    <div class="register-header">
      <div class="register-icon">✨</div>
      <h2 class="page-title">Create Account</h2>
      <p class="page-subtitle">Join us for professional mental health support</p>
    </div>

    <form method="POST" action="">
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" name="fullname" class="form-control" placeholder="Enter your full name" required>
      </div>

      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="your.email@example.com" required>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
      </div>

      <div class="password-requirements">
        <strong>Password should contain:</strong>
        <ul>
          <li>At least 8 characters</li>
          <li>One uppercase and one lowercase letter</li>
          <li>At least one number</li>
        </ul>
      </div>

      <button type="submit" name="register" class="btn-register">Create Account</button>
    </form>

    <div class="message"><?= $message ?></div>

    <div class="divider">───────  or  ───────</div>

    <p class="login-link">
      Already have an account? <a href="login.php">Login here</a>
    </p>
  </div>
</div>

<?php require_once '../Layout/footer.php'; ?>
