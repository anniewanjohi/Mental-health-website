<?php


spl_autoload_register(function ($class_name) {
    
    // Array of directories where classes might be located
    $directories = [
        __DIR__ . '/../Database/',
        __DIR__ . '/../pages/',
        __DIR__ . '/../classes/',
    ];
    
    // Try to find and include the class file
    foreach ($directories as $directory) {
        $file = $directory . $class_name . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // If class not found, log error
    error_log("Class not found: {$class_name}");
    return false;
});


?>