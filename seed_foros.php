<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Obtener un usuario existente o usar un ID conocido
    $stmt = $db->query("SELECT usuario_id FROM usuario LIMIT 1");
    $user = $stmt->fetch();
    
    if (!$user) {
        // Insertar usuario de prueba
        $stmtInsert = $db->prepare("INSERT INTO usuario (usuario, password, nombres, apellido_paterno, correo, habilitado) 
                                    VALUES ('testuser', '123', 'Juan', 'Pérez', 'juan@test.com', true) RETURNING usuario_id");
        $stmtInsert->execute();
        $usuario_id = $stmtInsert->fetchColumn();
    } else {
        $usuario_id = $user['usuario_id'];
    }

    if (!$usuario_id) die("No se pudo obtener un usuario_id válido.\n");

    // 2. Insertar un foro
    $stmtForo = $db->prepare("INSERT INTO foro (titulo, descripcion, fecha_creacion, estado_codigo, usuario_id, habilitado) 
                              VALUES (:titulo, :descripcion, NOW(), 'ACT', :usuario_id, true) RETURNING foro_id");
    $stmtForo->execute([
        ':titulo' => '¿Cuál es el mejor alojamiento cerca de la universidad?',
        ':descripcion' => 'Hola a todos, soy de primer ciclo y me gustaría saber qué zonas recomiendan para alquilar un cuarto que sea seguro y económico.',
        ':usuario_id' => $usuario_id
    ]);
    
    $foro_id = $stmtForo->fetchColumn();
    
    if ($foro_id) {
        // 3. Insertar comentarios (Usando prepared statements porque foro_id y usuario_id son UUID)
        $stmtComentario = $db->prepare("INSERT INTO foro_comentario (foro_id, mensaje, fecha_envio, usuario_id) 
                                        VALUES (:foro_id, :mensaje, NOW(), :usuario_id)");
        
        $stmtComentario->execute([
            ':foro_id' => $foro_id,
            ':mensaje' => 'Te recomiendo buscar por la zona norte, es bastante segura.',
            ':usuario_id' => $usuario_id
        ]);
        
        $stmtComentario->execute([
            ':foro_id' => $foro_id,
            ':mensaje' => 'Yo alquilo cerca de la avenida principal, hay mucho transporte.',
            ':usuario_id' => $usuario_id
        ]);
                   
        // 4. Insertar reacciones
        $stmtReaccion = $db->prepare("INSERT INTO foro_reaccion (tipo_reaccion_codigo, foro_id, usuario_id) 
                                      VALUES ('LIKE', :foro_id, :usuario_id)");
        $stmtReaccion->execute([
            ':foro_id' => $foro_id,
            ':usuario_id' => $usuario_id
        ]);
                   
        echo "¡Datos de prueba para Foros insertados correctamente! (Foro ID: $foro_id)\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
