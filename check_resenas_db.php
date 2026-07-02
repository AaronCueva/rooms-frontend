<?php
try {
    $conn = new PDO(
        'pgsql:host=aws-1-us-east-2.pooler.supabase.com;port=6543;dbname=postgres',
        'postgres.lokjiueialuwrulybgut',
        'g0UNVXoLuA8uaPtH'
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    echo "=== CATALOGOS VINCULADOS A RESENA O ESTADO ===\n";
    $stmt = $conn->query("SELECT DISTINCT referencia_codigo FROM catalogo ORDER BY referencia_codigo");
    $refs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($refs as $r) {
        echo " - " . $r . "\n";
    }

    echo "\n=== CATALOGOS CON 'RESEN' O 'ESTADO' ===\n";
    $stmt = $conn->query("SELECT codigo, nombre, descripcion, referencia_codigo FROM catalogo WHERE referencia_codigo ILIKE '%ESTADO%' OR referencia_codigo ILIKE '%RESEN%' ORDER BY referencia_codigo, orden, codigo");
    while ($row = $stmt->fetch()) {
        echo " [{$row['referencia_codigo']}] {$row['codigo']} : {$row['nombre']} ({$row['descripcion']})\n";
    }

    echo "\n=== CONTEO DE DATOS ACTUALES ===\n";
    foreach (['usuario', 'alojamiento', 'reserva', 'contrato', 'resena', 'resenia_alojamiento'] as $t) {
        $cnt = $conn->query("SELECT count(*) FROM $t")->fetchColumn();
        echo "Tabla $t: $cnt registros\n";
    }

    echo "\n=== MUESTRA DE USUARIOS (ESTUDIANTES Y PROPIETARIOS) ===\n";
    $stmt = $conn->query("SELECT u.usuario_id, u.nombres, u.apellido_paterno, u.correo, r.nombre as rol FROM usuario u LEFT JOIN rol r ON u.rol_id = r.rol_id LIMIT 10");
    while ($row = $stmt->fetch()) {
        echo " Usuario: {$row['nombres']} {$row['apellido_paterno']} | Rol: {$row['rol']} | ID: {$row['usuario_id']}\n";
    }

    echo "\n=== MUESTRA DE ALOJAMIENTOS ===\n";
    $stmt = $conn->query("SELECT alojamiento_id, titulo, precio_mensual, usuario_id FROM alojamiento LIMIT 5");
    while ($row = $stmt->fetch()) {
        echo " Alojamiento: {$row['titulo']} | Precio: {$row['precio_mensual']} | PropID: {$row['usuario_id']} | ID: {$row['alojamiento_id']}\n";
    }

    echo "\n=== MUESTRA DE CONTRATOS ===\n";
    $stmt = $conn->query("SELECT contrato_id, fecha_inicio, fecha_fin, monto_renta, estado_codigo, usuario_id FROM contrato LIMIT 5");
    while ($row = $stmt->fetch()) {
        echo " Contrato: {$row['contrato_id']} | Inicio: {$row['fecha_inicio']} | Fin: {$row['fecha_fin']} | Estado: {$row['estado_codigo']} | UserID: {$row['usuario_id']}\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
