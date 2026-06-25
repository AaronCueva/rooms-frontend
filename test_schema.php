<?php
require 'app/core/Database.php';

try {
    $db = App\Core\Database::getInstance()->getConnection();
    
    echo "=== COLUMNAS ALOJAMIENTO ===\n";
    $stmt = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'alojamiento'");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
    
    echo "\n=== COLUMNAS UNIVERSIDAD ===\n";
    $stmt2 = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'universidad'");
    print_r($stmt2->fetchAll(PDO::FETCH_COLUMN));

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
