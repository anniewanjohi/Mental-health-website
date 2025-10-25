<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    
    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
            display: block; 
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <h2>Send an Anonymous Message</h2>
    <p>Share what's on your mind. A professional will review it.</p>
    
    <form id="contactForm" action="process_submission.php" method="POST" novalidate>
        
        <div>
            <label for="message">Your Message:</label><br>
            
            <textarea id="message" name="message" rows="8" cols="50"></textarea>
            
            <span id="messageError" class="error-message"></span>
        </div>
        <br>
        <button type="submit">Submit Anonymously</button>
    </form>

    <script src="validate.js"></script>

</body>
</html>