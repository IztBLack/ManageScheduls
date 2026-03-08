<?php require APPROOT . '/views/inc/header.php'; ?>
<style>
    /* ESTILOS PREMIUM */
    .config-card {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .config-header {
        background: linear-gradient(135deg, #343a40 0%, #23272b 100%) !important;
        border-bottom: 3px solid #ffc107;
        padding: 1.5rem;
    }

    .unit-section {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .unit-title {
        background-color: #f8f9fa;
        padding: 1rem;
        margin: 0;
        border-bottom: 1px solid #e9ecef;
        font-size: 1.1rem;
        font-weight: bold;
        color: #495057;
    }
</style>

<div class="container-fluid mt-4 mb-5">
    
    <div class="card config-card">
        <!-- HEADER ESTILO CONFIGURACIÓN -->
        <div class="card-header config-header text-white d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h3 class="mb-1"><i class="fas fa-chart-line mr-2"></i>Reporte de Calificaciones</h3>
                <div class="d-flex align-items-center mt-2 mt-md-0">
                    <span class="badge badge-light mr-2" style="font-size: 0.9rem;">
                        <i class="fas fa-book mr-1"></i><?php echo htmlspecialchars($data['schedule']->subject_name); ?>
                    </span>
                    <span class="badge badge-warning mr-2" style="font-size: 0.9rem;">
                        <i class="fas fa-users mr-1"></i>Grupo <?php echo htmlspecialchars($data['schedule']->grupo); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- CUERPO DEL REPORTE -->
        <div class="card-body p-4">
            
            <?php if (empty($data['unidades'])) : ?>
                <div class="alert alert-info rounded-pill shadow-sm border-0 text-center">
                    <i class="fas fa-info-circle mr-2"></i> Aún no se han configurado unidades de evaluación para este grupo.
                </div>
            <?php else: ?>
                
                <?php foreach($data['unidades'] as $unidad) : ?>
                    <div class="unit-section">
                        <h4 class="unit-title text-uppercase">
                            <i class="fas fa-layer-group text-primary mr-2"></i> <?php echo htmlspecialchars($unidad->nombre); ?>
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless mb-0">
                                <thead class="border-bottom">
                                    <tr class="text-muted text-uppercase" style="font-size: 0.85rem;">
                                        <th class="pl-4">Actividad</th>
                                        <th class="text-center">Peso</th>
                                        <th class="text-center">Calificación</th>
                                        <th class="text-right pr-4">Puntaje Obtenido</th>
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
                                                <td class="pl-4 font-weight-bold text-dark"><i class="fas fa-chevron-right text-muted mr-2" style="font-size:0.7rem;"></i> <?php echo htmlspecialchars($act->nombre); ?></td>
                                                <td class="text-center"><span class="badge badge-light border"><?php echo $act->ponderacion; ?>%</span></td>
                                                <td class="text-center text-primary font-weight-bold"><?php echo number_format($calificacion, 1); ?></td>
                                                <td class="text-right pr-4 font-weight-bold"><?php echo number_format($puntaje, 1); ?></td>
                                            </tr>
                                    <?php 
                                        endforeach;
                                    else:
                                    ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">No hay actividades registradas en esta unidad.</td>
                                        </tr>
                                    <?php endif; ?>
                                    
                                    <!-- Totales -->
                                    <tr class="bg-light border-top">
                                        <td colspan="3" class="pl-4 text-right">Subtotal Unidad:</td>
                                        <td class="text-right pr-4">
                                            <strong><?php echo number_format($subtotal, 1); ?></strong>
                                        </td>
                                    </tr>
                                    <?php
                                    $puntosBonus = $data['bonus'][$data['inscripcion_id']][$unidad->id] ?? 0;
                                    if ($puntosBonus > 0) :
                                    ?>
                                        <tr class="bg-light text-success">
                                            <td colspan="3" class="pl-4 text-right">Puntos Adicionales (Bonus):</td>
                                            <td class="text-right pr-4">
                                                <strong>+ <?php echo number_format($puntosBonus, 1); ?></strong>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="bg-white border-top">
                                        <td colspan="3" class="pl-4 text-right text-dark font-weight-bold" style="font-size: 1.1rem;">CALIFICACIÓN FINAL DE UNIDAD:</td>
                                        <td class="text-right pr-4 text-primary font-weight-bold" style="font-size: 1.2rem;">
                                            <?php 
                                            $finalGrade = min(100, $subtotal + $puntosBonus);
                                            ?>
                                            <?php echo number_format($finalGrade, 1); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
        
        <!-- FOOTER -->
        <div class="card-footer bg-light p-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="fas fa-user-graduate mr-1"></i> Visualización de Estudiante</small>
                <a href="<?php echo URLROOT; ?>/students/index" class="btn btn-secondary font-weight-bold rounded-pill px-4">
                    <i class="fas fa-arrow-left mr-2"></i> Volver a Mis Calificaciones
                </a>
            </div>
        </div>

    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
