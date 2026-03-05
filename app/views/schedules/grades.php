<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
/* ESTILOS PARA TABLA COMPACTA Y AJUSTADA AL CONTENIDO */

/* Contenedor principal - ocupa el ancho necesario */
.container-fluid {
    width: 100%;
    margin: 0 auto;
    padding: 15px;
}

/* El formulario ocupa el 100% del contenedor */
.container-fluid form {
    width: 100%;
    display: block;
}

/* La card se adapta al ancho del formulario */
.card {
    width: 100%;
}

/* Contenedor de la tabla con scroll */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    max-height: 70vh; /* Un poco más pequeño */
    width: 100%;
    border-radius: 4px;
}

/* TABLA CON ANCHO AJUSTADO AL CONTENIDO */
.table {
    width: auto; /* Cambiado de 100% a auto */
    min-width: 100%; /* Mínimo el ancho del contenedor */
    margin-bottom: 0;
    table-layout: auto; /* Las columnas se ajustan al contenido */
    border-collapse: collapse;
    white-space: nowrap; /* Evita saltos de línea innecesarios */
}

/* Permitir que el texto se rompa solo cuando sea necesario */
.table th, 
.table td {
    white-space: nowrap; /* Texto en una línea por defecto */
    padding: 0.5rem 0.4rem; /* Padding más compacto */
    text-align: center;
    vertical-align: middle;
}

/* Excepción para nombres largos de alumnos */
.table td:first-child {
    white-space: normal;
    min-width: 150px;
    max-width: 200px;
    word-wrap: break-word;
}

/* Columna de alumnos - fija */
.table th:first-child,
.table td:first-child {
    position: sticky;
    left: 0;
    background-color: #ffffff;
    z-index: 10;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    background-clip: padding-box;
}

/* Inputs que se ajustan al ancho del contenido */
.table td input.form-control-sm {
    width: 60px; /* Ancho fijo más pequeño */
    min-width: 50px;
    max-width: 70px;
    margin: 0 auto;
    padding: 0.2rem 0.1rem;
    font-size: 0.85rem;
}

/* Badges más pequeños */
.badge {
    font-size: 0.7rem !important;
    padding: 0.2rem 0.4rem !important;
}

/* Celdas de calificación y bonus más compactas */
.calif-unidad {
    min-width: 40px;
    padding: 0.5rem 0.2rem !important;
}

/* Ajuste para pantallas pequeñas */
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

/* Scrollbars personalizados */
.table-responsive::-webkit-scrollbar {
    height: 6px; /* Más delgado */
    width: 6px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
}

/* Hover en filas */
tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Inputs deshabilitados */
input:disabled {
    background-color: #e9ecef;
    opacity: 0.8;
    cursor: not-allowed;
}

/* Texto de ponderación más pequeño */
.badge-info {
    font-size: 0.65rem !important;
}

/* Encabezados de unidades más compactos */
.table th.bg-info {
    padding: 0.5rem 0.3rem;
    font-size: 0.9rem;
}

/* Celdas de calificación final */
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


<div class="container-fluid mt-3">
    <form action="<?php echo URLROOT; ?>/schedules/grades/<?php echo $data['schedule']->id; ?>" method="post">

        <div class="card shadow border-0">

            <!-- HEADER -->
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-2">
                <div>
                    <h5 class="mb-0">
                        Captura de Calificaciones:
                        <small>
                            <?php echo $data['schedule']->subject_name; ?>
                            | Grupo <?php echo $data['schedule']->grupo; ?>
                        </small>
                    </h5>
                </div>
                <div>
                    <span class="badge badge-warning p-2">
                        Suma de Pesos: <?php echo $totalPesos; ?>%
                    </span>
                </div>
            </div>

            <!-- TABLA CON UNIDADES - TOTALMENTE AJUSTADA AL CONTENIDO -->
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
                        <?php foreach ($data['students'] as $stu): ?>
                            <tr>
                                <td class="align-middle font-weight-bold text-left">
                                    <?php echo $stu->name; ?>
                                </td>

                                <!-- ACTIVIDADES Y CALIFICACIONES POR UNIDAD -->
                                <?php foreach ($data['actividadesPorUnidad'] as $unidad): ?>

                                    <!-- Inputs de actividades de esta unidad -->
                                    <?php foreach ($unidad['actividades'] as $actividad): ?>
                                        <?php
                                        $valor = $data['grades'][$stu->inscripcion_id][$actividad->id] ?? '';
                                        ?>
                                        <td>
                                            <input type="number"
                                                class="form-control form-control-sm nota text-center"
                                                data-weight="<?php echo $actividad->ponderacion; ?>"
                                                data-unidad-id="<?php echo $unidad['id']; ?>"
                                                name="calif[<?php echo $stu->inscripcion_id; ?>][<?php echo $actividad->id; ?>]"
                                                value="<?php echo $valor; ?>"
                                                min="0" max="100"
                                                step="1"
                                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                                <?php echo !$data['editMode'] ? 'disabled' : ''; ?>>
                                        </td>
                                    <?php endforeach; ?>

                                    <!-- Calificación de la unidad (calculada con JS) -->
                                    <td class="calif-unidad align-middle font-weight-bold"
                                        data-unidad-id="<?php echo $unidad['id']; ?>">
                                        0
                                    </td>

                                    <!-- Bonus de la unidad -->
                                    <td>
                                        <input type="number"
                                            class="form-control form-control-sm bonus-unidad text-center"
                                            data-unidad-id="<?php echo $unidad['id']; ?>"
                                            name="bonus[<?php echo $stu->inscripcion_id; ?>][<?php echo $unidad['id']; ?>]"
                                            value="<?php echo $data['bonusPorUnidad'][$stu->inscripcion_id][$unidad['id']] ?? 0; ?>"
                                            min="0" max="20"
                                            step="1"
                                            onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                            <?php echo !$data['editMode'] ? 'disabled' : ''; ?>>
                                    </td>
                                <?php endforeach; ?>

                                <!-- CALIFICACIÓN FINAL -->
                                <td class="final font-weight-bold text-white align-middle">
                                    0
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                    <!-- PIE DE TABLA CON PROMEDIO -->
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

            <div class="card-footer text-right">
                <?php if ($data['editMode']): ?>
                    <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                    <a href="<?php echo URLROOT; ?>/schedules/grades/<?php echo $data['schedule']->id; ?>" class="btn btn-secondary btn-sm">Cancelar</a>
                <?php else: ?>
                    <a href="<?php echo URLROOT; ?>/schedules/grades/<?php echo $data['schedule']->id; ?>?edit=1" class="btn btn-warning btn-sm">✏ Modificar</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener("input", function(e) {
        if (e.target.classList.contains("nota") ||
            e.target.classList.contains("bonus-unidad")) {
            calcularTodo();
        }
    });

    // Forzar enteros al salir del campo
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
                    notasPorUnidad[unidadId] = {
                        sumaPonderada: 0,
                        sumaPesos: 0
                    };
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
            calificacionesUnidad.forEach(calif => {
                sumaCalifUnidad += calif;
            });

            let promedioCalifUnidad = calificacionesUnidad.length > 0 ?
                sumaCalifUnidad / calificacionesUnidad.length : 0;

            let sumaBonuses = 0;
            bonuses.forEach(input => {
                sumaBonuses += parseFloat(input.value) || 0;
            });

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