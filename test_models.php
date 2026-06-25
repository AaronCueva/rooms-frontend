<?php
require 'app/core/Database.php';
require 'app/models/Alojamiento.php';
require 'app/models/UniversidadModel.php';

try {
    $a = new App\Models\Alojamiento();
    $a->getAll();
    echo "Alojamiento OK\n";
} catch (Exception $e) {
    echo "Alojamiento Error: " . $e->getMessage() . "\n";
}

try {
    $u = new App\Models\UniversidadModel();
    $u->getAll();
    echo "Universidad OK\n";
} catch (Exception $e) {
    echo "Universidad Error: " . $e->getMessage() . "\n";
}
