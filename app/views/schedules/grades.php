<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container-fluid mt-5">
    <form action="<?php echo URLROOT; ?>/schedules/grades/<?php echo $data['schedule']->id; ?>" method="post">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">
                    Captura de calificaciones - <small><?php echo $data['schedule']->subject_name; ?> / Grupo <?php echo $data['schedule']->grupo; ?></small>
                </h4>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light text-center">
                        <tr>
                            <th class="align-middle">Alumno</th>
                            <?php if (!empty($data['activities'])) : ?>
                                <?php foreach($data['activities'] as $act) : ?>
                                    <th><?php echo $act->nombre; ?> (<?php echo $act->ponderacion; ?>%)</th>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <th class="text-muted">(no hay actividades definidas)</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['students'])) : ?>
                            <?php foreach($data['students'] as $stu) : ?>
                            <tr>
                                <td class="align-middle"><?php echo $stu->name; ?></td>
                                <?php foreach($data['activities'] as $act) : ?>
                                    <td>
                                        <input type="number" 
                                               name="calif[<?php echo $stu->id; ?>][<?php echo $act->id; ?>]" 
                                               class="form-control text-center" 
                                               min="0" max="100" step="0.1">
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?php echo max(1, count($data['activities']) + 1); ?>" class="text-center text-muted">
                                    No hay alumnos inscritos en este grupo.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar calificaciones</button>
            </div>
        </div>
    </form>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>