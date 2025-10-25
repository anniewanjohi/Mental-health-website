<?php
// 1. Include the necessary files for the architecture and OOP classes
require_once '../includes/Database.php'; // Your Database connection class
require_once '../classes/Model.php';    // Your Model class with getAllUsers()
include('../includes/header.php');      // Reusable header/navigation

$users = [];
$error_message = '';

try {
    // 2. Establish PDO connection using the OOP Database class
    $db = new Database();
    $pdo = $db->getConnection();

    // 3. Instantiate the Model class with the connection
    $model = new Model($pdo);

    // 4. Call the OOP method to fetch all users
    $users = $model->getAllUsers();

} catch (PDOException $e) {
    // Catch database-related errors (e.g., table doesn't exist, bad credentials)
    $error_message = "Database Error: Could not retrieve users. Please ensure the 'users' table exists. Details: " . $e->getMessage();
} catch (Exception $e) {
    // Catch general errors
    $error_message = "An application error occurred: " . $e->getMessage();
}
?>

<div class="page-content">
    <h1>All Registered Users</h1>

    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php elseif (empty($users)): ?>
        <div class="alert alert-info" role="alert">
            No users have been registered in the system yet.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Date Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php
include('../includes/footer.php'); // Reusable footer
?>