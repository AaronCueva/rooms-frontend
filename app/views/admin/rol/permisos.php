<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <a href="/admin/roles" class="text-decoration-none text-secondary me-2"><i class="fas fa-arrow-left"></i></a>
            Permisos de Acceso: <span class="text-primary"><?php echo htmlspecialchars($rol['nombre']); ?></span>
        </h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-shield-alt me-2"></i>Asignación de Módulos (Menú Maestro)</h6>
            <p class="mb-0 text-muted small mt-1">Selecciona los módulos y sub-módulos a los que este rol tendrá acceso.</p>
        </div>
        <div class="card-body">
            <form action="/admin/roles/permisos/guardar" method="POST">
                <input type="hidden" name="rol_id" value="<?php echo $rol['rol_id']; ?>">
                
                <div class="row">
                    <?php if(!empty($menus_arbol)): ?>
                        <?php foreach($menus_arbol as $seccion): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 border-left-primary shadow-sm">
                                    <div class="card-body p-3">
                                        <!-- Padre (Sección) -->
                                        <div class="form-check fw-bold mb-2 border-bottom pb-2">
                                            <?php $checked = in_array($seccion['menu_maestro_id'], $menus_asignados) ? 'checked' : ''; ?>
                                            <input class="form-check-input seccion-checkbox" type="checkbox" 
                                                name="menus[]" 
                                                value="<?php echo $seccion['menu_maestro_id']; ?>" 
                                                id="menu_<?php echo $seccion['menu_maestro_id']; ?>" 
                                                <?php echo $checked; ?>>
                                            <label class="form-check-label text-primary" for="menu_<?php echo $seccion['menu_maestro_id']; ?>">
                                                <i class="<?php echo htmlspecialchars($seccion['icono']); ?> me-1"></i> 
                                                <?php echo htmlspecialchars($seccion['nombre']); ?>
                                            </label>
                                        </div>

                                        <!-- Hijos (Submenús) -->
                                        <?php if(!empty($seccion['hijos'])): ?>
                                            <div class="ms-4 mt-2">
                                                <?php foreach($seccion['hijos'] as $hijo): ?>
                                                    <div class="form-check mb-1">
                                                        <?php $checkedHijo = in_array($hijo['menu_maestro_id'], $menus_asignados) ? 'checked' : ''; ?>
                                                        <input class="form-check-input subseccion-checkbox" type="checkbox" 
                                                            name="menus[]" 
                                                            value="<?php echo $hijo['menu_maestro_id']; ?>" 
                                                            id="menu_<?php echo $hijo['menu_maestro_id']; ?>" 
                                                            data-padre="menu_<?php echo $seccion['menu_maestro_id']; ?>"
                                                            <?php echo $checkedHijo; ?>>
                                                        <label class="form-check-label" for="menu_<?php echo $hijo['menu_maestro_id']; ?>">
                                                            <?php echo htmlspecialchars($hijo['nombre']); ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-warning">No se encontraron menús configurados en el sistema (Menu Maestro).</div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="text-end mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary me-2" id="btnMarcarTodos">Marcar Todos</button>
                    <button type="button" class="btn btn-outline-secondary me-2" id="btnDesmarcarTodos">Desmarcar Todos</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar Permisos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Checkear todos
    document.getElementById('btnMarcarTodos').addEventListener('click', function() {
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = true);
    });

    // Desmarcar todos
    document.getElementById('btnDesmarcarTodos').addEventListener('click', function() {
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    });

    // Lógica para que si marco un hijo, se marque el padre automáticamente (ux)
    const hijos = document.querySelectorAll('.subseccion-checkbox');
    hijos.forEach(hijo => {
        hijo.addEventListener('change', function() {
            if(this.checked) {
                const padreId = this.getAttribute('data-padre');
                if(padreId) {
                    document.getElementById(padreId).checked = true;
                }
            }
        });
    });

    // Lógica opcional: Si desmarco el padre, desmarcar todos los hijos
    const padres = document.querySelectorAll('.seccion-checkbox');
    padres.forEach(padre => {
        padre.addEventListener('change', function() {
            if(!this.checked) {
                const id = this.id;
                document.querySelectorAll(`.subseccion-checkbox[data-padre="${id}"]`).forEach(hijo => {
                    hijo.checked = false;
                });
            }
        });
    });
});
</script>
