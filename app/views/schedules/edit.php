<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between">
            <h4>Estructura de: <?php echo $data['schedule']->grupo; ?></h4>
            <span class="badge badge-pill badge-light">Materia: <?php echo $data['schedule']->subject_name; ?></span>
        </div>
        <div class="card-body">
            <?php if (!empty($data['unidades'])) : ?>
                <?php foreach($data['unidades'] as $unidad) : ?>
                <div class="unit-section mb-4 border-bottom pb-3">
                    <h5 class="text-primary"><?php echo $unidad->nombre; ?></h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Actividad</th>
                                <th>Ponderación</th>
                                <th>Fecha Límite</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($unidad->actividades)) : ?>
                                <?php foreach($unidad->actividades as $actividad) : ?>
                                <tr>
                                    <td><?php echo $actividad->nombre; ?></td>
                                    <td><strong><?php echo $actividad->ponderacion; ?>%</strong></td>
                                    <td><?php echo $actividad->fecha_entrega; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center text-muted">No hay actividades</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No se encontraron unidades para este grupo.</p>
            <?php endif; ?>
            <a href="<?php echo URLROOT; ?>/schedules" class="btn btn-secondary">Volver al Panel</a>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>