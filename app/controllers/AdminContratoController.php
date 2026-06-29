<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contrato;
use App\Models\Resena;
use App\Models\Multimedia;

class AdminContratoController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $contratoModel = new Contrato();

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $por_pagina = 10;

        $filtros = [];
        if (!empty($_GET['busqueda'])) $filtros['busqueda'] = $_GET['busqueda'];
        if (isset($_GET['estado']) && $_GET['estado'] !== '') $filtros['estado'] = $_GET['estado'];

        $total = $contratoModel->contar($filtros);
        $total_paginas = max(1, ceil($total / $por_pagina));
        $pagina = min($pagina, $total_paginas);

        $contratos = $contratoModel->buscar($filtros, $pagina, $por_pagina);

        $data = [
            'titulo' => 'Gestión de Contratos',
            'contratos' => $contratos,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas,
            'total' => $total,
            'filtros' => $filtros,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/contrato/index', $data, 'admin');
    }

    public function ver()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/contratos');
        }

        $contratoModel = new Contrato();
        $contrato = $contratoModel->findById($id);

        if (!$contrato) {
            $this->redirect('/admin/contratos');
        }

        $resenaModel = new Resena();
        $resenas = $resenaModel->getByContratoId($id);
        $resena = count($resenas) > 0 ? $resenas[0] : null;

        $data = [
            'titulo' => 'Detalle del Contrato',
            'contrato' => $contrato,
            'resena' => $resena,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/contrato/view', $data, 'admin');
    }

    public function crear()
    {
        $contratoModel = new Contrato();

        $data = [
            'titulo' => 'Crear Nuevo Contrato',
            'contrato' => null,
            'reservas' => $contratoModel->getReservasDisponibles(),
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/contrato/form', $data, 'admin');
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/contratos');
        }

        $contratoModel = new Contrato();
        $contrato = $contratoModel->findById($id);

        if (!$contrato) {
            $this->redirect('/admin/contratos');
        }

        $data = [
            'titulo' => 'Editar Contrato',
            'contrato' => $contrato,
            'reservas' => $contratoModel->getReservasDisponibles(),
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/contrato/form', $data, 'admin');
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $multimedia_id = null;
            
            // Handle file upload
            if (!empty($_FILES['documento']['name'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/contratos/';
                $maxSize = 5 * 1024 * 1024; // 5MB
                
                if ($_FILES['documento']['error'] === UPLOAD_ERR_OK && $_FILES['documento']['size'] <= $maxSize) {
                    $ext = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'webp'])) {
                        $newName = uniqid('contrato_') . '.' . $ext;
                        $destPath = $uploadDir . $newName;

                        if (move_uploaded_file($_FILES['documento']['tmp_name'], $destPath)) {
                            $multimediaModel = new Multimedia();
                            $multimedia_id = $multimediaModel->create([
                                'url'            => '/public/uploads/contratos/' . $newName,
                                'tipo_codigo'    => 'DOCUMENTO',
                                'nombre'         => $_FILES['documento']['name'],
                                'orden'          => 1,
                                'creado_por'     => $_SESSION['nombres'] ?? 'admin'
                            ]);
                        }
                    }
                }
            }

            $datos = [
                'reserva_id'         => $_POST['reserva_id'] ?? null,
                'fecha_inicio'       => $_POST['fecha_inicio'] ?? null,
                'fecha_fin'          => $_POST['fecha_fin'] ?? null,
                'monto_renta'        => $_POST['monto_renta'] ?? 0,
                'monto_garantia'     => $_POST['monto_garantia'] ?? 0,
                'cargo_plataforma'   => $_POST['cargo_plataforma'] ?? 0,
                'estado_codigo'      => $_POST['estado_codigo'] ?? 'ACTIVO',
                'fecha_pago_mensual' => $_POST['fecha_pago_mensual'] ?? 1,
                'multimedia_id'      => $multimedia_id,
                'creado_por'         => $_SESSION['nombres'] ?? 'admin'
            ];

            $contratoModel = new Contrato();
            $contratoModel->create($datos);

            self::setFlash('success', 'Contrato creado correctamente.');
        }
        $this->redirect('/admin/contratos');
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['contrato_id'] ?? null;
            if ($id) {
                $contratoModel = new Contrato();
                $contratoActual = $contratoModel->findById($id);
                $multimedia_id = $contratoActual['multimedia_id'] ?? null;

                // Handle file upload to replace document
                if (!empty($_FILES['documento']['name'])) {
                    $uploadDir = __DIR__ . '/../../public/uploads/contratos/';
                    $maxSize = 5 * 1024 * 1024; // 5MB
                    
                    if ($_FILES['documento']['error'] === UPLOAD_ERR_OK && $_FILES['documento']['size'] <= $maxSize) {
                        $ext = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
                        if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'webp'])) {
                            $newName = uniqid('contrato_') . '.' . $ext;
                            $destPath = $uploadDir . $newName;

                            if (move_uploaded_file($_FILES['documento']['tmp_name'], $destPath)) {
                                $multimediaModel = new Multimedia();
                                
                                // Optional: delete old file if it exists (requires more complex logic, let's just update DB for now or delete physical file)
                                if ($multimedia_id) {
                                    $oldMedia = $multimediaModel->findById($multimedia_id);
                                    if ($oldMedia && !empty($oldMedia['url'])) {
                                        $oldFilePath = __DIR__ . '/../../' . ltrim($oldMedia['url'], '/');
                                        if (file_exists($oldFilePath)) {
                                            unlink($oldFilePath);
                                        }
                                    }
                                    $multimediaModel->hardDelete($multimedia_id);
                                }

                                $multimedia_id = $multimediaModel->create([
                                    'url'            => '/public/uploads/contratos/' . $newName,
                                    'tipo_codigo'    => 'DOCUMENTO',
                                    'nombre'         => $_FILES['documento']['name'],
                                    'orden'          => 1,
                                    'creado_por'     => $_SESSION['nombres'] ?? 'admin'
                                ]);
                            }
                        }
                    }
                }

                $datos = [
                    'reserva_id'         => $_POST['reserva_id'] ?? null,
                    'fecha_inicio'       => $_POST['fecha_inicio'] ?? null,
                    'fecha_fin'          => $_POST['fecha_fin'] ?? null,
                    'monto_renta'        => $_POST['monto_renta'] ?? 0,
                    'monto_garantia'     => $_POST['monto_garantia'] ?? 0,
                    'cargo_plataforma'   => $_POST['cargo_plataforma'] ?? 0,
                    'estado_codigo'      => $_POST['estado_codigo'] ?? 'ACTIVO',
                    'fecha_pago_mensual' => $_POST['fecha_pago_mensual'] ?? 1,
                    'multimedia_id'      => $multimedia_id,
                    'modificado_por'     => $_SESSION['nombres'] ?? 'admin'
                ];

                $contratoModel->update($id, $datos);

                self::setFlash('success', 'Contrato actualizado correctamente.');
            }
        }
        $this->redirect('/admin/contratos');
    }

    public function toggleEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $estado = $_POST['estado'] ?? 0;

            if ($id) {
                $contratoModel = new Contrato();
                $contratoModel->toggleHabilitado($id, $estado);
            }
        }
        $this->redirect('/admin/contratos');
    }
}
