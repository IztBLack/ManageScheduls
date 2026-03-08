<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
    .config-card { border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
    .config-header { background: linear-gradient(135deg, #343a40 0%, #23272b 100%); border-bottom: 3px solid #ffc107; text-align: left; }
    .unit-block {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
    }
    .unit-title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        gap: 10px;
    }
    .unit-name-input {
        max-width: 350px;
        flex: 1;
    }
</style>

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
                        <label>Materia <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-control" required>
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
                        <label>Identificador del Grupo <span class="text-danger">*</span></label>
                        <input type="text" name="grupo" class="form-control"
                            placeholder="Ej: 6BM" required
                            value="<?php echo $data['grupo'] ?? ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Especialidad / Carrera</label>
                        <select name="especialidad" class="form-control">
                            <option value="sistemas" <?php echo (($data['especialidad'] ?? '') == 'sistemas') ? 'selected' : ''; ?>>Sistemas Computacionales</option>
                            <option value="industrial" <?php echo (($data['especialidad'] ?? '') == 'industrial') ? 'selected' : ''; ?>>Ing. Industrial</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Periodo Escolar</label>
                        <input type="text" name="periodo" class="form-control"
                            value="<?php echo $data['periodo'] ?? 'AGO-DIC 2026'; ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Hora</label>
                        <input type="text" name="turno" class="form-control"
                            placeholder="Ej: 07:00 - 09:00"
                            value="<?php echo $data['turno'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Aula</label>
                        <input type="text" name="salon" class="form-control"
                            placeholder="Ej: Edificio A-10"
                            value="<?php echo $data['salon'] ?? $data['aula'] ?? ''; ?>">
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Estructura de Evaluación</h5>
                    <button type="button" id="addUnit" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Agregar Unidad
                    </button>
                </div>

                <div id="unitsContainer">
                    <div class="text-center text-muted py-4" id="emptyState">
                        <i class="fas fa-layer-group fa-2x mb-2 d-block" style="color:#dee2e6;"></i>
                        No hay unidades. Puedes agregar unidades ahora o configurarlas después desde la edición del grupo.
                    </div>
                </div>

                <div class="alert alert-info small mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Las ponderaciones de cada unidad deben sumar <strong>100%</strong> para un cálculo correcto.
                </div>

                <button type="submit" class="btn btn-success btn-block btn-lg mt-3 shadow-sm">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

<script>
(function () {
    let unitCount = 0;

    function activityRowHTML(unitIdx, actIdx) {
        return `
        <div class="activity-row row mb-2" data-activity="${actIdx}">
            <div class="col-md-5">
                <input type="text"
                    name="units[${unitIdx}][activities][${actIdx}][name]"
                    class="form-control form-control-sm"
                    placeholder="Nombre de la actividad">
            </div>
            <div class="col-md-3">
                <input type="number"
                    name="units[${unitIdx}][activities][${actIdx}][weight]"
                    class="form-control form-control-sm weight-input"
                    placeholder="Peso (%)" min="0" max="100" value="0">
            </div>
            <div class="col-md-3">
                <input type="date"
                    name="units[${unitIdx}][activities][${actIdx}][due_date]"
                    class="form-control form-control-sm">
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-danger btn-sm remove-activity">&times;</button>
            </div>
        </div>`;
    }

    function unitBlockHTML(unitIdx) {
        return `
        <div class="unit-block" data-unit="${unitIdx}">
            <div class="unit-title-bar">
                <div class="d-flex align-items-center flex-grow-1 mr-3">
                    <i class="fas fa-layer-group text-primary mr-2"></i>
                    <input type="text"
                        name="units[${unitIdx}][unit_name]"
                        class="form-control form-control-sm"
                        placeholder="Nombre de la unidad (Ej: Unidad 1 - Introducción)">
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge badge-secondary weight-total mr-2">Total: 0%</span>
                    <button type="button" class="btn btn-sm btn-outline-success add-activity mr-1">
                        <i class="fas fa-plus"></i> Actividad
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-unit">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="activities">
                ${activityRowHTML(unitIdx, 1)}
            </div>
        </div>`;
    }

    function updateWeightBadge(unitBlock) {
        let total = 0;
        unitBlock.querySelectorAll('.weight-input').forEach(i => {
            total += parseInt(i.value) || 0;
        });
        const badge = unitBlock.querySelector('.weight-total');
        badge.textContent = `Total: ${total}%`;
        badge.className   = 'badge mr-2 weight-total ' +
            (total === 100 ? 'badge-success' : total > 100 ? 'badge-danger' : 'badge-secondary');
    }

    function toggleEmptyState() {
        const hasUnits = document.querySelectorAll('.unit-block').length > 0;
        document.getElementById('emptyState').style.display = hasUnits ? 'none' : 'block';
    }

    document.getElementById('addUnit').addEventListener('click', function () {
        unitCount++;
        const container = document.getElementById('unitsContainer');
        const div = document.createElement('div');
        div.innerHTML = unitBlockHTML(unitCount);
        container.appendChild(div.firstElementChild);
        toggleEmptyState();
    });

    document.getElementById('unitsContainer').addEventListener('click', function (e) {

        if (e.target.closest('.add-activity')) {
            const unitBlock = e.target.closest('.unit-block');
            const unitIdx   = unitBlock.getAttribute('data-unit');
            const actIdx    = unitBlock.querySelectorAll('.activity-row').length + 1;
            const div = document.createElement('div');
            div.innerHTML = activityRowHTML(unitIdx, actIdx);
            unitBlock.querySelector('.activities').appendChild(div.firstElementChild);
        }

        if (e.target.classList.contains('remove-activity')) {
            const row       = e.target.closest('.activity-row');
            const unitBlock = row.closest('.unit-block');
            if (unitBlock.querySelectorAll('.activity-row').length > 1) {
                row.remove();
                updateWeightBadge(unitBlock);
            } else {
                alert('Cada unidad debe tener al menos una actividad.');
            }
        }

        if (e.target.closest('.remove-unit')) {
            const unitBlock = e.target.closest('.unit-block');
            if (confirm('¿Eliminar esta unidad y todas sus actividades?')) {
                unitBlock.remove();
                toggleEmptyState();
            }
        }
    });

    document.getElementById('unitsContainer').addEventListener('input', function (e) {
        if (e.target.classList.contains('weight-input')) {
            updateWeightBadge(e.target.closest('.unit-block'));
        }
    });

    toggleEmptyState();
})();
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>