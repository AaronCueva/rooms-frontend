<?php
require 'app/core/Database.php';
require 'app/models/UniversidadModel.php';

try {
    $u = new App\Models\UniversidadModel();
    $id = '01b4c9e4-8451-4d37-8339-ff9e7943d0f0'; // Note: This might not be a real ID, wait, let me just fetch one ID first
    $all = $u->getAll();
    if(count($all) > 0) {
        $id = $all[0]['universidad_id'];
        echo "Found ID: $id\n";
        $uni = $u->findById($id);
        var_dump($uni);
    } else {
        echo "No universities in DB\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
