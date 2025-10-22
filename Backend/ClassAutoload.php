<?php

spl_autoload_register(function ($class_name) {
    $paths = [
        __DIR__ . '/classes/' . $class_name . '.php',
        __DIR__ . '/interfaces/' . $class_name . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});


define('DB_HOST', 'localhost');
define('DB_NAME', 'mental_health_db');
define('DB_USER', 'root');
define('DB_PASS', '');


try {
    $database = new Database();   // Create Database instance
    $SQL = new SQLHelper($database->getConnection()); // Pass PDO to SQLHelper
} catch (Exception $e) {
    die("❌ Database Connection Failed: " . $e->getMessage());
}


if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
    session_start();
}


date_default_timezone_set('Africa/Nairobi');
?>
