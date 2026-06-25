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

    $usuario_id = 'c73675d8-a245-4f6d-9af6-61dd2f15e896';

    // Verificar que el usuario existe
    $stmtCheck = $db->prepare("SELECT usuario_id, nombres, apellido_paterno, correo FROM usuario WHERE usuario_id = :id");
    $stmtCheck->bindParam(':id', $usuario_id, PDO::PARAM_STR);
    $stmtCheck->execute();
    $user = $stmtCheck->fetch();

    if (!$user) {
        die("Error: No se encontró el usuario con ID: $usuario_id\n");
    }

    echo "Usuario encontrado: " . $user['nombres'] . " " . $user['apellido_paterno'] . " (" . $user['correo'] . ")\n\n";

    // Obtener categorías disponibles del catálogo
    $stmtCat = $db->prepare("SELECT codigo, nombre FROM catalogo WHERE referencia_codigo = 'CATEGORIA_FORO' ORDER BY orden");
    $stmtCat->execute();
    $categorias = $stmtCat->fetchAll();

    if (empty($categorias)) {
        // Fallback si no hay categorías configuradas
        echo "No se encontraron categorías en catálogo, usando valores por defecto.\n";
        $categorias = [
            ['codigo' => 'ALOJ', 'nombre' => 'Alojamiento'],
            ['codigo' => 'TRAN', 'nombre' => 'Transporte'],
            ['codigo' => 'ESTU', 'nombre' => 'Estudios'],
            ['codigo' => 'EVEN', 'nombre' => 'Eventos'],
            ['codigo' => 'GENE', 'nombre' => 'General'],
        ];
    }

    // Obtener universidades disponibles
    $stmtUni = $db->query("SELECT universidad_id, nombre FROM universidad ORDER BY nombre LIMIT 5");
    $universidades = $stmtUni->fetchAll();

    if (empty($universidades)) {
        $universidades = [['universidad_id' => null, 'nombre' => 'General']];
    }

    echo "Categorías disponibles: " . count($categorias) . "\n";
    echo "Universidades disponibles: " . count($universidades) . "\n\n";

    // Temas de foros variados
    $foros_data = [
        [
            'titulo' => '¿Recomiendas alquilar por la zona de la UNMSM?',
            'descripcion' => 'Estoy buscando un cuarto cerca de San Marcos, he visto varios anuncios pero no sé qué zonas son seguras. ¿Alguien tiene experiencia viviendo por Mesa Redonda o el Cercado de Lima? Cualquier consejo es bienvenido.',
            'categoria_idx' => 0,
            'universidad_idx' => 0,
            'habilitado' => true,
        ],
        [
            'titulo' => 'Tips para encontrar roomie en tu universidad',
            'descripcion' => 'Compartir departamento es una gran opción para ahorrar. ¿Cómo han conocido a sus roomies? ¿Recomiendan grupos de Facebook o la misma universidad? Cuenten sus experiencias.',
            'categoria_idx' => 0,
            'universidad_idx' => 0,
            'habilitado' => true,
        ],
        [
            'titulo' => '¿Conviene más el metropolitano o los corredores?',
            'descripcion' => 'Con el alza de pasajes, estoy evaluando cuál me conviene más para ir a la universidad. Vivo por San Juan de Lurigancho y estudio en la PUCP. ¿Alguien hace la misma ruta?',
            'categoria_idx' => 1,
            'universidad_idx' => 1,
            'habilitado' => true,
        ],
        [
            'titulo' => 'Ciclo de verano: ¿vale la pena llevarlo?',
            'descripcion' => 'Estoy pensando en adelantar algunos cursos en el ciclo de verano, pero no sé si vale la pena el esfuerzo. Los horarios son intensivos y además hay que pagar extra. ¿Qué opinan?',
            'categoria_idx' => 2,
            'universidad_idx' => 2,
            'habilitado' => true,
        ],
        [
            'titulo' => '¿Alguien va al concierto de la primavera universitaria?',
            'descripcion' => 'Este año organizan un evento interuniversitario con bandas locales. ¿Alguien más va? Podríamos coordinar para ir en grupo desde la universidad.',
            'categoria_idx' => 3,
            'universidad_idx' => 0,
            'habilitado' => true,
        ],
        [
            'titulo' => '🚨 Cuidado: estafas en alquileres temporales',
            'descripcion' => 'Un amigo casi pierde su depósito por un anuncio falso en redes. Les comparto algunas señales de alerta para que no caigan: precios demasiado bajos, dueño que no puede mostrar el inmueble, solo aceptan transferencias. ¡Cuidado!',
            'categoria_idx' => 0,
            'universidad_idx' => 3,
            'habilitado' => true,
        ],
        [
            'titulo' => '¿Grupos de estudio para los cursos más duros?',
            'descripcion' => 'Los cursos de cálculo y física están bien difíciles este ciclo. ¿Alguien quiere armar un grupo de estudio para los sábados? Podemos reunirnos en la biblioteca central.',
            'categoria_idx' => 2,
            'universidad_idx' => 0,
            'habilitado' => true,
        ],
        [
            'titulo' => '¿Dónde comprar laptops con descuento universitario?',
            'descripcion' => 'Necesito una laptop nueva para la carrera, he visto que algunas marcas ofrecen descuento por ser universitario. ¿Alguien ha comprado con ese beneficio? ¿Recomiendan alguna marca/modelo?',
            'categoria_idx' => 2,
            'universidad_idx' => 4,
            'habilitado' => false,
        ],
        [
            'titulo' => 'Rifa universitaria: viaje a Cusco 2026',
            'descripcion' => 'El centro de estudiantes está organizando una rifa para financiar el viaje de promoción. Los premios incluyen paseos turísticos y una estadía en hoteles. Pregunten por los puntos de venta en el pabellón de letras.',
            'categoria_idx' => 3,
            'universidad_idx' => 2,
            'habilitado' => true,
        ],
        [
            'titulo' => '¿Cómo es la vida en los residenciales universitarios?',
            'descripcion' => 'Estoy considerando mudarme a un residencial universitario el próximo ciclo. ¿Alguno ha vivido en uno? ¿Cómo es la convivencia? ¿Vale la pena comparado con alquilar un cuarto particular?',
            'categoria_idx' => 0,
            'universidad_idx' => 1,
            'habilitado' => true,
        ],
    ];

    $comentarios_data = [
        // Foro 1: ¿Recomiendas alquilar por la zona de la UNMSM?
        [
            'foro_idx' => 0, 'mensaje' => 'Yo vivo por la Av. Colonial y está bien, hay bastante movimiento y cerca hay mercados. Lo malo es el ruido.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 0, 'mensaje' => 'No recomiendo Mesa Redonda, es muy congestionado y hay mucha delincuencia. Mejor busca por Breña.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 0, 'mensaje' => 'Hay unos cuartos cerca a la puerta 5 de San Marcos, son económicos. Pregunta en la oficina de bienestar universitario.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 0, 'mensaje' => 'Este comentario fue reportado y eliminado por spam.', 'habilitado' => false,
        ],
        [
            'foro_idx' => 0, 'mensaje' => 'Vivo en el Cercado desde hace 2 años. Tips: busca con rejas y vigilancia, no salgas muy tarde. Fuera de eso, la ubicación es inmejorable para ir a San Marcos.', 'habilitado' => true,
        ],
        // Foro 2: Tips para encontrar roomie
        [
            'foro_idx' => 1, 'mensaje' => 'Yo encontré a mi roomie por el grupo de la universidad en WhatsApp. Funciona bien si pones tus reglas claras desde el inicio.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 1, 'mensaje' => 'La app de CompartoCuarto me sirvió bastante. También hay grupos en FB específicos por universidad.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 1, 'mensaje' => 'Importante: hagan un acuerdo de convivencia escrito. Horarios de limpieza, visitas, ruido. Evita problemas después.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 1, 'mensaje' => 'Felizmente mi mejor amigo de la universidad se volvió mi roomie. La confianza ayuda mucho.', 'habilitado' => true,
        ],
        // Foro 3: Metropolitano o corredores
        [
            'foro_idx' => 2, 'mensaje' => 'El Metropolitano es más rápido pero siempre lleno en hora punta. Yo prefiero los corredores porque son menos saturados.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 2, 'mensaje' => 'De SJL a la PUCP en Metropolitano te demoras como 1h30. Mejor busca una ruta con corredor complementario.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 2, 'mensaje' => 'Con el pago integrado del metropolitano a corredor te sale más barato. Revisa la app de la ATU.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 2, 'mensaje' => 'Invertir en una moto lineal fue lo mejor que hice. En 40 min llegaba a la PUCP desde SJL.', 'habilitado' => true,
        ],
        // Foro 4: Ciclo de verano
        [
            'foro_idx' => 3, 'mensaje' => 'Depende del curso. Si es un curso que se te hace difícil, mejor llévalo en ciclo regular con más tiempo.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 3, 'mensaje' => 'Yo adelanté 3 cursos en verano y pude aligerar mi carga en el ciclo regular. Totalmente recomendado si te organizas.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 3, 'mensaje' => 'El problema es que algunos profesores son muy exigentes y al ser intensivo no te da tiempo de asimilar bien.', 'habilitado' => true,
        ],
        // Foro 5: Concierto primavera universitaria
        [
            'foro_idx' => 4, 'mensaje' => 'Yo voy con mis amigos de la facultad. Han confirmado bandas bien buenas este año.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 4, 'mensaje' => '¿Dónde se venden los tickets? ¿Hay descuento por grupo universitario?', 'habilitado' => true,
        ],
        [
            'foro_idx' => 4, 'mensaje' => 'El año pasado estuvo genial, este año promete más. Voy a ir con mi cámara a grabar.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 4, 'mensaje' => 'Coordinemos un punto de encuentro. Yo propongo el frontis de la biblioteca central.', 'habilitado' => true,
        ],
        // Foro 5: Estafas en alquileres
        [
            'foro_idx' => 5, 'mensaje' => 'Gracias por el aviso! Justo estaba viendo un anuncio sospechoso. No sabía de esas señales.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 5, 'mensaje' => 'A mí me estafaron con un depósito de 500 soles. Nunca entregaron las llaves. Hagan todo por escrito.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 5, 'mensaje' => 'Siempre pidan video llamada o visita presencial antes de depositar cualquier cosa.', 'habilitado' => true,
        ],
        // Foro 6: Grupos de estudio
        [
            'foro_idx' => 6, 'mensaje' => 'Me apunto para cálculo. Estoy en el aula 203 de la facultad de ciencias.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 6, 'mensaje' => 'También podemos hacer un grupo de Discord para compartir materiales y resolver dudas entre semana.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 6, 'mensaje' => 'Física 2 me está costando mucho. ¿Alguien más quiere repasar?', 'habilitado' => true,
        ],
        [
            'foro_idx' => 6, 'mensaje' => 'Los sábados en la biblioteca está lleno, mejor reservar sala con anticipación.', 'habilitado' => true,
        ],
        // Foro 7: Laptops con descuento
        [
            'foro_idx' => 7, 'mensaje' => 'Lenovo tiene un programa de descuento universitario, te ahorras como 15%.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 7, 'mensaje' => 'Yo compré una Dell en su página con el .edu y me dieron buen descuento. La recomiendo.', 'habilitado' => true,
        ],
        // Foro 8: Rifa universitaria
        [
            'foro_idx' => 8, 'mensaje' => 'Ya compré mis 5 tickets. Ojalá me gane el viaje, tengo muchas ganas de conocer Cusco.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 8, 'mensaje' => '¿Hasta cuándo venden los tickets? Quiero comprar pero estoy en práctica.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 8, 'mensaje' => 'Los chicos del centro de estudiantes están en el Hall principal hasta las 6pm.', 'habilitado' => true,
        ],
        // Foro 9: Residenciales universitarios
        [
            'foro_idx' => 9, 'mensaje' => 'Viví un año en el residencial de mi universidad. Es cómodo pero hay reglas estrictas. Bueno para enfocarte.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 9, 'mensaje' => 'El costo es menor que un alquiler particular y ya incluye servicios. La convivencia es lo más difícil.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 9, 'mensaje' => 'Hay residenciales mixtos ahora, pero algunos mantienen separación por género. Pregunta bien antes de elegir.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 9, 'mensaje' => 'No me gustó la experiencia, siento que no tienes privacidad. Prefiero alquilar un cuarto aunque sea más caro.', 'habilitado' => true,
        ],
        [
            'foro_idx' => 9, 'mensaje' => 'Depende de la universidad. En la UNI el residencial es súper ordenado y barato. En otras no sé.', 'habilitado' => true,
        ],
    ];

    $reacciones_data = [
        ['foro_idx' => 0, 'tipo' => 'LIKE'],
        ['foro_idx' => 0, 'tipo' => 'LIKE'],
        ['foro_idx' => 1, 'tipo' => 'LIKE'],
        ['foro_idx' => 1, 'tipo' => 'LOVE'],
        ['foro_idx' => 2, 'tipo' => 'LIKE'],
        ['foro_idx' => 2, 'tipo' => 'LIKE'],
        ['foro_idx' => 3, 'tipo' => 'LIKE'],
        ['foro_idx' => 4, 'tipo' => 'LIKE'],
        ['foro_idx' => 4, 'tipo' => 'LOVE'],
        ['foro_idx' => 5, 'tipo' => 'LIKE'],
        ['foro_idx' => 5, 'tipo' => 'LIKE'],
        ['foro_idx' => 6, 'tipo' => 'LIKE'],
        ['foro_idx' => 7, 'tipo' => 'LIKE'],
        ['foro_idx' => 8, 'tipo' => 'LIKE'],
        ['foro_idx' => 9, 'tipo' => 'LIKE'],
        ['foro_idx' => 9, 'tipo' => 'DISLIKE'],
    ];

    // Obtener usuarios para usar como reaccionadores (necesitamos varios usuarios para las reacciones)
    $stmtUsers = $db->query("SELECT usuario_id FROM usuario WHERE usuario_id != '" . $usuario_id . "' ORDER BY random() LIMIT 5");
    $otros_usuarios = $stmtUsers->fetchAll();
    if (empty($otros_usuarios)) {
        $otros_usuarios = [['usuario_id' => $usuario_id]];
    }

    $contador_foros = 0;
    $contador_comentarios = 0;
    $contador_reacciones = 0;

    // Preparar statements
    $stmtForo = $db->prepare("INSERT INTO foro (titulo, descripcion, fecha_creacion, estado_codigo, usuario_id, habilitado, categoria_codigo, universidad_id)
                              VALUES (:titulo, :descripcion, :fecha, 'ACT', :usuario_id, :habilitado, :categoria_codigo, :universidad_id) RETURNING foro_id");

    $stmtComentario = $db->prepare("INSERT INTO foro_comentario (foro_id, mensaje, fecha_envio, usuario_id, habilitado)
                                    VALUES (:foro_id, :mensaje, :fecha, :usuario_id, :habilitado)");

    $stmtReaccion = $db->prepare("INSERT INTO foro_reaccion (tipo_reaccion_codigo, foro_id, usuario_id)
                                    VALUES (:tipo, :foro_id, :usuario_id)");

    // Insertar foros con variación de fechas
    $dias_atras = 60;

    foreach ($foros_data as $fi => $foro_data) {
        $categoria = $categorias[$foro_data['categoria_idx'] % count($categorias)];
        $universidad = $universidades[$foro_data['universidad_idx'] % count($universidades)];
        $fecha = date('Y-m-d H:i:s', strtotime("-" . ($dias_atras - $fi * 5) . " days + " . rand(0, 12) . " hours"));

        $stmtForo->execute([
            ':titulo' => $foro_data['titulo'],
            ':descripcion' => $foro_data['descripcion'],
            ':fecha' => $fecha,
            ':usuario_id' => $usuario_id,
            ':habilitado' => $foro_data['habilitado'],
            ':categoria_codigo' => $categoria['codigo'],
            ':universidad_id' => $universidad['universidad_id'],
        ]);

        $foro_id = $stmtForo->fetchColumn();
        $contador_foros++;

        echo "  Foro #$contador_foros: '" . substr($foro_data['titulo'], 0, 50) . "...' (ID: $foro_id, Cat: " . $categoria['nombre'] . ")\n";

        // Insertar comentarios para este foro
        $comentarios_foro = array_filter($comentarios_data, function($c) use ($fi) {
            return $c['foro_idx'] == $fi;
        });

        $dias_offset = 1;
        foreach ($comentarios_foro as $ci => $comentario) {
            $fecha_com = date('Y-m-d H:i:s', strtotime($fecha . " + $dias_offset days + " . rand(0, 8) . " hours"));

            $stmtComentario->execute([
                ':foro_id' => $foro_id,
                ':mensaje' => $comentario['mensaje'],
                ':fecha' => $fecha_com,
                ':usuario_id' => $usuario_id,
                ':habilitado' => $comentario['habilitado'],
            ]);
            $contador_comentarios++;
            $dias_offset++;
        }

        // Insertar reacciones para este foro
        $reacciones_foro = array_filter($reacciones_data, function($r) use ($fi) {
            return $r['foro_idx'] == $fi;
        });

        $ri = 0;
        foreach ($reacciones_foro as $reaccion) {
            $user_reaccion = $otros_usuarios[$ri % count($otros_usuarios)];

            try {
                $stmtReaccion->execute([
                    ':tipo' => $reaccion['tipo'],
                    ':foro_id' => $foro_id,
                    ':usuario_id' => $user_reaccion['usuario_id'],
                ]);
                $contador_reacciones++;
            } catch (Exception $e) {
                // Ignorar duplicados
            }
            $ri++;
        }
    }

    echo "\n========================================\n";
    echo "✅ Resumen de inserción:\n";
    echo "   Foros:       $contador_foros\n";
    echo "   Comentarios: $contador_comentarios\n";
    echo "   Reacciones:  $contador_reacciones\n";
    echo "   Usuario:     " . $user['nombres'] . " " . $user['apellido_paterno'] . " ($usuario_id)\n";
    echo "========================================\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
