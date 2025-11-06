<?php
class Database {
    private static $instance = null;
    private $connection;

    // ✅ Correct database configuration
    private $host = '127.0.0.1';
    private $port = 3306;
    private $db_name = 'mentalhealth_db';
    private $username = 'root';
    private $password = 'Annie123';
    private $charset = 'utf8mb4';

    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset={$this->charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);

            // Optional success confirmation (remove later in production)
            echo "<h3 style='color:green;'>✅ Connected to database successfully!</h3>";

        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            die("<h3 style='color:red;'>❌ Connection failed: " . $e->getMessage() . "</h3>");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
?>
