<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
/* ESTILOS PARA TABLA COMPACTA Y AJUSTADA AL CONTENIDO */

.container-fluid {
    width: 100%;
    margin: 0 auto;
    padding: 15px;
}

.container-fluid form {
    width: 100%;
    display: block;
}

.card {
    width: 100%;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    max-height: 70vh;
    width: 100%;
    border-radius: 4px;
}

.table {
    width: auto;
    min-width: 100%;
    margin-bottom: 0;
    table-layout: auto;
    border-collapse: collapse;
    white-space: nowrap;
}

.table th,
.table td {
    white-space: nowrap;
    padding: 0.5rem 0.4rem;
    text-align: center;
    vertical-align: middle;
}

.table td:first-child {
    white-space: normal;
    min-width: 150px;
    max-width: 200px;
    word-wrap: break-word;
}

.table th:first-child,
.table td:first-child {
    position: sticky;
    left: 0;
    background-color: #ffffff;
    z-index: 10;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    background-clip: padding-box;
}

.table td input.form-control-sm {
    width: 60px;
    min-width: 50px;
    max-width: 70px;
    margin: 0 auto;
    padding: 0.2rem 0.1rem;
    font-size: 0.85rem;
}

.badge {
    font-size: 0.7rem !important;
    padding: 0.2rem 0.4rem !important;
}

.calif-unidad {
    min-width: 40px;
    padding: 0.5rem 0.2rem !important;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 10px 5px;
    }

    .table td input.form-control-sm {
        width: 45px;
        min-width: 40px;
        font-size: 0.75rem;
        padding: 0.15rem;
    }

    .table th,
    .table td {
        padding: 0.4rem 0.2rem;
    }
}

.table-responsive::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
}

tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

input:disabled {
    background-color: #e9ecef;
    opacity: 0.8;
    cursor: not-allowed;
}

.badge-info {
    font-size: 0.65rem !important;
}

.table th.bg-info {
    padding: 0.5rem 0.3rem;
    font-size: 0.9rem;
}

.final {
    min-width: 50px;
}
</style>

<?php
$totalPesos = 0;
foreach ($data['activities'] as $a) {
    $totalPesos += $a->ponderacion;
}

$totalUnidades = count($data['unidades']);
?>

<style>
    .config-card { border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
    .config-header { background: linear-gradient(135deg, #343a40 0%, #23272b 100%); border-bottom: 3px solid #ffc107; }
</style>

<div class="container-fluid mt-4">
    <form action="<?php echo URLROOT; ?>/schedules/grades/<?php echo $data['schedule']->id; ?>" method="post">

        <div class="card config-card mb-5">
            <!-- HEADER ESTILO CONFIGURACIÓN -->
            <div class="card-header config-header text-white d-flex justify-content-between align-items-center py-3 flex-wrap">
                <div>
                    <h3 class="mb-1"><i class="fas fa-edit mr-2"></i>Captura de Calificaciones</h3>
                    <div class="d-flex align-items-center mt-2 mt-md-0">
                        <span class="badge badge-light mr-2"><i class="fas fa-book mr-1"></i><?php echo $data['schedule']->subject_name; ?></span>
                        <span class="badge badge-warning mr-2"><i class="fas fa-users mr-1"></i>Grupo <?php echo $data['schedule']->grupo; ?></span>
                    </div>
                </div>
                
                <div class="mt-2 mt-md-0">
                    <span class="badge badge-info p-2" style="font-size: 0.9rem;">
                        <i class="fas fa-weight-hanging mr-1"></i> Suma de Pesos: <?php echo $totalPesos; ?>%
                    </span>
                </div>
            </div>

            <!-- Barra de búsqueda y filtros -->
            <div class="bg-light p-3 border-bottom d-flex align-items-center flex-wrap" style="gap:10px;">
                <div class="input-group input-group-sm flex-grow-1" style="max-width:320px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                    </div>
                    <input type="text" id="alumnoSearch"
                        class="form-control border-left-0"
                        placeholder="Buscar alumno..."
                        oninput="filtrarYOrdenar()">
                </div>
                <div class="btn-group btn-group-sm" id="sortBtns" role="group">
                    <button type="button" class="btn btn-outline-secondary sort-btn active"
                        data-sort="default" onclick="cambiarOrden(this, 'default')"
                        title="Orden original">
                        <i class="fas fa-list"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary sort-btn"
                        data-sort="asc" onclick="cambiarOrden(this, 'asc')"
                        title="A → Z">
                        A<i class="fas fa-arrow-down ml-1" style="font-size:0.7rem;"></i>Z
                    </button>
                    <button type="button" class="btn btn-outline-secondary sort-btn"
                        data-sort="desc" onclick="cambiarOrden(this, 'desc')"
                        title="Z → A">
                        Z<i class="fas fa-arrow-up ml-1" style="font-size:0.7rem;"></i>A
                    </button>
                </div>
            </div>

            <!-- TABLA -->
            <div class="table-responsive">
                <table class="table table-bordered text-center mb-0">

                    <thead class="thead-light">
                        <tr>
                            <th rowspan="2" class="align-middle" style="z-index: 20; min-width: 200px;">Alumno</th>

                            <?php foreach ($data['actividadesPorUnidad'] as $unidad): ?>
                                <th colspan="<?php echo count($unidad['actividades']); ?>" class="text-center bg-info text-white border-bottom-0">
                                    <?php echo htmlspecialchars($unidad['nombre']); ?>
                                </th>
                                <th rowspan="2" class="align-middle bg-secondary text-white">
                                    Calif.
                                </th>
                                <th rowspan="2" class="align-middle bg-warning">
                                    Bonus
                                </th>
                            <?php endforeach; ?>

                            <th rowspan="2" class="align-middle bg-primary text-white">
                                Final
                            </th>
                        </tr>

                        <tr>
                            <?php foreach ($data['actividadesPorUnidad'] as $unidad): ?>
                                <?php foreach ($unidad['actividades'] as $actividad): ?>
                                    <th class="align-middle bg-light">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="font-weight-bold" style="font-size: 0.75rem; display: block; max-width: 80px; overflow: hidden; text-overflow: ellipsis;">
                                                <?php echo htmlspecialchars($actividad->nombre); ?>
                                            </span>
                                            <span class="badge badge-info p-1" style="font-size: 0.65rem;">
                                                <?php echo $actividad->ponderacion; ?>%
                                            </span>
                                        </div>
                                    </th>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($data['students'] as $index => $stu): ?>
                            <tr class="student-item" data-index="<?php echo $index; ?>" data-name="<?php echo strtolower(htmlspecialchars($stu->name)); ?>">
                                <td class="align-middle font-weight-bold text-left index-col">
                                    <?php echo $stu->name; ?>
                                </td>

                                <?php foreach ($data['actividadesPorUnidad'] as $unidad): ?>
                                    <?php foreach ($unidad['actividades'] as $actividad): ?>
                                        <?php $valor = $data['grades'][$stu->inscripcion_id][$actividad->id] ?? ''; ?>
                                        <td>
                                            <input type="number"
                                                class="form-control form-control-sm nota text-center"
                                                data-weight="<?php echo $actividad->ponderacion; ?>"
                                                data-unidad-id="<?php echo $unidad['id']; ?>"
                                                name="calif[<?php echo $stu->inscripcion_id; ?>][<?php echo $actividad->id; ?>]"
                                                value="<?php echo $valor; ?>"
                                                min="0" max="100" step="1"
                                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                                <?php echo !$data['editMode'] ? 'disabled' : ''; ?>>
                                        </td>
                                    <?php endforeach; ?>

                                    <td class="calif-unidad align-middle font-weight-bold"
                                        data-unidad-id="<?php echo $unidad['id']; ?>">
                                        0
                                    </td>

                                    <td>
                                        <input type="number"
                                            class="form-control form-control-sm bonus-unidad text-center"
                                            data-unidad-id="<?php echo $unidad['id']; ?>"
                                            name="bonus[<?php echo $stu->inscripcion_id; ?>][<?php echo $unidad['id']; ?>]"
                                            value="<?php echo $data['bonusPorUnidad'][$stu->inscripcion_id][$unidad['id']] ?? 0; ?>"
                                            min="0" max="20" step="1"
                                            onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                            <?php echo !$data['editMode'] ? 'disabled' : ''; ?>>
                                    </td>
                                <?php endforeach; ?>

                                <td class="final font-weight-bold text-white align-middle">
                                    0
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="<?php echo count($data['activities']) + (count($data['unidades']) * 2) + 1; ?>" class="text-right">
                                Promedio:
                            </td>
                            <td id="promedioGrupo" class="text-primary">
                                0
                            </td>
                        </tr>
                    </tfoot>

                </table>
            </div>
            <!-- FOOTER -->
            <div class="card-footer bg-light p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>Última modificación: <?php echo date('d/m/Y'); ?>
                    </small>
                    <div>
                        <?php if ($data['editMode']): ?>
                            <a href="<?php echo URLROOT; ?>/schedules/grades/<?php echo $data['schedule']->id; ?>" class="btn btn-secondary mr-2">
                                <i class="fas fa-times mr-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-1"></i>Guardar Cambios
                            </button>
                        <?php else: ?>
                            <a href="<?php echo URLROOT; ?>/schedules/grades/<?php echo $data['schedule']->id; ?>?edit=1" class="btn btn-warning mr-2 text-dark font-weight-bold">
                                <i class="fas fa-edit mr-1"></i> Modo Edición
                            </a>
                            <a href="<?php echo URLROOT; ?>/schedules/index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a Mis Grupos
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // --- FILTRO Y ORDENAMIENTO DE ALUMNOS ---
    let sortOrder = 'default';

    function cambiarOrden(btn, orden) {
        sortOrder = orden;
        document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        filtrarYOrdenar();
    }

    function filtrarYOrdenar() {
        const query = (document.getElementById('alumnoSearch') ? document.getElementById('alumnoSearch').value : '').toLowerCase().trim();
        const tbody = document.querySelector('table tbody');
        if (!tbody) return;
        
        // Convertir Nodelist a un array para manipular
        const items = Array.from(tbody.querySelectorAll('tr.student-item'));
        if (items.length === 0) return;

        // 1. Filtrar visibilidad
        items.forEach(item => {
            const nombre = item.dataset.name || '';
            if (!query || nombre.includes(query)) {
                item.style.display = ''; // Mostrar
            } else {
                item.style.display = 'none'; // Ocultar
            }
        });

        // 2. Ordenar Nodos HTML
        const visiblesYListos = [...items]; 
        if (sortOrder === 'asc') {
            visiblesYListos.sort((a, b) => (a.dataset.name).localeCompare(b.dataset.name, 'es'));
        } else if (sortOrder === 'desc') {
            visiblesYListos.sort((a, b) => (b.dataset.name).localeCompare(a.dataset.name, 'es'));
        } else {
            // default: restaurar orden original por su atributo data-index original
            visiblesYListos.sort((a, b) => parseInt(a.dataset.index) - parseInt(b.dataset.index));
        }

        // Volver a insertar en el DOM en el orden correcto
        visiblesYListos.forEach(item => tbody.appendChild(item));
    }

    // --- CÁLCULO DE CALIFICACIONES ---
    document.addEventListener("input", function(e) {
        if (e.target.classList.contains("nota") ||
            e.target.classList.contains("bonus-unidad")) {
            calcularTodo();
        }
    });

    document.addEventListener('blur', function(e) {
        if (e.target.classList.contains("nota") || e.target.classList.contains("bonus-unidad")) {
            if (e.target.value !== '') {
                let valor = Math.round(parseFloat(e.target.value));
                let max = e.target.classList.contains('nota') ? 100 : 20;
                if (valor > max) valor = max;
                if (valor < 0) valor = 0;
                e.target.value = valor;
                calcularTodo();
            }
        }
    }, true);

    function calcularTodo() {
        let filas = document.querySelectorAll("tbody tr");
        let sumaGrupo = 0;
        let contador = 0;

        filas.forEach(row => {
            let notas = row.querySelectorAll(".nota");
            let notasPorUnidad = {};

            notas.forEach(input => {
                let unidadId = input.dataset.unidadId;
                let valor = parseFloat(input.value) || 0;
                let peso = parseFloat(input.dataset.weight);

                if (!notasPorUnidad[unidadId]) {
                    notasPorUnidad[unidadId] = { sumaPonderada: 0, sumaPesos: 0 };
                }

                notasPorUnidad[unidadId].sumaPonderada += valor * peso;
                notasPorUnidad[unidadId].sumaPesos += peso;
            });

            let bonuses = row.querySelectorAll(".bonus-unidad");
            let calificacionesUnidad = [];
            let celdasCalifUnidad = row.querySelectorAll(".calif-unidad");

            celdasCalifUnidad.forEach(celda => {
                let unidadId = celda.dataset.unidadId;
                if (notasPorUnidad[unidadId] && notasPorUnidad[unidadId].sumaPesos > 0) {
                    let califUnidad = notasPorUnidad[unidadId].sumaPonderada / notasPorUnidad[unidadId].sumaPesos;
                    celda.innerText = Math.round(califUnidad);
                    calificacionesUnidad.push(califUnidad);
                } else {
                    celda.innerText = "0";
                }
            });

            let sumaCalifUnidad = 0;
            calificacionesUnidad.forEach(calif => { sumaCalifUnidad += calif; });

            let promedioCalifUnidad = calificacionesUnidad.length > 0
                ? sumaCalifUnidad / calificacionesUnidad.length
                : 0;

            let sumaBonuses = 0;
            bonuses.forEach(input => { sumaBonuses += parseFloat(input.value) || 0; });

            let final = promedioCalifUnidad + sumaBonuses;
            let finalCell = row.querySelector(".final");
            finalCell.innerText = Math.round(final);

            finalCell.classList.remove("bg-success", "bg-warning", "bg-danger");
            if (final >= 90) {
                finalCell.classList.add("bg-success");
            } else if (final >= 70) {
                finalCell.classList.add("bg-warning");
            } else {
                finalCell.classList.add("bg-danger");
            }

            sumaGrupo += final;
            contador++;
        });

        let promedio = contador > 0 ? (sumaGrupo / contador) : 0;
        document.getElementById("promedioGrupo").innerText = Math.round(promedio);
    }

    window.addEventListener("load", calcularTodo);
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>