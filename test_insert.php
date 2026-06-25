<?php
require_once __DIR__ . '/app/core/Database.php';
use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'alojamiento_politica'");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
