<?php
require 'app/core/Database.php';

try {
    $db = App\Core\Database::getInstance()->getConnection();
    
    $tables = ['resena'];
    
    foreach ($tables as $t) {
        $stmt = $db->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = '$t'");
        echo "=== $t ===\n";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
