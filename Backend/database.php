<?php


class Database {
    private static $instance = null;
    private $connection;
    
    // Database configuration
    private $host = 'localhost';
    private $db_name = 'healthsite_db'; 
    private $username = 'root';          
    private $password = 'Annie123';               
    private $charset = 'utf8mb4';
    
    public function __construct() {
    try {
        $dsn = "mysql:host={$this->host};port=3307;dbname={$this->db_name};charset={$this->charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
        ];

        $this->connection = new PDO($dsn, $this->username, $this->password, $options);

        // ✅ Temporary success message
        //echo "<h2 style='color:green;'>✅ Database connection successful!</h2>";

    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        die("<h2 style='color:red;'>❌ Database connection failed: " . $e->getMessage() . "</h2>");
    }
}

    
    
   
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get PDO connection object
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->connection = null;
    }
    
    /**
     * Test database connection
     * @return bool
     */
    public function testConnection() {
        try {
            $this->connection->query("SELECT 1");
            return true;
        } catch(PDOException $e) {
            error_log("Connection test failed: " . $e->getMessage());
            return false;
        }
    }
}
?>