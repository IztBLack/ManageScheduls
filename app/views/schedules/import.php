<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/schedules">Mis Grupos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Importar Alumnos</li>
        </ol>
    </nav>

    <?php flash('schedule_message'); ?>

    <div class="card shadow border-0">
        <div class="card-header bg-info text-white py-3">
            <h4 class="mb-0"><i class="fa fa-file-excel-o"></i> Importar Lista de Alumnos</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <h5><i class="fa fa-info-circle text-info"></i> Instrucciones</h5>
                    <p class="text-muted small">Para que la autogestión sea exitosa, asegúrese de que su archivo cumpla con los siguientes requisitos:</p>
                    <ul class="small text-secondary">
                        <li>El archivo debe ser formato <strong>.CSV</strong> únicamente.</li>
                        <li>Debe contener: <strong>Matrícula y Nombre Completo</strong>.</li>
                        <li>No debe haber filas vacías entre los registros.</li>
                    </ul>
                    <div class="alert alert-light border">
                        <p class="small mb-2 text-muted">Ejemplo visual del CSV:</p>
                        <code class="small">21020001,Juan Perez<br>21020002,Maria Garcia</code>
                    </div>
                </div>

                <div class="col-md-7 border-left">
                    <div class="form-group">
                        <label><strong>Grupo Destino:</strong></label>
                        <input type="text" class="form-control" value="<?php echo $data['nombre_grupo'] ?? 'Grupo Seleccionado'; ?>" readonly>
                    </div>

                    <div class="form-group mt-4">
                        <label for="archivo_alumnos">1. Seleccionar archivo CSV:</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="input_archivo" accept=".csv">
                            <label class="custom-file-label" for="input_archivo" id="label_archivo">Seleccionar archivo...</label>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-info btn-block mt-3 shadow-sm font-weight-bold" onclick="procesarArchivoLocal()">
                        <i class="fa fa-eye"></i> Cargar Vista Previa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form action="<?php echo URLROOT; ?>/schedules/import" method="post">
        <input type="hidden" name="id_grupo" value="<?php echo $data['id_grupo']; ?>">

        <div class="card mt-5 shadow-sm border-0">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark"><i class="fa fa-users"></i> 2. Revisar y Editar Alumnos</h5>
                <span id="contador_badge" class="badge badge-primary">0 Alumnos detectados</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 25%;">Matrícula</th>
                            <th>Nombre Completo</th>
                            <th class="text-center" style="width: 15%;">Estado</th>
                            <th class="text-right" style="width: 10%;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo_tabla">
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="fa fa-arrow-up fa-2x mb-3 d-block"></i>
                                Cargue un archivo arriba para editar la lista aquí antes de guardar.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white text-right">
                <button type="submit" class="btn btn-info shadow-sm font-weight-bold px-5" id="btn_guardar" disabled>
                    <i class="fa fa-check-circle"></i> Procesar e Inscribir Alumnos
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function procesarArchivoLocal() {
    const fileInput = document.getElementById('input_archivo');
    const file = fileInput.files[0];
    
    if (!file) {
        alert("Por favor, seleccione un archivo .csv primero.");
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const contenido = e.target.result;
        const lineas = contenido.split(/\r?\n/);
        const cuerpo = document.getElementById('cuerpo_tabla');
        cuerpo.innerHTML = ""; 
        let contador = 0;

        lineas.forEach((linea) => {
            const datos = linea.split(',');
            if (datos.length >= 2 && datos[0].trim() !== "") {
                const matricula = datos[0].trim();
                const nombre = datos[1].trim();

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <input type="text" name="alumnos[${contador}][matricula]" 
                               class="form-control form-control-sm font-weight-bold" value="${matricula}" required>
                    </td>
                    <td>
                        <input type="text" name="alumnos[${contador}][nombre]" 
                               class="form-control form-control-sm" value="${nombre}" required>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-warning">Pendiente</span>
                    </td>
                    <td class="text-right">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove(); actualizarContador();">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                `;
                cuerpo.appendChild(tr);
                contador++;
            }
        });

        actualizarContador();
    };
    reader.readAsText(file);
}

function actualizarContador() {
    const filas = document.querySelectorAll('#cuerpo_tabla tr').length;
    // Si la tabla está vacía o solo tiene el mensaje inicial
    const tieneDatos = document.querySelectorAll('#cuerpo_tabla input').length > 0;
    
    document.getElementById('contador_badge').innerText = tieneDatos ? `${filas} Alumnos detectados` : `0 Alumnos detectados`;
    document.getElementById('btn_guardar').disabled = !tieneDatos;
}

// Mostrar nombre del archivo al seleccionar
document.getElementById('input_archivo').onchange = function () {
    document.getElementById('label_archivo').innerHTML = this.files[0].name;
};
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>