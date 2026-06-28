<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Alojamiento;
use App\Models\Favorito;
use App\Models\PoliticaCasa;
use App\Models\Servicio;
use App\Models\AlojamientoServicio;
use App\Models\AlojamientoPolitica;
use App\Models\Descuento;
use App\Models\Beneficio;
use App\Models\Catalogo;
use App\Models\Ubicacion;
use App\Models\Multimedia;
use App\Models\Resena;
use App\Models\ReseniaAlojamiento;

class AdminAlojamientoController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $alojamientoModel = new Alojamiento();
        $catalogoModel = new Catalogo();

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $por_pagina = 10;

        $filtros = [];
        if (!empty($_GET['busqueda'])) $filtros['busqueda'] = $_GET['busqueda'];
        if (isset($_GET['estado']) && $_GET['estado'] !== '') $filtros['estado'] = $_GET['estado'];

        $total = $alojamientoModel->contar($filtros);
        $total_paginas = max(1, ceil($total / $por_pagina));
        $pagina = min($pagina, $total_paginas);

        $alojamientos = $alojamientoModel->buscar($filtros, $pagina, $por_pagina);
        $estados = $catalogoModel->obtenerPorReferencia('ESTADO_PUBLICACION');

        $data = [
            'titulo' => 'Gestión de Alojamientos',
            'alojamientos' => $alojamientos,
            'estados' => $estados,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas,
            'total' => $total,
            'filtros' => $filtros,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/alojamiento/index', $data, 'admin');
    }

    public function ver()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/alojamientos');
        }

        $alojamientoModel = new Alojamiento();
        $alojamiento = $alojamientoModel->findById($id);

        if (!$alojamiento) {
            $this->redirect('/admin/alojamientos');
        }

        $favoritoModel = new Favorito();
        $favoritos = $favoritoModel->getByAlojamientoId($id);

        $alojamientoServicioModel = new AlojamientoServicio();
        $servicios_asignados = $alojamientoServicioModel->getByAlojamientoId($id);

        $alojamientoPoliticaModel = new AlojamientoPolitica();
        $politicas_asignadas = $alojamientoPoliticaModel->getByAlojamientoId($id);

        $descuentoModel = new Descuento();
        $descuentos = $descuentoModel->getByAlojamientoId($id);

        $beneficioModel = new Beneficio();
        $beneficios = $beneficioModel->getByAlojamientoId($id);

        $servicioModel = new Servicio();
        $servicios_disponibles = $servicioModel->getAll();

        $multimediaModel = new Multimedia();
        $imagenes = $multimediaModel->getByAlojamientoId($id);

        $reseniaModel = new ReseniaAlojamiento();
        $resenas = $reseniaModel->getByAlojamientoId($id);

        $data = [
            'titulo' => 'Detalle del Alojamiento: ' . $alojamiento['titulo'],
            'alojamiento' => $alojamiento,
            'favoritos' => $favoritos,
            'servicios_asignados' => $servicios_asignados,
            'politicas_asignadas' => $politicas_asignadas,
            'descuentos' => $descuentos,
            'beneficios' => $beneficios,
            'servicios_disponibles' => $servicios_disponibles,
            'imagenes' => $imagenes,
            'resenas' => $resenas,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/alojamiento/view', $data, 'admin');
    }

    public function crear()
    {
        $this->cargarDatosFormulario('Crear Nuevo Alojamiento');
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/alojamientos');
        }

        $alojamientoModel = new Alojamiento();
        $alojamiento = $alojamientoModel->findById($id);

        if (!$alojamiento) {
            $this->redirect('/admin/alojamientos');
        }

        $this->cargarDatosFormulario('Editar Alojamiento', $alojamiento);
    }

    private function cargarDatosFormulario($titulo, $alojamiento = null)
    {
        $catalogoModel = new Catalogo();
        $ubicacionModel = new Ubicacion();
        $politicaModel = new PoliticaCasa();
        $alojamientoModel = new Alojamiento();

        $tipos = $catalogoModel->obtenerPorReferencia('TIPO_PUBLICACION_ALOJAMIENTO');
        $generos = $catalogoModel->obtenerPorReferencia('GENERO_EXCLUSIVO_ALOJAMIENTO');
        $monedas = $catalogoModel->obtenerPorReferencia('TIPO_MONEDA');
        $estados = $catalogoModel->obtenerPorReferencia('ESTADO_PUBLICACION_ALOJAMIENTO'); // Asumiendo este nombre para la referencia
        $departamentos = $ubicacionModel->obtenerDepartamentos();
        $politicas = $politicaModel->getAll();
        $propietarios = $alojamientoModel->getPropietarios(); // Trae usuarios OWNER

        $jerarquia = null;
        if ($alojamiento && !empty($alojamiento['ubicacion_id'])) {
            $jerarquia = $ubicacionModel->obtenerJerarquia($alojamiento['ubicacion_id']);
        }

        // Obtener IDs de políticas ya seleccionadas (para edición)
        $politicas_seleccionadas = [];
        if ($alojamiento) {
            $alojamientoPoliticaModel = new AlojamientoPolitica();
            $politicas_seleccionadas = $alojamientoPoliticaModel->getIdsByAlojamientoId($alojamiento['alojamiento_id']);
        }

        $data = [
            'titulo' => $titulo,
            'alojamiento' => $alojamiento,
            'jerarquia' => $jerarquia,
            'tipos' => $tipos,
            'generos' => $generos,
            'monedas' => $monedas,
            'estados' => $estados,
            'departamentos' => $departamentos,
            'politicas' => $politicas,
            'politicas_seleccionadas' => $politicas_seleccionadas,
            'propietarios' => $propietarios,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/alojamiento/form', $data, 'admin');
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = $this->obtenerDatosPost();
            $alojamientoModel = new Alojamiento();
            $alojamiento_id = $alojamientoModel->create($datos);

            // Sincronizar políticas
            if ($alojamiento_id) {
                $politicas = $_POST['politicas'] ?? [];
                $alojamientoPoliticaModel = new AlojamientoPolitica();
                $alojamientoPoliticaModel->sincronizarPoliticas($alojamiento_id, $politicas);
            }
        }
        $this->redirect('/admin/alojamientos');
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['alojamiento_id'] ?? null;
            if ($id) {
                $datos = $this->obtenerDatosPost();
                $alojamientoModel = new Alojamiento();
                $alojamientoModel->update($id, $datos);

                // Sincronizar políticas
                $politicas = $_POST['politicas'] ?? [];
                $alojamientoPoliticaModel = new AlojamientoPolitica();
                $alojamientoPoliticaModel->sincronizarPoliticas($id, $politicas);
            }
        }
        $this->redirect('/admin/alojamientos');
    }

    private function obtenerDatosPost()
    {
        return [
            'codigo' => $_POST['codigo'] ?? '',
            'titulo' => $_POST['titulo'] ?? '',
            'tipo_codigo' => $_POST['tipo_codigo'] ?? '',
            'numero_habitaciones' => $_POST['numero_habitaciones'] ?? 0,
            'numero_banios' => $_POST['numero_banios'] ?? 0,
            'bano_privado' => isset($_POST['bano_privado']) ? 1 : 0,
            'tamanio_m2' => $_POST['tamanio_m2'] ?? 0,
            'genero_exclusivo_codigo' => $_POST['genero_exclusivo_codigo'] ?? '',
            'mascotas_permitidas' => isset($_POST['mascotas_permitidas']) ? 1 : 0,
            'fumadores_permitidos' => isset($_POST['fumadores_permitidos']) ? 1 : 0,
            'descripcion' => $_POST['descripcion'] ?? '',
            'usuario_id' => $_POST['usuario_id'] ?? null,
            'ubicacion_id' => $_POST['distrito'] ?? null,
            'direccion' => $_POST['direccion'] ?? '',
            'latitud' => !empty($_POST['latitud']) ? $_POST['latitud'] : null,
            'longitud' => !empty($_POST['longitud']) ? $_POST['longitud'] : null,
            'precio_mensual' => $_POST['precio_mensual'] ?? 0,
            'moneda_codigo' => $_POST['moneda_codigo'] ?? '',
            'garantia' => $_POST['garantia'] ?? 0,
            'duracion_minima_meses' => $_POST['duracion_minima_meses'] ?? 0,
            'fecha_disponible' => !empty($_POST['fecha_disponible']) ? $_POST['fecha_disponible'] : null,
            'estado_codigo' => $_POST['estado_codigo'] ?? '',
            'habilitado' => isset($_POST['habilitado']) ? 1 : 0,
            'precio_servicios' => $_POST['precio_servicios'] ?? 0,
            'amoblado' => isset($_POST['amoblado']) ? 1 : 0,
            'solo_verificados' => isset($_POST['solo_verificados']) ? 1 : 0,
            'calificacion' => $_POST['calificacion'] ?? 0
        ];
    }

    public function toggleEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $estado = $_POST['estado'] ?? 0;

            if ($id) {
                $alojamientoModel = new Alojamiento();
                $alojamientoModel->toggleHabilitado($id, $estado);
            }
        }
        $this->redirect('/admin/alojamientos');
    }

    public function aprobar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $estado_codigo = $_POST['estado_codigo'] ?? 'EPA003'; // Código para APROBADO

            if ($id) {
                $alojamientoModel = new Alojamiento();
                $alojamientoModel->aprobar($id, $estado_codigo);
            }
        }
        $this->redirect('/admin/alojamientos/ver?id=' . $_POST['id']);
    }

    // --- Servicios ---
    public function agregarServicio()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'alojamiento_id' => $_POST['alojamiento_id'],
                'servicio_id' => $_POST['servicio_id'],
                'precio' => $_POST['precio'] ?? 0,
                'fecha_pago' => !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : null
            ];
            $model = new AlojamientoServicio();
            $model->create($datos);
            $this->redirect('/admin/alojamientos/ver?id=' . $_POST['alojamiento_id']);
        }
    }

    public function eliminarServicio()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = new AlojamientoServicio();
            $model->delete($_POST['alojamiento_servicio_id']);
            $this->redirect('/admin/alojamientos/ver?id=' . $_POST['alojamiento_id']);
        }
    }

    // --- Descuentos ---
    public function guardarDescuento()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'alojamiento_id' => $_POST['alojamiento_id'],
                'nombre' => $_POST['nombre'],
                'monto' => $_POST['monto'],
                'motivo' => $_POST['motivo'],
                'inicio' => $_POST['inicio'],
                'fin' => $_POST['fin']
            ];
            $model = new Descuento();
            $model->create($datos); // Solo implementamos create por ahora para simplificar, se puede agregar update luego
            $this->redirect('/admin/alojamientos/ver?id=' . $_POST['alojamiento_id']);
        }
    }

    public function eliminarDescuento()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = new Descuento();
            $model->delete($_POST['descuento_id']);
            $this->redirect('/admin/alojamientos/ver?id=' . $_POST['alojamiento_id']);
        }
    }

    // --- Beneficios ---
    public function guardarBeneficio()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'alojamiento_id' => $_POST['alojamiento_id'],
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'habilitado' => isset($_POST['habilitado']) ? 1 : 0
            ];
            $model = new Beneficio();
            $model->create($datos);
            $this->redirect('/admin/alojamientos/ver?id=' . $_POST['alojamiento_id']);
        }
    }

    public function eliminarBeneficio()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = new Beneficio();
            $model->delete($_POST['beneficio_id']);
            $this->redirect('/admin/alojamientos/ver?id=' . $_POST['alojamiento_id']);
        }
    }

    // --- Multimedia (Imágenes) ---
    public function subirImagen()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $alojamiento_id = $_POST['alojamiento_id'] ?? null;
            if (!$alojamiento_id || empty($_FILES['imagenes']['name'][0])) {
                $this->redirect('/admin/alojamientos/ver?id=' . $alojamiento_id);
                return;
            }

            $uploadDir = __DIR__ . '/../../public/uploads/alojamientos/';
            $multimediaModel = new Multimedia();
            $maxSize = 5 * 1024 * 1024; // 5MB

            foreach ($_FILES['imagenes']['name'] as $i => $name) {
                if ($_FILES['imagenes']['error'][$i] !== UPLOAD_ERR_OK) continue;
                if ($_FILES['imagenes']['size'][$i] > $maxSize) continue;

                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) continue;

                $newName = uniqid('aloj_') . '.' . $ext;
                $destPath = $uploadDir . $newName;

                if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $destPath)) {
                    $multimediaModel->create([
                        'url'            => '/public/uploads/alojamientos/' . $newName,
                        'tipo_codigo'    => 'IMAGEN',
                        'nombre'         => $name,
                        'orden'          => $i,
                        'alojamiento_id' => $alojamiento_id,
                        'creado_por'     => $_SESSION['nombres'] ?? 'admin'
                    ]);
                }
            }

            self::setFlash('success', 'Imágenes subidas correctamente.');
            $this->redirect('/admin/alojamientos/ver?id=' . $alojamiento_id);
        }
    }

    public function eliminarImagen()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $multimedia_id = $_POST['multimedia_id'] ?? null;
            $alojamiento_id = $_POST['alojamiento_id'] ?? null;

            if ($multimedia_id) {
                $multimediaModel = new Multimedia();
                $media = $multimediaModel->findById($multimedia_id);
                if ($media && !empty($media['url'])) {
                    $filePath = __DIR__ . '/../../' . ltrim($media['url'], '/');
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $multimediaModel->hardDelete($multimedia_id);
            }

            self::setFlash('success', 'Imagen eliminada.');
            $this->redirect('/admin/alojamientos/ver?id=' . $alojamiento_id);
        }
    }

    // --- Reseñas ---
    public function toggleResena()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $resena_id = $_POST['resena_id'] ?? null;
            $alojamiento_id = $_POST['alojamiento_id'] ?? null;

            if ($resena_id) {
                $reseniaModel = new ReseniaAlojamiento();
                $reseniaModel->toggleEstado($resena_id);
            }

            self::setFlash('success', 'Estado de la reseña actualizado.');
            $this->redirect('/admin/alojamientos/ver?id=' . $alojamiento_id);
        }
    }
}
