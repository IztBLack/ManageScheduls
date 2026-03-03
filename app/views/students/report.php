<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/students/grades">Mis Calificaciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Reporte de Evaluación: Unidad 1</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Reporte de Evaluación: Unidad 1</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Actividad</th>
                        <th class="text-center">Peso</th>
                        <th class="text-center">Calificación</th>
                        <th class="text-right">Puntaje Obtenido</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Ejemplo de actividades -->
                    <tr>
                        <td>Examen Teórico (Fundamentos PHP)</td>
                        <td class="text-center">40%</td>
                        <td class="text-center">80</td>
                        <td class="text-right">32.0</td>
                    </tr>
                    <tr>
                        <td>Proyecto: CRUD con MySQL</td>
                        <td class="text-center">50%</td>
                        <td class="text-center">100</td>
                        <td class="text-right">50.0</td>
                    </tr>
                    <tr>
                        <td>Participación y Asistencia</td>
                        <td class="text-center">10%</td>
                        <td class="text-center">100</td>
                        <td class="text-right">10.0</td>
                    </tr>
                    
                    <!-- 
                    Comentario: Para implementar dinámicamente descomentar:
                    
                    Estructura esperada en $data['activities']:
                    [
                        [
                            'nombre' => 'Examen Teórico...',
                            'peso' => 40,
                            'calificacion' => 80,
                            'puntaje' => 32.0
                        ]
                    ]
                    -->
                    
                    <?php /*
                    <?php if (isset($data['activities']) && !empty($data['activities'])): ?>
                        <?php foreach ($data['activities'] as $activity): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($activity['nombre']); ?></td>
                                <td class="text-center"><?php echo $activity['peso']; ?>%</td>
                                <td class="text-center"><?php echo $activity['calificacion']; ?></td>
                                <td class="text-right"><?php echo number_format($activity['puntaje'], 1); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    */ ?>
                    
                    <!-- Totales -->
                    <tr class="table-info">
                        <td colspan="3"><strong>Subtotal Unidad</strong></td>
                        <td class="text-right">
                            <strong>
                                <?php echo isset($data['subtotal']) ? number_format($data['subtotal'], 1) : '92.0'; ?>
                            </strong>
                        </td>
                    </tr>
                    <tr class="text-info">
                        <td colspan="3"><strong>Puntos Adicionales (Bonus)</strong></td>
                        <td class="text-right">
                            <strong>
                                + <?php echo isset($data['bonus']) ? number_format($data['bonus'], 1) : '5.0'; ?>
                            </strong>
                        </td>
                    </tr>
                    <tr class="bg-primary text-white">
                        <td colspan="3"><strong>CALIFICACIÓN FINAL UNIDAD</strong></td>
                        <td class="text-right">
                            <strong>
                                <?php echo isset($data['final_grade']) ? number_format($data['final_grade'], 1) : '97.0'; ?>
                            </strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <h6><i class="fa fa-comment"></i> Retroalimentación del Docente:</h6>
            <p class="text-muted">
                <?php 
                if (isset($data['feedback'])) {
                    echo htmlspecialchars($data['feedback']);
                } else {
                    echo "Excelente manejo de la estructura de base de datos. El bonus se otorga por tu apoyo en el taller de programación.";
                }
                ?>
            </p>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
