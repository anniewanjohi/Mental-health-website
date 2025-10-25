<?php
$errors = [];
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["message"]))) {
        $errors[] = "Message is required.";
    } else {
        
        $message = htmlspecialchars(trim($_POST["message"]));
    }

    if (empty($errors)) {
        echo "<h1>Thank You!</h1>";
        echo "<p>Your message has been submitted successfully.</p>";
        echo "<p><strong>Your (sanitized) message:</strong></g> <br>" . nl2br($message) . "</p>";
        echo "<a href='contact.php'>Go Back</a>";

    } else {
        echo "<h1>Error</h1>";
        echo "<p>Please correct the following issues:</p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "<a href='contact.php'>Try Again</a>";
    }

} else {
    header("Location: contact.php");
    exit;
}
?>