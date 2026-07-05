<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'staynest');

function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            // Return mock data if DB not connected (for demo purposes)
            return null;
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}
?>
