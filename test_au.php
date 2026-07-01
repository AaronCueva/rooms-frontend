<?php
require 'app/core/Database.php';
try {
    $db = App\Core\Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM alojamiento_universidad LIMIT 1");
    print_r($stmt->fetchAll());
    echo "OK\n";
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
