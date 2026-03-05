<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4><i class="fa fa-plus-circle"></i> Registrar Nuevo Grupo</h4>
        </div>
        <div class="card-body">
            <form action="<?php echo URLROOT; ?>/schedules/add" method="post">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Materia:</label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">Seleccione una materia...</option>
                            <?php if (!empty($data['subjects'])) : ?>
                                <?php foreach($data['subjects'] as $subject): ?>
                                    <option value="<?php echo $subject->id; ?>" <?php echo (isset($data['subject_id']) && $data['subject_id'] == $subject->id) ? 'selected' : ''; ?>>
                                        <?php echo $subject->subject_name; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Identificador del Grupo:</label>
                        <input type="text" name="grupo" class="form-control" placeholder="Ej: 6BM" required value="<?php echo $data['grupo'] ?? ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Especialidad / Carrera:</label>
                        <select name="especialidad" class="form-control">
                            <option value="sistemas" <?php echo (isset($data['especialidad']) && $data['especialidad']=='sistemas')?'selected':''; ?>>Sistemas Computacionales</option>
                            <option value="industrial" <?php echo (isset($data['especialidad']) && $data['especialidad']=='industrial')?'selected':''; ?>>Ing. Industrial</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Periodo Escolar:</label>
                        <input type="text" name="periodo" class="form-control" value="<?php echo $data['periodo'] ?? 'AGO-DIC 2026'; ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Hora:</label>
                        <input type="text" name="turno" class="form-control" placeholder="Ej: 07:00 - 09:00" value="<?php echo $data['turno'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Aula Sugerida:</label>
                        <input type="text" name="salon" class="form-control" placeholder="Ej: Edificio A-10" value="<?php echo $data['aula'] ?? $data['salon'] ?? ''; ?>">
                    </div>
                </div>

                <hr>
                <h5>Estructura de evaluación</h5>
                <div id="unitsContainer">
                    <div class="unit-block mb-4 border p-3 bg-light rounded" data-unit="1">
                        <h6>Unidad 1</h6>
                        <div class="activities">
                            <div class="activity-row row mb-2" data-activity="1">
                                <div class="col-md-5">
                                    <input type="text" name="units[1][activities][1][name]" class="form-control" placeholder="Actividad evaluable">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="units[1][activities][1][weight]" class="form-control" placeholder="Peso (%)" min="1" max="100">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="units[1][activities][1][due_date]" class="form-control">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm remove-activity">&times;</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary add-activity mb-2">
                            <i class="fa fa-plus"></i> Agregar actividad
                        </button>
                    </div>
                </div>

                <button type="button" id="addUnit" class="btn btn-outline-primary mb-3">
                    <i class="fa fa-layer-group"></i> Agregar unidad
                </button>

                <div class="alert alert-info border small">
                    <i class="fa fa-info-circle"></i> Las ponderaciones de cada unidad deben sumar <strong>100%</strong> para evitar errores en el cálculo final.
                </div>

                <button type="submit" class="btn btn-success btn-block btn-lg shadow">
                    Crear Espacio de Trabajo
                </button>

                <?php if($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="mt-4 alert alert-secondary small">
                    <h6><i class="fa fa-bug"></i> DEBUG DATA</h6>
                    <pre><?php print_r($_POST); ?></pre>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        let unitCount = 1;

        // Agregar Nueva Unidad
        document.getElementById('addUnit').addEventListener('click', function() {
            unitCount++;
            const container = document.getElementById('unitsContainer');
            const newUnit = container.querySelector('.unit-block').cloneNode(true);
            
            newUnit.setAttribute('data-unit', unitCount);
            newUnit.querySelector('h6').textContent = 'Unidad ' + unitCount;
            newUnit.querySelectorAll('input').forEach(i => i.value = '');
            
            // Limpiar actividades extra para que solo quede una al clonar
            const activitiesDiv = newUnit.querySelector('.activities');
            const rows = activitiesDiv.querySelectorAll('.activity-row');
            for(let i = 1; i < rows.length; i++) rows[i].remove();

            // Actualizar nombres de inputs
            newUnit.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/units\[\d+\]/, 'units[' + unitCount + ']');
                el.name = el.name.replace(/activities\]\[\d+\]/, 'activities][1]');
            });
            
            container.appendChild(newUnit);
        });

        // Eventos Delegados para Actividades (Agregar/Eliminar)
        document.getElementById('unitsContainer').addEventListener('click', function(e) {
            // Agregar Actividad
            if (e.target.classList.contains('add-activity') || e.target.closest('.add-activity')) {
                const unitBlock = e.target.closest('.unit-block');
                const unitIdx = unitBlock.getAttribute('data-unit');
                const activitiesContainer = unitBlock.querySelector('.activities');
                const currentRows = activitiesContainer.querySelectorAll('.activity-row');
                
                const newAct = currentRows[0].cloneNode(true);
                const actIdx = currentRows.length + 1;
                
                newAct.setAttribute('data-activity', actIdx);
                newAct.querySelectorAll('input').forEach(i => i.value = '');
                
                newAct.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace(/units\[\d+\]/, 'units[' + unitIdx + ']');
                    el.name = el.name.replace(/activities\]\[\d+\]/, 'activities][' + actIdx + ']');
                });
                
                activitiesContainer.appendChild(newAct);
            }

            // Eliminar Actividad
            if (e.target.classList.contains('remove-activity')) {
                const row = e.target.closest('.activity-row');
                const parent = row.parentNode;
                if (parent.querySelectorAll('.activity-row').length > 1) {
                    row.remove();
                }
            }
        });
    })();
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>