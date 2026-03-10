<?php require APPROOT . '/views/inc/header.php'; ?>

<?php
$totalPesos = 0;
foreach ($data['activities'] as $a) {
    $totalPesos += $a->ponderacion;
}

$totalUnidades = count($data['unidades']);
?>

<div class="grades-container mt-4">
    <form action="<?php echo URLROOT; ?>/schedules/grades/<?php echo $data['schedule']->id; ?>" method="post">

        <div class="card config-card mb-5">
            <!-- HEADER ESTILO CONFIGURACIÓN -->
            <div class="card-header config-header text-white d-flex justify-content-between align-items-center py-3 flex-wrap">
                <div>
                    <h3 class="mb-1"><i class="fas fa-edit mr-2"></i>Captura de Calificaciones</h3>
                    <div class="d-flex align-items-center mt-2 mt-md-0">
                        <span class="badge badge-light mr-2"><i class="fas fa-book mr-1"></i><?php echo $data['schedule']->subject_name; ?></span>
                        <span class="badge badge-warning mr-2"><i class="fas fa-users mr-1"></i>Grupo <?php echo $data['schedule']->grupo; ?></span>
                    </div>
                </div>
                
                <div class="mt-2 mt-md-0">
                    <span class="badge badge-info p-2" style="font-size: 0.9rem;">
                        <i class="fas fa-weight-hanging mr-1"></i> Suma de Pesos: <?php echo $totalPesos; ?>%
                    </span>
                </div>
            </div>

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

            <!-- TABLA -->
            <div class="table-attendance-responsive">
                <table class="table table-hover table-bordered grades-table mb-0 text-center" style="font-size: 0.95rem;">

                    <thead class="thead-light">
                        <tr>
                            <th rowspan="2" class="align-middle" style="z-index: 20; min-width: 200px;">Alumno</th>

                            <?php foreach ($data['actividadesPorUnidad'] as $unidad): ?>
                                <th colspan="<?php echo count($unidad['actividades']); ?>" class="text-center bg-info text-white border-bottom-0">
                                    <?php echo htmlspecialchars($unidad['nombre']); ?>
                                </th>
                                <th rowspan="2" class="align-middle bg-secondary text-white">
                                    Calif.
                                </th>
                                <th rowspan="2" class="align-middle bg-warning">
                                    Bonus
                                </th>
                            <?php endforeach; ?>

                            <th rowspan="2" class="align-middle bg-primary text-white">
                                Final
                            </th>
                        </tr>

                        <tr>
                            <?php foreach ($data['actividadesPorUnidad'] as $unidad): ?>
                                <?php foreach ($unidad['actividades'] as $actividad): ?>
                                    <th class="align-middle bg-light">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="font-weight-bold" style="font-size: 0.75rem; display: block; max-width: 80px; overflow: hidden; text-overflow: ellipsis;">
                                                <?php echo htmlspecialchars($actividad->nombre); ?>
                                            </span>
                                            <span class="badge badge-info p-1" style="font-size: 0.65rem;">
                                                <?php echo $actividad->ponderacion; ?>%
                                            </span>
                                        </div>
                                    </th>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($data['students'] as $index => $stu): ?>
                            <tr class="student-item" data-index="<?php echo $index; ?>" data-name="<?php echo strtolower(htmlspecialchars($stu->name)); ?>">
                                <td class="align-middle font-weight-bold text-left index-col">
                                    <?php echo $stu->name; ?>
                                </td>

                                <?php foreach ($data['actividadesPorUnidad'] as $unidad): ?>
                                    <?php foreach ($unidad['actividades'] as $actividad): ?>
                                        <?php $valor = $data['grades'][$stu->inscripcion_id][$actividad->id] ?? ''; ?>
                                        <td>
                                            <input type="number"
                                                class="form-control form-control-sm nota text-center"
                                                data-weight="<?php echo $actividad->ponderacion; ?>"
                                                data-unidad-id="<?php echo $unidad['id']; ?>"
                                                name="calif[<?php echo $stu->inscripcion_id; ?>][<?php echo $actividad->id; ?>]"
                                                value="<?php echo $valor; ?>"
                                                min="0" max="100" step="1"
                                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                                <?php echo !$data['editMode'] ? 'disabled' : ''; ?>>
                                        </td>
                                    <?php endforeach; ?>

                                    <td class="calif-unidad align-middle font-weight-bold"
                                        data-unidad-id="<?php echo $unidad['id']; ?>">
                                        0
                                    </td>

                                    <td>
                                        <input type="number"
                                            class="form-control form-control-sm bonus-unidad text-center"
                                            data-unidad-id="<?php echo $unidad['id']; ?>"
                                            name="bonus[<?php echo $stu->inscripcion_id; ?>][<?php echo $unidad['id']; ?>]"
                                            value="<?php echo $data['bonusPorUnidad'][$stu->inscripcion_id][$unidad['id']] ?? 0; ?>"
                                            min="0" max="20" step="1"
                                            onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                            <?php echo !$data['editMode'] ? 'disabled' : ''; ?>>
                                    </td>
                                <?php endforeach; ?>

                                <td class="final font-weight-bold text-white align-middle">
                                    0
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="<?php echo count($data['activities']) + (count($data['unidades']) * 2) + 1; ?>" class="text-right">
                                Promedio:
                            </td>
                            <td id="promedioGrupo" class="text-primary">
                                0
                            </td>
                        </tr>
                    </tfoot>

                </table>
            </div>
            <!-- FOOTER -->
            <div class="card-footer bg-light p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>Última modificación: <?php echo date('d/m/Y'); ?>
                    </small>
                    <div>
                        <?php if ($data['editMode']): ?>
                            <a href="<?php echo URLROOT; ?>/schedules/grades/<?php echo $data['schedule']->id; ?>" class="btn btn-secondary mr-2">
                                <i class="fas fa-times mr-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-1"></i>Guardar Cambios
                            </button>
                        <?php else: ?>
                            <a href="<?php echo URLROOT; ?>/schedules/grades/<?php echo $data['schedule']->id; ?>?edit=1" class="btn btn-warning mr-2 text-dark font-weight-bold">
                                <i class="fas fa-edit mr-1"></i> Modo Edición
                            </a>
                            <a href="<?php echo URLROOT; ?>/schedules/index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a Mis Grupos
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>