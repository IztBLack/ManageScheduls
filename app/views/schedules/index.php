<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container mt-5">
    <?php flash('schedule_message'); ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-4"><i class="fa fa-calendar-check-o"></i> Mis Grupos</h1>
            <p class="lead text-muted">Gestión de evaluación y seguimiento académico.</p>
        </div>
        <div>
            <a href="<?php echo URLROOT; ?>/schedules/add" class="btn btn-success btn-lg shadow-sm">
                <i class="fa fa-plus-circle"></i> Crear Nuevo Grupo
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 30%;">Materia / Carrera</th>
                            <th class="text-center">Grupo</th>
                            <th class="text-center">Aula</th>
                            <th class="text-center">Periodo</th>
                            <th class="text-right" style="width: 25%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['schedules'])) : ?>
                            <?php foreach($data['schedules'] as $schedule) : ?>
                            <tr>
                                <td class="align-middle">
                                    <h6 class="mb-0 text-primary"><?php echo $schedule->subject_name; ?></h6>
                                    <small class="text-muted text-uppercase"><?php echo $schedule->especialidad; ?></small>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-info p-2" style="font-size: 0.9rem;">
                                        <?php echo $schedule->grupo; ?>
                                    </span>
                                    <br><small class="text-muted"><?php echo $schedule->turno; ?></small>
                                </td>
                                <td class="text-center align-middle text-secondary font-weight-bold">
                                    <?php echo $schedule->aula; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="text-dark"><?php echo $schedule->periodo; ?></span>
                                </td>
                                <td class="text-right align-middle">
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo URLROOT; ?>/schedules/edit/<?php echo $schedule->id; ?>" 
                                           class="btn btn-outline-warning btn-sm" title="Configurar Evaluación">
                                            <i class="fa fa-cog"></i> Configurar
                                        </a>

                                        <a href="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $schedule->id; ?>" 
                                           class="btn btn-info btn-sm" title="Pase de Lista">
                                            <i class="fa fa-list"></i> Asistencia
                                        </a>

                                        <a href="<?php echo URLROOT; ?>/schedules/grades/<?php echo $schedule->id; ?>" 
                                           class="btn btn-primary btn-sm" title="Capturar Calificaciones">
                                            <i class="fa fa-edit"></i> Calificar
                                        </a>

                
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa fa-folder-open-o fa-3x mb-3"></i>
                                        <h4>No tienes grupos registrados aún</h4>
                                        <p>Comienza creando un nuevo grupo para gestionar tus unidades y evaluaciones.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>