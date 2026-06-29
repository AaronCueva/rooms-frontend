<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Reserva;
use App\Models\Catalogo;

class AdminReservaController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $reservaModel = new Reserva();
        $catalogoModel = new Catalogo();

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $por_pagina = 10;

        $filtros = [];
        if (!empty($_GET['busqueda'])) $filtros['busqueda'] = $_GET['busqueda'];
        if (isset($_GET['estado']) && $_GET['estado'] !== '') $filtros['estado'] = $_GET['estado'];

        $total = $reservaModel->contar($filtros);
        $total_paginas = max(1, ceil($total / $por_pagina));
        $pagina = min($pagina, $total_paginas);

        $reservas = $reservaModel->buscar($filtros, $pagina, $por_pagina);
        $estados_reserva = $catalogoModel->obtenerPorReferencia('ESTADO_RESERVA');
        if (empty($estados_reserva)) {
            $estados_reserva = [
                ['codigo' => 'ESRE001', 'nombre' => 'Pendiente'],
                ['codigo' => 'ESRE002', 'nombre' => 'Aprobada'],
                ['codigo' => 'ESRE003', 'nombre' => 'Rechazada'],
                ['codigo' => 'ESRE004', 'nombre' => 'Finalizada']
            ];
        }

        $data = [
            'titulo' => 'Gestión de Reservas',
            'reservas' => $reservas,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas,
            'total' => $total,
            'filtros' => $filtros,
            'estados_reserva' => $estados_reserva,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/reserva/index', $data, 'admin');
    }

    public function ver()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/reservas');
        }

        $reservaModel = new Reserva();
        $reserva = $reservaModel->findById($id);

        if (!$reserva) {
            $this->redirect('/admin/reservas');
        }

        $data = [
            'titulo' => 'Detalle de Reserva',
            'reserva' => $reserva,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/reserva/view', $data, 'admin');
    }

    public function crear()
    {
        $reservaModel = new Reserva();
        $catalogoModel = new Catalogo();
        $estados_reserva = $catalogoModel->obtenerPorReferencia('ESTADO_RESERVA');
        if (empty($estados_reserva)) {
            $estados_reserva = [
                ['codigo' => 'ESRE001', 'nombre' => 'Pendiente'],
                ['codigo' => 'ESRE002', 'nombre' => 'Aprobada'],
                ['codigo' => 'ESRE003', 'nombre' => 'Rechazada'],
                ['codigo' => 'ESRE004', 'nombre' => 'Finalizada']
            ];
        }

        $data = [
            'titulo' => 'Crear Nueva Reserva',
            'reserva' => null,
            'usuarios' => $reservaModel->getUsuarios(),
            'alojamientos' => $reservaModel->getAlojamientos(),
            'estados_reserva' => $estados_reserva,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/reserva/form', $data, 'admin');
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/reservas');
        }

        $reservaModel = new Reserva();
        $reserva = $reservaModel->findById($id);

        if (!$reserva) {
            $this->redirect('/admin/reservas');
        }

        $catalogoModel = new Catalogo();
        $estados_reserva = $catalogoModel->obtenerPorReferencia('ESTADO_RESERVA');
        if (empty($estados_reserva)) {
            $estados_reserva = [
                ['codigo' => 'ESRE001', 'nombre' => 'Pendiente'],
                ['codigo' => 'ESRE002', 'nombre' => 'Aprobada'],
                ['codigo' => 'ESRE003', 'nombre' => 'Rechazada'],
                ['codigo' => 'ESRE004', 'nombre' => 'Finalizada']
            ];
        }

        $data = [
            'titulo' => 'Editar Reserva',
            'reserva' => $reserva,
            'usuarios' => $reservaModel->getUsuarios(),
            'alojamientos' => $reservaModel->getAlojamientos(),
            'estados_reserva' => $estados_reserva,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/reserva/form', $data, 'admin');
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'fecha_ingreso'        => $_POST['fecha_ingreso'] ?? null,
                'duracion_meses'       => $_POST['duracion_meses'] ?? 0,
                'monto_total'          => $_POST['monto_total'] ?? 0,
                'mensaje_presentacion' => $_POST['mensaje_presentacion'] ?? null,
                'estado_codigo'        => $_POST['estado_codigo'] ?? 'PENDIENTE',
                'usuario_id'           => $_POST['usuario_id'] ?? null,
                'alojamiento_id'       => $_POST['alojamiento_id'] ?? null,
                'creado_por'           => $_SESSION['nombres'] ?? 'admin'
            ];

            $reservaModel = new Reserva();
            $reservaModel->create($datos);

            self::setFlash('success', 'Reserva creada correctamente.');
        }
        $this->redirect('/admin/reservas');
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['reserva_id'] ?? null;
            if ($id) {
                $datos = [
                    'fecha_ingreso'        => $_POST['fecha_ingreso'] ?? null,
                    'duracion_meses'       => $_POST['duracion_meses'] ?? 0,
                    'monto_total'          => $_POST['monto_total'] ?? 0,
                    'mensaje_presentacion' => $_POST['mensaje_presentacion'] ?? null,
                    'estado_codigo'        => $_POST['estado_codigo'] ?? 'PENDIENTE',
                    'usuario_id'           => $_POST['usuario_id'] ?? null,
                    'alojamiento_id'       => $_POST['alojamiento_id'] ?? null,
                    'fecha_respuesta'      => !empty($_POST['fecha_respuesta']) ? $_POST['fecha_respuesta'] : null,
                    'modificado_por'       => $_SESSION['nombres'] ?? 'admin'
                ];

                $reservaModel = new Reserva();
                $reservaModel->update($id, $datos);

                self::setFlash('success', 'Reserva actualizada correctamente.');
            }
        }
        $this->redirect('/admin/reservas');
    }

    public function toggleEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $estado = $_POST['estado'] ?? 0;

            if ($id) {
                $reservaModel = new Reserva();
                $reservaModel->toggleHabilitado($id, $estado);
            }
        }
        $this->redirect('/admin/reservas');
    }
}
