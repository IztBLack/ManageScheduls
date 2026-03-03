<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="container-fluid mt-5">
    <form action="<?php echo URLROOT; ?>/schedules/grades/<?php echo $data['schedule']->id; ?>" method="post">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Registro de Resultados - Unidad: <?php echo $data['current_unit']->nombre; ?></h4>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="thead-light text-center">
                        <tr>
                            <th class="align-middle">Alumno</th>
                            <?php foreach($data['actividades'] as $act) : ?>
                                <th><?php echo $act->nombre; ?> (<?php echo $act->ponderacion; ?>%)</th>
                            <?php endforeach; ?>
                            <th class="align-middle text-info">Bonus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['alumnos'] as $alumno) : ?>
                        <tr>
                            <td class="align-middle"><?php echo $alumno->name; ?></td>
                            <?php foreach($data['actividades'] as $act) : ?>
                                <td>
                                    <input type="number" 
                                           name="calif[<?php echo $alumno->id; ?>][<?php echo $act->id; ?>]" 
                                           class="form-control text-center" 
                                           min="0" max="100" step="0.1" required>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <input type="number" name="bonus[<?php echo $alumno->id; ?>]" class="form-control text-center" value="0" min="0" max="10">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success btn-lg">Guardar y Calcular Promedios</button>
            </div>
        </div>
    </form>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>