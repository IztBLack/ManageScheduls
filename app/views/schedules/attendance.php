<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
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

<div class="container-fluid mt-5 px-4">
    <!-- Navegación y Título -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/schedules">Mis Grupos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pase de Lista</li>
                </ol>
            </nav>
            <h1 class="display-5 text-dark">
                <i class="fa fa-users text-primary"></i> Asistencia Global
            </h1>
            <p class="text-muted lead mb-0">
                <?php echo htmlspecialchars($data['schedule']->subject_name); ?> - 
                <span class="badge badge-info"><?php echo htmlspecialchars($data['schedule']->grupo); ?></span>
            </p>
        </div>
        <div class="col-md-4 text-right">
            <a href="<?php echo URLROOT; ?>/schedules" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Volver a Mis Grupos
            </a>
        </div>
    </div>

    <?php flash('attendance_message'); ?>

    <div class="card shadow-sm border-0 mb-5">
        
        <?php if(!empty($data['all_dates']) || $data['editMode']) : ?>
        <!-- Barra de herramientas (solo si hay historial o estamos en modo edición) -->
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0 text-dark"><i class="fa fa-calendar-check-o"></i> 
                <?php echo $data['editMode'] ? 'Pase de Lista Activo' : 'Historial de Asistencia'; ?>
            </h5>
            
            <?php if($data['editMode']): ?>
                <form action="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $data['schedule']->id; ?>" method="GET" class="form-inline mt-2 mt-md-0">
                    <input type="hidden" name="edit" value="1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0">Fecha a capturar:</span>
                        </div>
                        <input type="date" name="fecha" class="form-control" value="<?php echo htmlspecialchars($data['fecha']); ?>" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Fijar Columna</button>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <a href="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $data['schedule']->id; ?>?edit=1" class="btn btn-warning mt-2 mt-md-0">
                    <i class="fa fa-edit"></i> Editar / Nueva Captura
                </a>
            <?php endif; ?>
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
                                        sort($fechas_mostrar); 
                                    }
                                    
                                    foreach($fechas_mostrar as $fecha_hist) : 
                                        $is_active_column = ($data['editMode'] && $fecha_hist == $data['fecha']);
                                        $fecha_format = date('d/M', strtotime($fecha_hist));
                                ?>
                                    <th class="align-middle <?php echo $is_active_column ? 'bg-primary border-primary' : ''; ?>" style="min-width: 80px;" title="<?php echo $fecha_hist; ?>">
                                        <?php if($is_active_column): ?>
                                            <i class="fa fa-pencil text-white"></i><br>
                                            <span class="text-white"><?php echo $fecha_format; ?></span>
                                        <?php else: ?>
                                            <small class="text-light"><?php echo $fecha_format; ?></small>
                                        <?php endif; ?>
                                    </th>
                                <?php endforeach; ?>

                                <!-- Columna Total -->
                                <th class="align-middle bg-info text-white" style="width: 10%;">
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
                                    <tr>
                                        <!-- Alumno Info -->
                                        <td class="align-middle font-weight-bold text-muted"><?php echo $index + 1; ?></td>
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
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
