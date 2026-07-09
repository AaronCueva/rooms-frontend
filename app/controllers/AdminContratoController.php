<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contrato;
use App\Models\Resena;
use App\Models\Multimedia;
use App\Models\Catalogo;

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
        $catalogoModel = new Catalogo();
        $estados_contrato = $catalogoModel->obtenerPorReferencia('ESTADO_CONTRATO');
        if (empty($estados_contrato)) {
            $estados_contrato = [
                ['codigo' => 'ESCO001', 'nombre' => 'Activo'],
                ['codigo' => 'ESCO002', 'nombre' => 'Finalizado'],
                ['codigo' => 'ESCO003', 'nombre' => 'Cancelado']
            ];
        }

        $data = [
            'titulo' => 'Gestión de Contratos',
            'contratos' => $contratos,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas,
            'total' => $total,
            'filtros' => $filtros,
            'estados_contrato' => $estados_contrato,
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
        $catalogoModel = new Catalogo();
        $estados_contrato = $catalogoModel->obtenerPorReferencia('ESTADO_CONTRATO');
        if (empty($estados_contrato)) {
            $estados_contrato = [
                ['codigo' => 'ESCO001', 'nombre' => 'Activo'],
                ['codigo' => 'ESCO002', 'nombre' => 'Finalizado'],
                ['codigo' => 'ESCO003', 'nombre' => 'Cancelado']
            ];
        }

        $data = [
            'titulo' => 'Crear Nuevo Contrato',
            'contrato' => null,
            'reservas' => $contratoModel->getReservasDisponibles(),
            'estados_contrato' => $estados_contrato,
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

        $catalogoModel = new Catalogo();
        $estados_contrato = $catalogoModel->obtenerPorReferencia('ESTADO_CONTRATO');
        if (empty($estados_contrato)) {
            $estados_contrato = [
                ['codigo' => 'ESCO001', 'nombre' => 'Activo'],
                ['codigo' => 'ESCO002', 'nombre' => 'Finalizado'],
                ['codigo' => 'ESCO003', 'nombre' => 'Cancelado']
            ];
        }

        $data = [
            'titulo' => 'Editar Contrato',
            'contrato' => $contrato,
            'reservas' => $contratoModel->getReservasDisponibles(),
            'estados_contrato' => $estados_contrato,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/contrato/form', $data, 'admin');
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $multimedia_id = null;
            
            // Handle file upload — Azure Blob Storage
            if (!empty($_FILES['documento']['name'])) {
                $maxSize = 5 * 1024 * 1024; // 5MB
                
                if ($_FILES['documento']['error'] === UPLOAD_ERR_OK && $_FILES['documento']['size'] <= $maxSize) {
                    $ext = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'webp'])) {
                        $newName = 'contratos/' . uniqid('contrato_') . '_' . time() . '.' . $ext;
                        
                        $mimeType = 'application/octet-stream';
                        if (function_exists('mime_content_type')) {
                            $mimeType = mime_content_type($_FILES['documento']['tmp_name']);
                        }
                        if (!$mimeType) $mimeType = 'application/octet-stream';
                        
                        $azureUrl = \App\Core\AzureStorage::uploadFile($_FILES['documento']['tmp_name'], $newName, $mimeType);
                        
                        if ($azureUrl) {
                            $multimediaModel = new Multimedia();
                            $multimedia_id = $multimediaModel->create([
                                'url'            => $azureUrl,
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

                // Handle file upload to replace document — Azure Blob Storage
                if (!empty($_FILES['documento']['name'])) {
                    $maxSize = 5 * 1024 * 1024; // 5MB
                    
                    if ($_FILES['documento']['error'] === UPLOAD_ERR_OK && $_FILES['documento']['size'] <= $maxSize) {
                        $ext = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
                        if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'webp'])) {
                            $newName = 'contratos/' . uniqid('contrato_') . '_' . time() . '.' . $ext;
                            
                            $mimeType = 'application/octet-stream';
                            if (function_exists('mime_content_type')) {
                                $mimeType = mime_content_type($_FILES['documento']['tmp_name']);
                            }
                            if (!$mimeType) $mimeType = 'application/octet-stream';
                            
                            $azureUrl = \App\Core\AzureStorage::uploadFile($_FILES['documento']['tmp_name'], $newName, $mimeType);
                            
                            if ($azureUrl) {
                                $multimediaModel = new Multimedia();
                                
                                // Eliminar registro antiguo de BD si existe
                                if ($multimedia_id) {
                                    $multimediaModel->hardDelete($multimedia_id);
                                }

                                $multimedia_id = $multimediaModel->create([
                                    'url'            => $azureUrl,
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
