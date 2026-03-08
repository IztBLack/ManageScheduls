<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
    .config-card { border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
    .config-header { background: linear-gradient(135deg, #343a40 0%, #23272b 100%); border-bottom: 3px solid #ffc107; }
    /* Estilos para mantener el encabezado y columnas fijas si crece mucho */
    .table-responsive {
        max-height: 600px;
        overflow: auto;
    }
    .table thead th {
        position: sticky;
        top: 0;
        background-color: #343a40;
        z-index: 2;
    }
    /* El checkbox más grande */
    .huge-checkbox {
        transform: scale(1.5);
        cursor: pointer;
    }
</style>

<div class="container-fluid mt-4">
    <?php flash('attendance_message'); ?>

    <div class="card config-card mb-5">
        
        <!-- HEADER ESTILO CONFIGURACIÓN -->
        <div class="card-header config-header text-white d-flex justify-content-between align-items-center py-3 flex-wrap">
            <div>
                <h3 class="mb-1"><i class="fas fa-users mr-2"></i>Asistencia Global</h3>
                <div class="d-flex align-items-center mt-2 mt-md-0">
                    <span class="badge badge-light mr-2"><i class="fas fa-book mr-1"></i><?php echo $data['schedule']->subject_name; ?></span>
                    <span class="badge badge-warning"><i class="fas fa-users mr-1"></i>Grupo <?php echo $data['schedule']->grupo; ?></span>
                </div>
            </div>
            
            <div class="mt-2 mt-md-0 d-flex align-items-center flex-wrap" style="gap: 10px;">
                <!-- Barra de herramientas según modo -->
                <?php if($data['editMode']): ?>
                    <form action="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $data['schedule']->id; ?>" method="GET" class="form-inline m-0">
                        <input type="hidden" name="edit" value="1">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0 text-dark">Fecha a capturar:</span>
                            </div>
                            <input type="date" name="fecha" class="form-control" value="<?php echo htmlspecialchars($data['fecha']); ?>" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Fijar Columna</button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <?php if(!empty($data['all_dates'])) : ?>
                        <a href="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $data['schedule']->id; ?>?edit=1" class="btn btn-warning btn-sm text-dark font-weight-bold">
                            <i class="fa fa-edit"></i> Editar / Nueva Captura
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="card-body p-0">
            <!-- Barra de estado (opcional) -->
            <?php if(!empty($data['all_dates']) || $data['editMode']) : ?>
                <div class="bg-light p-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <span class="text-muted font-weight-bold">
                        <i class="fa fa-calendar-check-o text-success mr-1"></i> 
                        <?php echo $data['editMode'] ? 'Modo: Captura Activa' : 'Modo: Historial (Solo lectura)'; ?>
                    </span>
                </div>
            <?php endif; ?>


        <div class="card-body p-0">
            <?php if(empty($data['all_dates']) && !$data['editMode']): ?>
                
                <!-- ESTADO VACÍO (Sin historial y NO en edición) -->
                <div class="text-center py-5">
                    <i class="fa fa-battery-empty fa-4x text-muted mb-3"></i>
                    <h3 class="text-secondary">Sin Registros Previos</h3>
                    <p class="text-muted mb-4">Aún no se ha pasado lista en este grupo.</p>
                    <a href="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $data['schedule']->id; ?>?edit=1" class="btn btn-success btn-lg">
                        <i class="fa fa-plus-circle"></i> Iniciar Control de Asistencia Hoy
                    </a>
                </div>

            <?php else: ?>

                <!-- CUADRÍCULA (Modo Edición o Modo Historial) -->
                <form action="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $data['schedule']->id; ?>" method="POST">
                <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($data['fecha']); ?>">
                
                <!-- Barra de búsqueda y filtros -->
                <div class="bg-light p-3 border-bottom d-flex align-items-center flex-wrap" style="gap:10px;">
                    <div class="input-group input-group-sm flex-grow-1" style="max-width:320px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" id="alumnoSearch"
                            class="form-control border-left-0"
                            placeholder="Buscar alumno..."
                            oninput="filtrarYOrdenar()">
                    </div>
                    <div class="btn-group btn-group-sm" id="sortBtns" role="group">
                        <button type="button" class="btn btn-outline-secondary sort-btn active"
                            data-sort="default" onclick="cambiarOrden(this, 'default')"
                            title="Orden original">
                            <i class="fas fa-list"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary sort-btn"
                            data-sort="asc" onclick="cambiarOrden(this, 'asc')"
                            title="A → Z">
                            A<i class="fas fa-arrow-down ml-1" style="font-size:0.7rem;"></i>Z
                        </button>
                        <button type="button" class="btn btn-outline-secondary sort-btn"
                            data-sort="desc" onclick="cambiarOrden(this, 'desc')"
                            title="Z → A">
                            Z<i class="fas fa-arrow-up ml-1" style="font-size:0.7rem;"></i>A
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped table-sm table-bordered mb-0 text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 5%;" class="align-middle">#</th>
                                <th style="width: 25%;" class="align-middle text-left">Nombre del Alumno</th>
                                
                                <!-- Columnas Históricas -->
                                <?php 
                                    $fechas_mostrar = $data['all_dates'];
                                    
                                    // Si estamos editando y la fecha no existe en el historial, la forzamos temporalmente
                                    if($data['editMode'] && !in_array($data['fecha'], $fechas_mostrar)){
                                        $fechas_mostrar[] = $data['fecha']; 
                                    }
                                    if(!empty($data['all_dates'])) {
                                        $fechas_mostrar = array_unique(array_merge($data['all_dates'], [$data['fecha']]));
                                        sort($fechas_mostrar); 
                                    }
                                    
                                    foreach($fechas_mostrar as $fecha_hist) : 
                                        $is_active_column = ($data['editMode'] && $fecha_hist == $data['fecha']);
                                        $fecha_format = date('d/M', strtotime($fecha_hist));
                                ?>
                                    <th class="align-middle <?php echo $is_active_column ? 'bg-primary border-primary' : ''; ?>" style="min-width: 90px;" title="<?php echo $fecha_hist; ?>">
                                        <?php if($is_active_column): ?>
                                            <i class="fa fa-pencil text-white mb-1"></i><br>
                                            <span class="text-white"><?php echo $fecha_format; ?></span>
                                        <?php else: ?>
                                            <div class="mb-1">
                                                <a href="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $data['schedule']->id; ?>?edit=1&fecha=<?php echo $fecha_hist; ?>" 
                                                   class="btn btn-sm btn-outline-light py-0 px-1 mr-1" title="Editar este día">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1" title="Eliminar este día"
                                                        onclick="confirmarEliminacion('<?php echo $fecha_hist; ?>', '<?php echo $fecha_format; ?>')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                            <small class="text-light"><?php echo $fecha_format; ?></small>
                                        <?php endif; ?>
                                    </th>
                                <?php endforeach; ?>

                                <!-- Columna Total -->
                                <th class="align-middle text-white" style="width: 10%;">
                                    <i class="fa fa-calculator"></i><br>Total Asist.
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['students'])) : ?>
                                <?php foreach($data['students'] as $index => $student) : ?>
                                    <?php 
                                        $total_asistencias = 0;
                                    ?>
                                    <tr class="student-item" data-index="<?php echo $index; ?>" data-name="<?php echo strtolower(htmlspecialchars($student->name)); ?>">
                                        <!-- Alumno Info -->
                                        <td class="align-middle font-weight-bold text-muted index-col"><?php echo $index + 1; ?></td>
                                        <td class="align-middle text-left text-nowrap">
                                            <span class="text-dark font-weight-500"><?php echo htmlspecialchars($student->name); ?></span>
                                        </td>
                                        
                                        <!-- Celdas de Fechas -->
                                        <?php foreach($fechas_mostrar as $fecha_hist) : ?>
                                            <?php 
                                                $is_active_column = ($data['editMode'] && $fecha_hist == $data['fecha']);
                                                // Buscar estado en el historial (si existe)
                                                $estado = isset($data['all_records'][$student->inscripcion_id][$fecha_hist]) 
                                                          ? $data['all_records'][$student->inscripcion_id][$fecha_hist] 
                                                          : null;
                                                
                                                // Sumar al total si está presente (incluso en la columna activa si ya estaba guardada)
                                                if($estado == 'presente') $total_asistencias++;
                                            ?>

                                            <?php if($is_active_column) : ?>
                                                <!-- CELDA ACTIVA (Editable) -->
                                                <td class="align-middle bg-light border-primary" style="border-width: 2px !important;">
                                                    <input type="hidden" name="attendance[<?php echo $student->inscripcion_id; ?>]" value="ausente">
                                                    
                                                    <?php 
                                                        $checked = ($estado === 'presente' || $estado === null) ? 'checked' : '';
                                                    ?>
                                                    <input type="checkbox" 
                                                           name="attendance[<?php echo $student->inscripcion_id; ?>]" 
                                                           value="presente" 
                                                           class="huge-checkbox text-primary"
                                                           <?php echo $checked; ?>>
                                                </td>
                                            <?php else : ?>
                                                <!-- CELDA HISTÓRICA (Solo lectura) -->
                                                <td class="align-middle">
                                                    <?php if($estado == 'presente'): ?>
                                                        <i class="fa fa-check text-success" title="Presente"></i>
                                                    <?php elseif($estado == 'ausente'): ?>
                                                        <!-- Si se marcó como ausente explícitamente -->
                                                        <i class="fa fa-times text-danger" title="Ausente"></i>
                                                    <?php elseif($estado == 'retardo'): ?>
                                                        <i class="fa fa-clock-o text-warning" title="Retardo"></i>
                                                    <?php else: ?>
                                                        <!-- Si no hay registro para este alumno puntual ese día -->
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                            
                                        <?php endforeach; ?>

                                        <!-- Total Asistencias del Alumno -->
                                        <td class="align-middle font-weight-bold <?php echo ($total_asistencias == 0) ? 'text-danger' : 'text-dark'; ?>">
                                            <?php echo $total_asistencias; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="100" class="text-center py-5 text-muted">
                                        <i class="fa fa-users fa-3x mb-3"></i>
                                        <h5>No se encontraron alumnos inscritos en este grupo.</h5>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(!empty($data['students'])) : ?>
                <div class="card-footer bg-light py-3 d-flex justify-content-between">
                    <small class="text-muted mt-2"><i class="fa fa-info-circle"></i> Los registros se actualizan al guardar.</small>
                    
                    <?php if($data['editMode']) : ?>
                        <div>
                            <a href="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $data['schedule']->id; ?>" class="btn btn-secondary mr-2">
                                <i class="fa fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success px-4 shadow-sm">
                                <i class="fa fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </form>
            
            <?php endif; ?> <!-- Cierre del if NOT Empty o EditMode -->
        </div>

        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Asistencia en tiempo real</small>
                <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href='<?php echo URLROOT; ?>/schedules'"><i class="fas fa-arrow-left mr-1"></i> Volver a Mis Grupos</button>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>

<!-- Formulario Oculto para Eliminar Columna de Asistencia -->
<form id="delete-form" action="<?php echo URLROOT; ?>/schedules/deleteAttendanceDate/<?php echo $data['schedule']->id; ?>" method="POST" style="display: none;">
    <input type="hidden" name="fecha" id="delete-fecha-input">
</form>

<!-- SWEETALERT PARA CONFIRMACIÓN ELEGANTE -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// --- BORRADO DE COLUMNAS ---
function confirmarEliminacion(fecha, fechaF) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Vas a eliminar permanentemente el pase de lista del " + fechaF + ".",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-trash"></i> Sí, eliminar columna',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-fecha-input').value = fecha;
            document.getElementById('delete-form').submit();
        }
    });
}

// --- FILTRO Y ORDENAMIENTO DE ALUMNOS ---
let sortOrder = 'default';

function cambiarOrden(btn, orden) {
    sortOrder = orden;
    document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filtrarYOrdenar();
}

function filtrarYOrdenar() {
    const query = (document.getElementById('alumnoSearch') ? document.getElementById('alumnoSearch').value : '').toLowerCase().trim();
    const tbody = document.querySelector('table tbody');
    if (!tbody) return;
    
    // Convertir Nodelist a un array para manipular
    const items = Array.from(tbody.querySelectorAll('tr.student-item'));
    if (items.length === 0) return;

    // 1. Filtrar visibilidad
    items.forEach(item => {
        const nombre = item.dataset.name || '';
        if (!query || nombre.includes(query)) {
            item.style.display = ''; // Mostrar
        } else {
            item.style.display = 'none'; // Ocultar
        }
    });

    // 2. Ordenar Nodos HTML
    const visiblesYListos = [...items]; 
    if (sortOrder === 'asc') {
        visiblesYListos.sort((a, b) => (a.dataset.name).localeCompare(b.dataset.name, 'es'));
    } else if (sortOrder === 'desc') {
        visiblesYListos.sort((a, b) => (b.dataset.name).localeCompare(a.dataset.name, 'es'));
    } else {
        // default: restaurar orden original por su atributo data-index original
        visiblesYListos.sort((a, b) => parseInt(a.dataset.index) - parseInt(b.dataset.index));
    }

    // Volver a insertar en el DOM en el orden correcto
    visiblesYListos.forEach(item => tbody.appendChild(item));
}
</script>
