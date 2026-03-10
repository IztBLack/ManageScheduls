<?php require APPROOT . '/views/inc/header.php'; ?>


<div class="container mt-4 mb-5">
    <div class="card config-card">
        <!-- HEADER ESTILO CONFIGURACIÓN -->
        <div class="card-header config-header text-white d-flex justify-content-between align-items-center py-3 flex-wrap">
            <div>
                <h3 class="mb-1"><i class="fas fa-plus-circle mr-2"></i>Registrar Nuevo Grupo</h3>
               
            </div>
        </div>
        <div class="card-body">

            <!-- MENSAJES INLINE -->
            <?php if (!empty($data['error'])) : ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <?php echo $data['error']; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($data['warning'])) : ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <?php echo $data['warning']; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo URLROOT; ?>/schedules/add" method="post" id="addForm">

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="d-flex justify-content-between align-items-center" for="subjectSelect">
                            <span>Materia <span class="text-danger">*</span></span>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#addSubjectModal">
                                <i class="fas fa-plus"></i> Nueva Materia
                            </button>
                        </label>
                        <select name="subject_id" id="subjectSelect" class="form-control" required>
                            <option value="">Seleccione una materia...</option>
                            <?php if (!empty($data['subjects'])) : ?>
                                <?php foreach ($data['subjects'] as $subject) : ?>
                                    <option value="<?php echo $subject->id; ?>"
                                        <?php echo (isset($data['subject_id']) && $data['subject_id'] == $subject->id) ? 'selected' : ''; ?>>
                                        <?php echo $subject->subject_name; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="grupoInput">Identificador del Grupo <span class="text-danger">*</span></label>
                        <input type="text" id="grupoInput" name="grupo" class="form-control"
                            placeholder="Ej: 6BM" required
                            value="<?php echo $data['grupo'] ?? ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="d-flex justify-content-between align-items-center" for="institucionSelect" style="margin-bottom: 0.5rem;">
                            <span>Institución <span class="text-danger">*</span></span>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-toggle="modal" data-target="#addInstitucionModal" title="Nueva Institución">
                                <i class="fas fa-plus"></i> Nueva
                            </button>
                        </label>
                        <select id="institucionSelect" name="institucion" class="form-control" required>
                            <option value="">Seleccione Institución...</option>
                            <?php if (!empty($data['instituciones_existentes'])) : ?>
                                <?php foreach ($data['instituciones_existentes'] as $inst) : ?>
                                    <option value="<?php echo htmlspecialchars($inst); ?>"
                                        <?php echo (isset($data['institucion']) && $data['institucion'] == $inst) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($inst); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="d-flex justify-content-between align-items-center" for="especialidadSelect" style="margin-bottom: 0.5rem;">
                            <span>Especialidad (Carrera)</span>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-toggle="modal" data-target="#addEspecialidadModal" title="Nueva Especialidad">
                                <i class="fas fa-plus"></i> Nueva
                            </button>
                        </label>
                        <select id="especialidadSelect" name="especialidad" class="form-control">
                            <option value="">Seleccione Carrera...</option>
                            <?php if (!empty($data['especialidades_existentes'])) : ?>
                                <?php foreach ($data['especialidades_existentes'] as $esp) : ?>
                                    <option value="<?php echo htmlspecialchars($esp); ?>"
                                        <?php echo (isset($data['especialidad']) && $data['especialidad'] == $esp) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($esp); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="periodoInput">Periodo Escolar</label>
                        <input type="text" id="periodoInput" name="periodo" class="form-control"
                            placeholder="Ej: AGOSTO-DICIEMBRE 2026" required
                            value="<?php echo $data['periodo'] ?? 'AGOSTO-DICIEMBRE 2026'; ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="turnoInput">Hora</label>
                        <input type="text" id="turnoInput" name="turno" class="form-control"
                            placeholder="Ej: 14-15" pattern="^([01]?[0-9]|2[0-3])-([01]?[0-9]|2[0-3])$"
                            title="Formato 24h numérico, ej. 14-15"
                            value="<?php echo htmlspecialchars($data['turno'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="salonInput">Aula</label>
                        <input type="text" id="salonInput" name="salon" class="form-control"
                            placeholder="Ej: Edificio A-10"
                            value="<?php echo $data['salon'] ?? $data['aula'] ?? ''; ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-block btn-lg mt-4 shadow-sm">
                    <i class="fas fa-save mr-1"></i> Crear Espacio de Trabajo
                </button>

            </form>
        </div>

        <!-- FOOTER -->
        <div class="card-footer bg-light p-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Completa los datos para crear un nuevo tablero de alumnos y calificaciones.</small>
                <a href="<?php echo URLROOT; ?>/schedules" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Volver a Mis Grupos
                </a>
            </div>
        </div>

    </div>
</div>

<!-- Modal para Nueva Materia -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Nueva Materia</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addSubjectForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="subjectNameInput">Nombre de la Materia</label>
                        <input type="text" id="subjectNameInput" name="subject_name" class="form-control" placeholder="Ej: Programación Web" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Materia</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Nueva Especialidad -->
<div class="modal fade" id="addEspecialidadModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Añadir Especialidad</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addEspecialidadForm">
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label for="especialidadNameInput">Nombre de la Carrera</label>
                        <input type="text" id="especialidadNameInput" class="form-control" placeholder="Ej: Ingeniería en..." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Especialidad</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Nueva Institucion -->
<div class="modal fade" id="addInstitucionModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Añadir Institución</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addInstitucionForm">
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label for="institucionNameInput">Nombre o Abreviatura</label>
                        <input type="text" id="institucionNameInput" class="form-control" placeholder="Ej: UNAM, TEC, etc." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Institución</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>