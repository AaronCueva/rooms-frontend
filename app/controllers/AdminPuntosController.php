<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\PuntoMovimiento;
use App\Models\Referido;
use App\Models\Catalogo;

class AdminPuntosController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }

        // Verificar rol (permiso 1, 2 o rol administrador/social)
        $rol = $_SESSION['rol_id'] ?? 0;
        if (!in_array($rol, [1, 2, '125a6470-ce60-4302-945a-6c0b26fdaddf', '8a161c0e-684a-42c3-bddf-b03a61b9d726', '68a6b252-0545-4bb6-ad73-5a41fd2bb54c'])) {
            $this->redirect('/');
        }
    }

    public function index()
    {
        $tab = $_GET['tab'] ?? 'leaderboard';
        $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $por_pagina = 8;

        $pmModel = new PuntoMovimiento();
        $refModel = new Referido();
        $catalogoModel = new Catalogo();

        $data = [
            'tab' => $tab,
            'pagina' => $pagina,
            'por_pagina' => $por_pagina,
            'filtros' => $_GET,
            'estudiantes_lista' => $pmModel->obtenerEstudiantesLista()
        ];

        if ($tab === 'leaderboard') {
            $filtros = [
                'busqueda' => trim($_GET['busqueda'] ?? ''),
                'nivel' => $_GET['nivel'] ?? ''
            ];
            $data['leaderboard'] = $pmModel->obtenerLeaderboard($filtros, $pagina, $por_pagina);
            $total_registros = $pmModel->contarLeaderboard($filtros);
            $data['total_registros'] = $total_registros;
            $data['total_paginas'] = ceil($total_registros / $por_pagina);
        } elseif ($tab === 'movimientos') {
            $filtros = [
                'busqueda' => trim($_GET['busqueda'] ?? ''),
                'tipo_movimiento' => $_GET['tipo_movimiento'] ?? ''
            ];
            $data['movimientos'] = $pmModel->buscar($filtros, $pagina, $por_pagina);
            $total_registros = $pmModel->contar($filtros);
            $data['total_registros'] = $total_registros;
            $data['total_paginas'] = ceil($total_registros / $por_pagina);
            $data['tipos_movimiento'] = $catalogoModel->obtenerPorReferencia('TIPO_MOVIMIENTO_PUNTO');
        } elseif ($tab === 'referidos') {
            $filtros = [
                'busqueda' => trim($_GET['busqueda'] ?? ''),
                'estado' => $_GET['estado'] ?? ''
            ];
            $data['referidos'] = $refModel->buscar($filtros, $pagina, $por_pagina);
            $total_registros = $refModel->contar($filtros);
            $data['total_registros'] = $total_registros;
            $data['total_paginas'] = ceil($total_registros / $por_pagina);
            $data['estados_referido'] = $catalogoModel->obtenerPorReferencia('ESTADO_REFERIDO');
        }

        $this->render('admin/puntos/index', $data, 'admin');
    }

    /**
     * POST: Ajuste manual de puntos por parte del Administrador
     */
    public function ajusteManual()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_POST['usuario_id'] ?? null;
            $operacion = $_POST['operacion'] ?? 'suma';
            $puntos_raw = abs((int)($_POST['puntos'] ?? 0));
            $motivo = trim($_POST['motivo'] ?? '');

            if ($usuario_id && $puntos_raw > 0 && !empty($motivo)) {
                $puntos_calc = ($operacion === 'resta') ? -1 * $puntos_raw : $puntos_raw;
                $user = $_SESSION['correo'] ?? $_SESSION['nombres'] ?? 'Admin';
                
                $desc_completa = "[AJUSTE MANUAL] " . $motivo . " (" . strtoupper($operacion) . " por " . $user . ")";
                
                $pmModel = new PuntoMovimiento();
                if ($pmModel->registrarMovimiento($usuario_id, 'TMPT006', $puntos_calc, $desc_completa, $user)) {
                    $this->setFlash('success', "Se han " . ($operacion === 'resta' ? 'debitedo' : 'acreditado') . " $puntos_raw puntos NIDO correctamente.");
                } else {
                    $this->setFlash('error', "No se pudo realizar el ajuste de puntos en la base de datos.");
                }
            } else {
                $this->setFlash('error', "Por favor completa todos los campos requeridos (usuario, puntos y motivo justificado).");
            }
        }
        $this->redirect('/admin/puntos?tab=leaderboard');
    }

    /**
     * POST: Acreditar bonificación de referido (cambiar estado a Acreditado y dar +200 y +100 puntos)
     */
    public function acreditarReferido()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $referido_id = $_POST['referido_id'] ?? null;
            if ($referido_id) {
                $refModel = new Referido();
                $ref = $refModel->findById($referido_id);
                
                if ($ref && $ref['estado_codigo'] !== 'ESREF02') {
                    $user = $_SESSION['correo'] ?? $_SESSION['nombres'] ?? 'Admin';
                    if ($refModel->cambiarEstado($referido_id, 'ESREF02', $user)) {
                        $pmModel = new PuntoMovimiento();
                        
                        // +200 al referidor
                        if (!empty($ref['usuario_referidor_id'])) {
                            $pmModel->registrarMovimiento(
                                $ref['usuario_referidor_id'], 
                                'TMPT001', 
                                200, 
                                "Bonificación acreditada por invitación exitosa: " . ($ref['referido_nombres'] ?? $ref['codigo']), 
                                $user, 
                                $referido_id
                            );
                        }
                        // +100 al referido (bono bienvenida por código)
                        if (!empty($ref['usuario_referido_id'])) {
                            $pmModel->registrarMovimiento(
                                $ref['usuario_referido_id'], 
                                'TMPT007', 
                                100, 
                                "Bono de bienvenida por registro con código de referido: " . $ref['codigo'], 
                                $user, 
                                $referido_id
                            );
                        }

                        $this->setFlash('success', "¡Bonificación acreditada con éxito! Se sumaron +200 pts al referidor y +100 pts al invitado.");
                    } else {
                        $this->setFlash('error', "No se pudo actualizar el estado del referido.");
                    }
                } else {
                    $this->setFlash('warning', "Esta invitación ya se encontraba acreditada previamente o no existe.");
                }
            }
        }
        $this->redirect('/admin/puntos?tab=referidos');
    }

    /**
     * POST: Anular o cancelar un referido (sospecha de fraude)
     */
    public function anularReferido()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $referido_id = $_POST['referido_id'] ?? null;
            if ($referido_id) {
                $refModel = new Referido();
                $user = $_SESSION['correo'] ?? $_SESSION['nombres'] ?? 'Admin';
                if ($refModel->cambiarEstado($referido_id, 'ESREF03', $user)) {
                    $this->setFlash('success', "La relación de referido ha sido anulada/cancelada correctamente.");
                } else {
                    $this->setFlash('error', "No se pudo anular el referido.");
                }
            }
        }
        $this->redirect('/admin/puntos?tab=referidos');
    }

    /**
     * GET (o POST): Modal para ver historial Ledger de un estudiante
     */
    public function ledgerModal()
    {
        $usuario_id = $_GET['usuario_id'] ?? null;
        if (!$usuario_id) {
            echo "<div class='p-4 text-center text-danger'>ID de usuario no proporcionado.</div>";
            return;
        }

        $pmModel = new PuntoMovimiento();
        $movimientos = $pmModel->getLedgerByUsuario($usuario_id);

        $this->render('admin/puntos/ledger_modal', ['movimientos' => $movimientos], '');
    }
}
