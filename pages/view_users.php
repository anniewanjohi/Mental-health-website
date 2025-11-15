<?php
session_start();
require_once '../Backend/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: pages/login.php');
    exit();
}

// Connect to the database
$db = Database::getInstance();
$conn = $db->getConnection();

$success = '';
$error = '';

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$_GET['delete']]);
        $success = "User deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting user: " . $e->getMessage();
    }
}

// Fetch all users
try {
    $query = "SELECT * FROM users ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
    $users = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="styles/view_users.css">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="card shadow-lg border-0 rounded-4">
    <div class="card-body p-4 p-md-5">
      <h2 class="text-center text-primary fw-bold mb-4">
        <i class="bi bi-people-fill me-2"></i>Registered Users
      </h2>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-4">
        <a href="pages/register.php" class="btn btn-primary btn-lg px-4 rounded-pill shadow-sm w-100 w-md-auto">
          <i class="bi bi-plus-circle me-2"></i>Add New User
        </a>
        <a href="admin_dashboard.php" class="btn btn-secondary btn-lg px-4 rounded-pill shadow-sm w-100 w-md-auto">
          <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
      </div>

      <?php if (count($users) > 0): ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-primary">
              <tr>
                <th class="py-3">ID</th>
                <th class="py-3">Full Name</th>
                <th class="py-3">Email</th>
                <th class="py-3">Role</th>
                <th class="py-3">Created</th>
                <th class="py-3 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): ?>
                <tr>
                  <td class="fw-semibold"><?= htmlspecialchars($user['user_id']); ?></td>
                  <td>
                    <i class="bi bi-person-circle me-2 text-primary fs-5"></i>
                    <span class="fw-medium"><?= htmlspecialchars($user['fullname']); ?></span>
                  </td>
                  <td class="text-muted"><?= htmlspecialchars($user['email']); ?></td>
                  <td>
                    <?php
                      $roleClasses = [
                        'patient' => 'bg-info',
                        'mentor' => 'bg-primary',
                        'admin' => 'bg-danger'
                      ];
                      $roleClass = $roleClasses[strtolower($user['role'])] ?? 'bg-secondary';
                    ?>
                    <span class="badge <?= $roleClass ?> px-3 py-2 rounded-pill">
                      <?= ucfirst($user['role']); ?>
                    </span>
                  </td>
                  <td><?= date('M d, Y', strtotime($user['created_at'])); ?></td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <button class="btn btn-sm btn-outline-primary" onclick="alert('Edit user ID: <?= $user['user_id']; ?>')">
                        <i class="bi bi-pencil-square"></i> Edit
                      </button>
                      <a href="?delete=<?= $user['user_id']; ?>" 
                         class="btn btn-sm btn-outline-danger" 
                         onclick="return confirmDelete()">
                        <i class="bi bi-trash"></i> Delete
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="text-center py-5">
          <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
          <p class="text-muted mt-3 fs-5">No users found.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function confirmDelete() {
    return confirm("Are you sure you want to delete this user?");
  }
</script>

</body>
</html>