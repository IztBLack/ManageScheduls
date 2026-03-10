<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container mt-4 mb-5">
    <?php flash('schedule_message'); ?>

    <!-- HEADER ESTILO CONFIGURACIÓN -->
    <div class="config-header index-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h2 class="mb-1 font-weight-bold"><i class="fas fa-chalkboard-teacher mr-2 text-warning"></i> Mis Grupos</h2>
            <p class="mb-0 text-light opacity-75">Selecciona un grupo para gestionar asistencia, calificaciones y estructura.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="<?php echo URLROOT; ?>/schedules/add" class="btn btn-success btn-lg shadow-sm font-weight-bold">
                <i class="fas fa-plus-circle mr-1"></i> Crear Nuevo Grupo
            </a>
        </div>
    </div>

    <!-- GRID DE GRUPOS -->
    <div class="row">
        <?php if(!empty($data['schedules'])) : ?>
            <?php foreach($data['schedules'] as $schedule) : ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card feature-card h-100" 
                         style="cursor: pointer;" 
                         ondblclick="window.location.href='<?php echo URLROOT; ?>/schedules/grades/<?php echo $schedule->id; ?>'">
                        <i class="fas fa-book card-icon-bg"></i>
                        <div class="card-body pt-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title font-weight-bold text-primary mb-0" style="max-width: 75%; line-height: 1.3;">
                                    <?php echo $schedule->subject_name; ?>
                                </h5>
                                <span class="badge badge-info px-2 py-1 shadow-sm" style="font-size: 0.9rem;">
                                    <?php echo $schedule->grupo; ?>
                                </span>
                            </div>
                            <h6 class="card-subtitle mb-4 text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <?php echo $schedule->especialidad; ?>
                            </h6>
                            
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2 text-secondary"><i class="fas fa-clock mr-2" style="width: 20px; text-align: center;"></i> <strong><?php echo $schedule->turno; ?></strong></li>
                                <li class="mb-2 text-secondary"><i class="fas fa-door-open mr-2" style="width: 20px; text-align: center;"></i> <?php echo $schedule->aula; ?></li>
                                <li class="text-secondary"><i class="fas fa-calendar-alt mr-2" style="width: 20px; text-align: center;"></i> <?php echo $schedule->periodo; ?></li>
                            </ul>
                        </div>
                        <div class="card-footer bg-white border-top-0 pb-4 pt-0">
                            <hr class="mt-0 mb-3" style="opacity: 0.5;">
                            <div class="d-flex justify-content-between text-center">
                                <a href="<?php echo URLROOT; ?>/schedules/edit/<?php echo $schedule->id; ?>" class="btn btn-sm btn-light text-warning shadow-sm rounded-pill font-weight-bold px-3 transition-hover" title="Configurar Estructura">
                                    <i class="fas fa-cog"></i> <span class="d-none d-sm-inline ml-1">Ajustes</span>
                                </a>
                                <a href="<?php echo URLROOT; ?>/schedules/attendance/<?php echo $schedule->id; ?>" class="btn btn-sm btn-light text-info shadow-sm rounded-pill font-weight-bold px-3 transition-hover" title="Pase de Lista">
                                    <i class="fas fa-user-check"></i> <span class="d-none d-sm-inline ml-1">Listas</span>
                                </a>
                                <a href="<?php echo URLROOT; ?>/schedules/grades/<?php echo $schedule->id; ?>" class="btn btn-sm btn-primary shadow-sm rounded-pill font-weight-bold px-4 transition-hover" title="Capturar Calificaciones">
                                    <i class="fas fa-edit"></i> <span class="ml-1">Calificar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12 text-center py-5 mt-4">
                <i class="fas fa-folder-open fa-5x text-muted mb-4 opacity-50"></i>
                <h3 class="font-weight-bold text-secondary">No tienes grupos registrados aún</h3>
                <p class="text-muted lead">Para comenzar, crea un nuevo espacio de trabajo para este ciclo escolar.</p>
                <a href="<?php echo URLROOT; ?>/schedules/add" class="btn btn-outline-success mt-3">
                    <i class="fas fa-plus mr-1"></i> Crear mi primer Grupo
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>