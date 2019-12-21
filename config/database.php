<?php

    class Database {
    
        // specify your own database credentials
        const HOST = 'localhost';
        const DATABASE = "sige";
        const USER = "root";
        const PASSWORD = null;
        public $conn;
    
        // get the database connection
        public function getConnection() {
    
            $this->conn = null;
    
            try {
                $this->conn = new PDO("mysql:host=" . self::HOST . ";dbname=" . self::DATABASE, self::USER, self::PASSWORD);
                $this->conn->exec("set names utf8");
            } catch(PDOException $exception) {
                echo "Connection error: " . $exception->getMessage();
            }
    
            return $this->conn;
        }
    }
?>