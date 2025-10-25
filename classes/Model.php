<?php
// FILE: classes/Model.php

class Model {
    // The property to hold the active PDO database connection
    private $pdo;

    // The constructor requires a PDO object to be passed in
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Reads all records from the 'users' table.
     * This directly addresses the requirement to display all users.
     * @return array An array of associative arrays containing all user data.
     */
    public function getAllUsers(): array {
        // SQL command to select all fields from the users table
        $sql = "SELECT id, full_name, email, created_at FROM users ORDER BY created_at DESC";
        
        // Prepare the statement for secure execution
        $stmt = $this->pdo->prepare($sql);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch all results, returning an empty array if none are found
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>