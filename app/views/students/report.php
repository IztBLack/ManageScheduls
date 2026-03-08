<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/students/index">Mis Calificaciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Reporte de Evaluación: <?php echo htmlspecialchars($data['schedule']->subject_name . ' - ' . $data['schedule']->grupo); ?></li>
        </ol>
    </nav>

    <?php foreach($data['unidades'] as $unidad) : ?>
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Evaluación: <?php echo htmlspecialchars($unidad->nombre); ?></h4>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Actividad</th>
                            <th class="text-center">Peso</th>
                            <th class="text-center">Calificación</th>
                            <th class="text-right">Puntaje Obtenido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $subtotal = 0;
                        if (!empty($unidad->actividades)) :
                            foreach ($unidad->actividades as $act) :
                                $calificacion = $data['grades'][$data['inscripcion_id']][$act->id] ?? 0;
                                $puntaje = ($calificacion * $act->ponderacion) / 100;
                                $subtotal += $puntaje;
                        ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($act->nombre); ?></td>
                                    <td class="text-center"><?php echo $act->ponderacion; ?>%</td>
                                    <td class="text-center"><?php echo number_format($calificacion, 1); ?></td>
                                    <td class="text-right"><?php echo number_format($puntaje, 1); ?></td>
                                </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay actividades registradas en esta unidad.</td>
                            </tr>
                        <?php endif; ?>
                        
                        <!-- Totales -->
                        <tr class="table-info">
                            <td colspan="3"><strong>Subtotal Unidad</strong></td>
                            <td class="text-right">
                                <strong><?php echo number_format($subtotal, 1); ?></strong>
                            </td>
                        </tr>
                        <?php
                        $puntosBonus = $data['bonus'][$data['inscripcion_id']][$unidad->id] ?? 0;
                        if ($puntosBonus > 0) :
                        ?>
                            <tr class="text-success">
                                <td colspan="3"><strong>Puntos Adicionales (Bonus)</strong></td>
                                <td class="text-right">
                                    <strong>+ <?php echo number_format($puntosBonus, 1); ?></strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr class="bg-primary text-white">
                            <td colspan="3"><strong>CALIFICACIÓN FINAL UNIDAD</strong></td>
                            <td class="text-right">
                                <?php 
                                $finalGrade = min(100, $subtotal + $puntosBonus);
                                ?>
                                <strong><?php echo number_format($finalGrade, 1); ?></strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($data['unidades'])) : ?>
        <div class="alert alert-info">Aún no se han configurado unidades de evaluación para este grupo.</div>
    <?php endif; ?>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
