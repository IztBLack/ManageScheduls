<?php require APPROOT . '/views/inc/header.php'; ?>

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
                    <span class="badge badge-primary py-2 px-3" style="font-size:0.95rem;">
                        <i class="fa fa-pencil mr-1"></i> Editando: <?php echo date('d/M/Y', strtotime($data['fecha'])); ?>
                    </span>
                <?php else: ?>
                    <?php if(!empty($data['all_dates'])) : ?>
                        <a href="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $data['schedule']->id; ?>?edit=1&fecha=<?php echo date('Y-m-d'); ?>" class="btn btn-warning btn-sm text-dark font-weight-bold">
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
                    <a href="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $data['schedule']->id; ?>?edit=1&fecha=<?php echo date('Y-m-d'); ?>" class="btn btn-success btn-lg">
                        <i class="fa fa-plus-circle"></i> Iniciar Control de Asistencia
                    </a>
                </div>

            <?php else: ?>

                <!-- CUADRÍCULA (Modo Edición o Modo Historial) -->
                <form action="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $data['schedule']->id; ?>" method="POST">
                <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($data['fecha']); ?>">
                
                <!-- Barra de búsqueda y filtros -->
                <div class="bg-light p-3 border-bottom d-flex align-items-center flex-wrap">
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
                            Z<i class="fas fa-arrow-up ml-1"></i>A
                        </button>
                    </div>
                </div>

                <div class="table-attendance-responsive p-0">
                    <table class="table table-hover table-bordered mb-0 table-sm text-center" style="font-size: 0.95rem;">
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
                                        $fecha_format = date('d/M/y', strtotime($fecha_hist));
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
                                                    <?php 
                                                        $val = strtolower($estado ?? 'presente');
                                                        $name = "attendance[".$student->inscripcion_id."]";
                                                    ?>
                                                    <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                                        <label class="btn btn-outline-success btn-sm flex-fill <?php echo ($val == 'presente') ? 'active' : ''; ?>" title="Presente">
                                                            <input type="radio" name="<?php echo $name; ?>" value="Presente" <?php echo ($val == 'presente') ? 'checked' : ''; ?>> P
                                                        </label>
                                                        <label class="btn btn-outline-danger btn-sm flex-fill <?php echo ($val == 'falta') ? 'active' : ''; ?>" title="Falta">
                                                            <input type="radio" name="<?php echo $name; ?>" value="Falta" <?php echo ($val == 'falta') ? 'checked' : ''; ?>> F
                                                        </label>
                                                        <label class="btn btn-outline-warning btn-sm flex-fill <?php echo ($val == 'retardo') ? 'active' : ''; ?>" title="Retardo">
                                                            <input type="radio" name="<?php echo $name; ?>" value="Retardo" <?php echo ($val == 'retardo') ? 'checked' : ''; ?>> R
                                                        </label>
                                                        <label class="btn btn-outline-info btn-sm flex-fill <?php echo ($val == 'justificado') ? 'active' : ''; ?>" title="Justificado">
                                                            <input type="radio" name="<?php echo $name; ?>" value="Justificado" <?php echo ($val == 'justificado') ? 'checked' : ''; ?>> J
                                                        </label>
                                                    </div>
                                                </td>
                                            <?php else : ?>
                                                <!-- CELDA HISTÓRICA (Solo lectura) -->
                                                <td class="align-middle">
                                                    <?php if(strtolower($estado) == 'presente'): ?>
                                                        <span class="badge badge-success" title="Presente">P</span>
                                                    <?php elseif(strtolower($estado) == 'falta' || strtolower($estado) == 'ausente'): ?>
                                                        <!-- Si se marcó como ausente explícitamente -->
                                                        <span class="badge badge-danger" title="Falta">F</span>
                                                    <?php elseif(strtolower($estado) == 'retardo'): ?>
                                                        <span class="badge badge-warning" title="Retardo">R</span>
                                                    <?php elseif(strtolower($estado) == 'justificado'): ?>
                                                        <span class="badge badge-info" title="Justificado">J</span>
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

<!-- Formulario Oculto para Eliminar Columna de Asistencia -->
<form id="delete-form" action="<?php echo URLROOT; ?>/schedules/deleteAttendanceDate/<?php echo $data['schedule']->id; ?>" method="POST" style="display: none;">
    <input type="hidden" name="fecha" id="delete-fecha-input">
</form>

<!-- SWEETALERT PARA CONFIRMACIÓN ELEGANTE -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
