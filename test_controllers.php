<?php
session_start();
$_SESSION['nombres'] = 'Test';

require 'app/core/Database.php';

// Mock views loading by defining a base controller or just requiring the controller class
require 'app/models/Alojamiento.php';
require 'app/models/Catalogo.php';
require 'app/models/Favorito.php';
require 'app/models/PoliticaCasa.php';
require 'app/models/Servicio.php';
require 'app/models/AlojamientoServicio.php';
require 'app/models/AlojamientoPolitica.php';
require 'app/models/Descuento.php';
require 'app/models/Beneficio.php';
require 'app/models/Ubicacion.php';

// The controllers probably extend a base Controller which defines render().
// We just want to check if there are compilation errors or missing methods.

require 'app/controllers/AdminAlojamientoController.php';
require 'app/controllers/AdminUniversidadController.php';

echo "Controllers loaded.\n";
