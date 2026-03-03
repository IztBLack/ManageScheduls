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
                                    <option value="<?php echo $subject->id; ?>" <?php echo (isset($data['subject_id']) && $data['subject_id'] == $subject->id) ? 'selected' : ''; ?>><?php echo $subject->subject_name; ?></option>
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
                        <input type="text" name="turno" class="form-control"     value="<?php echo $data['turno'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Aula Sugerida:</label>
                        <input type="text" name="salon" class="form-control" placeholder="Ej: Edificio A-10" value="<?php echo $data['aula'] ?? $data['salon'] ?? ''; ?>">
                    </div>
                </div>
                <hr>
                <h5>Estructura de evaluación</h5>
                <div id="unitsContainer">
                    <div class="unit-block mb-4" data-unit="1">
                        <h6>Unidad 1</h6>
                        <div class="activities">
                            <div class="activity-row row mb-2" data-activity="1">
                                <div class="col-md-5">
                                    <input type="text" name="units[1][activities][1][name]" class="form-control" placeholder="Actividad evaluable">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="units[1][activities][1][weight]" class="form-control" placeholder="Peso (%)">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="units[1][activities][1][due_date]" class="form-control">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm remove-activity">&times;</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary add-activity mb-2">Agregar actividad</button>
                    </div>
                </div>
                <button type="button" id="addUnit" class="btn btn-outline-primary mb-3">Agregar unidad</button>

                <div class="alert alert-info border">
                    <i class="fa fa-info-circle"></i> Si deseas, puedes dejar esta parte en blanco y definirla más tarde.
                </div>
                <button type="submit" class="btn btn-success btn-block">Crear Espacio de Trabajo</button>

                <?php if($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="mt-4 alert alert-secondary">
                    <h5>DEBUG POST / DATA</h5>
                    <pre>POST: <?php echo print_r($_POST, true); ?></pre>
                    <pre>DATA: <?php echo print_r($data, true); ?></pre>
                </div>
                <?php endif; ?>

                <script>
                    (function() {
                        let unitCount = 1;

                        document.getElementById('addUnit').addEventListener('click', function() {
                            unitCount++;
                            const container = document.getElementById('unitsContainer');
                            const newUnit = container.querySelector('.unit-block').cloneNode(true);
                            newUnit.setAttribute('data-unit', unitCount);
                            newUnit.querySelector('h6').textContent = 'Unidad ' + unitCount;
                            newUnit.querySelectorAll('input').forEach(i => i.value = '');
                            newUnit.querySelectorAll('[name]').forEach(el => {
                                el.name = el.name.replace(/units\[\d+\]/, 'units[' + unitCount + ']');
                                el.name = el.name.replace(/activities\]\[\d+\]/, 'activities][1]');
                            });
                            container.appendChild(newUnit);
                        });

                        document.getElementById('unitsContainer').addEventListener('click', function(e) {
                            if (e.target.classList.contains('add-activity')) {
                                const unitBlock = e.target.closest('.unit-block');
                                const unitIdx = unitBlock.getAttribute('data-unit');
                                const activities = unitBlock.querySelector('.activities');
                                const last = activities.querySelectorAll('.activity-row').length;
                                const newAct = activities.querySelector('.activity-row').cloneNode(true);
                                const actIdx = last + 1;
                                newAct.setAttribute('data-activity', actIdx);
                                newAct.querySelectorAll('input').forEach(i => i.value = '');
                                newAct.querySelectorAll('[name]').forEach(el => {
                                    el.name = el.name.replace(/activities\]\[\d+\]/, 'activities][' + actIdx + ']');
                                    el.name = el.name.replace(/units\[\d+\]/, 'units[' + unitIdx + ']');
                                });
                                activities.appendChild(newAct);
                            }
                            if (e.target.classList.contains('remove-activity')) {
                                const row = e.target.closest('.activity-row');
                                if (row.parentNode.querySelectorAll('.activity-row').length > 1) {
                                    row.remove();
                                }
                            }
                        });
                    })();
                </script>
            </form>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>