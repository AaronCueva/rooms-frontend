<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1"><?php echo htmlspecialchars($titulo ?? 'Blog / Guía del Universitario'); ?></h1>
            <p class="text-muted small mb-0">Canal editorial oficial, avisos normativos, tutoriales de mudanza y guías de supervivencia universitaria</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary fs-6 px-3 py-2 shadow-sm">
                <i class="fas fa-newspaper me-1"></i> <?php echo $total ?? count($articulos ?? []); ?> artículos
            </span>
            <button type="button" class="btn btn-primary shadow-sm" onclick="abrirModalCrearBlog()">
                <i class="fas fa-plus me-1"></i> Nuevo Artículo
            </button>
        </div>
    </div>

    <div class="card shadow mb-4 border-0 rounded-3">
        <div class="card-body">
            <form method="GET" action="/admin/blog" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold mb-1 text-secondary">Buscar Artículo</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="busqueda" class="form-control" placeholder="Título, palabras clave o contenido..." value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1 text-secondary">Estado de Publicación</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <?php if (!empty($estados)): ?>
                            <?php foreach ($estados as $est): ?>
                                <option value="<?php echo $est['codigo']; ?>" <?php echo (isset($filtros['estado']) && $filtros['estado'] == $est['codigo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($est['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="ESBL001" <?php echo (isset($filtros['estado']) && $filtros['estado'] === 'ESBL001') ? 'selected' : ''; ?>>Borrador</option>
                            <option value="ESBL002" <?php echo (isset($filtros['estado']) && $filtros['estado'] === 'ESBL002') ? 'selected' : ''; ?>>Publicado</option>
                            <option value="ESBL003" <?php echo (isset($filtros['estado']) && $filtros['estado'] === 'ESBL003') ? 'selected' : ''; ?>>Oculto</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="/admin/blog" class="btn btn-sm btn-outline-secondary flex-fill">
                        <i class="fas fa-times me-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4 border-0 rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table foro-table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-secondary small text-uppercase fw-bold">
                        <tr>
                            <th class="py-3 ps-4">Artículo / Título</th>
                            <th class="py-3">Autor</th>
                            <th class="py-3 text-center">Estado</th>
                            <th class="py-3 text-center">Fecha Publicación</th>
                            <th class="py-3 text-center pe-4" style="width: 140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if (!empty($articulos)): ?>
                            <?php foreach ($articulos as $item): 
                                $cod = $item['estado_codigo'] ?? '';
                                $badgeClass = 'bg-secondary';
                                if ($cod === 'ESBL002') $badgeClass = 'bg-success';
                                elseif ($cod === 'ESBL001') $badgeClass = 'bg-warning text-dark';
                                
                                $ini = !empty($item['nombres']) ? strtoupper(substr(trim($item['nombres']), 0, 1)) : 'A';
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="foro-icon rounded-circle bg-light d-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 40px; height: 40px;">
                                                <i class="fas fa-newspaper fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-6" title="<?php echo htmlspecialchars($item['titulo'] ?? ''); ?>">
                                                    <?php 
                                                    $tit = $item['titulo'] ?? 'Sin título';
                                                    echo htmlspecialchars(strlen($tit) > 65 ? substr($tit, 0, 65) . '...' : $tit); 
                                                    ?>
                                                </div>
                                                <div class="text-muted small">
                                                    ID: <code><?php echo substr($item['blog_id'], 0, 8); ?></code>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="autor-avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center small fw-bold" style="width: 32px; height: 32px;">
                                                <?php echo $ini; ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold small text-dark">
                                                    <?php echo htmlspecialchars(trim(($item['nombres'] ?? 'Admin') . ' ' . ($item['apellido_paterno'] ?? ''))); ?>
                                                </div>
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    <?php echo htmlspecialchars($item['correo'] ?? 'soporte@nidouniversitario.com'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $badgeClass; ?> px-3 py-2 fw-normal rounded-pill">
                                            <?php echo htmlspecialchars($item['estado_nombre'] ?? $cod); ?>
                                        </span>
                                    </td>
                                    <td class="text-center text-muted small">
                                        <?php if (!empty($item['fecha_publicacion'])): ?>
                                            <div><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($item['fecha_publicacion'])); ?></div>
                                            <div style="font-size: 0.75rem;"><?php echo date('H:i', strtotime($item['fecha_publicacion'])); ?> hrs</div>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border">Sin publicar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="acciones-group d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-accion" title="Ver previa de lectura" onclick="abrirModalVerBlog('<?php echo $item['blog_id']; ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-accion" title="Editar artículo" onclick="abrirModalEditarBlog('<?php echo $item['blog_id']; ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <?php if ($cod !== 'ESBL003'): ?>
                                                <form action="/admin/blog/cambiar-estado" method="POST" class="d-inline form-confirm" data-title="¿Ocultar artículo del Blog?" data-text="El artículo cambiará al estado Oculto (ESBL003) pero permanecerá en esta lista para que puedas republicarlo cuando desees." data-icon="question" data-confirm-text="Sí, ocultar">
                                                    <input type="hidden" name="id" value="<?php echo $item['blog_id']; ?>">
                                                    <input type="hidden" name="estado_codigo" value="ESBL003">
                                                    <button type="submit" class="btn btn-sm btn-outline-warning text-dark btn-accion" title="Ocultar artículo (pasar a Oculto)">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form action="/admin/blog/cambiar-estado" method="POST" class="d-inline form-confirm" data-title="¿Republicar artículo?" data-text="El artículo volverá al estado Publicado y será visible inmediatamente para toda la comunidad." data-icon="question" data-confirm-text="Sí, republicar">
                                                    <input type="hidden" name="id" value="<?php echo $item['blog_id']; ?>">
                                                    <input type="hidden" name="estado_codigo" value="ESBL002">
                                                    <button type="submit" class="btn btn-sm btn-outline-success btn-accion" title="Republicar artículo">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <form action="/admin/blog/eliminar" method="POST" class="d-inline form-confirm" data-title="¿Mover a la papelera?" data-text="El artículo se eliminará de esta lista de gestión. ¿Deseas proceder?" data-icon="warning" data-confirm-text="Sí, eliminar">
                                                <input type="hidden" name="id" value="<?php echo $item['blog_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-accion" title="Eliminar / Papelera">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-newspaper fa-3x mb-3 d-block opacity-25"></i>
                                    No se encontraron artículos en el Blog con los filtros seleccionados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (($total_paginas ?? 1) > 1): ?>
    <nav aria-label="Paginación del Blog">
        <ul class="pagination pagination-sm justify-content-center">
            <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($filtros, ['pagina' => $pagina - 1])); ?>">Anterior</a>
            </li>
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($filtros, ['pagina' => $i])); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($pagina >= $total_paginas) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($filtros, ['pagina' => $pagina + 1])); ?>">Siguiente</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<script>
function abrirModalVerBlog(id) {
    fetch('/admin/blog/ver-modal?id=' + id)
        .then(r => {
            if (!r.ok) throw new Error('Error de red');
            return r.text();
        })
        .then(html => {
            mostrarModalDinamico('📖 Guía del Universitario', html, 'modal-lg');
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'No se pudo cargar la vista previa del artículo.',
                confirmButtonColor: '#3085d6'
            });
        });
}

function abrirModalCrearBlog() {
    fetch('/admin/blog/crear-modal')
        .then(r => {
            if (!r.ok) throw new Error('Error de red');
            return r.text();
        })
        .then(html => {
            mostrarModalDinamico('✍️ Redactar Nuevo Artículo', html, 'modal-lg');
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'No se pudo cargar el formulario de redacción.',
                confirmButtonColor: '#3085d6'
            });
        });
}

function abrirModalEditarBlog(id) {
    fetch('/admin/blog/editar-modal?id=' + id)
        .then(r => {
            if (!r.ok) throw new Error('Error de red');
            return r.text();
        })
        .then(html => {
            mostrarModalDinamico('✏️ Editar Artículo', html, 'modal-lg');
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'No se pudo cargar el formulario de edición.',
                confirmButtonColor: '#3085d6'
            });
        });
}

function mostrarModalDinamico(titulo, contenidoHtml, tamanoClase = 'modal-lg') {
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div class="modal fade" tabindex="-1">
            <div class="modal-dialog ${tamanoClase} modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white py-3 px-4">
                        <h6 class="modal-title d-flex align-items-center gap-2 mb-0 fw-semibold">
                            ${titulo}
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    ${contenidoHtml}
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    const m = new bootstrap.Modal(modal.querySelector('.modal'));
    m.show();
    modal.querySelector('.modal').addEventListener('hidden.bs.modal', () => modal.remove());
}
</script>
