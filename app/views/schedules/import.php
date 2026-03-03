<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/schedules">Mis Grupos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Importar Alumnos</li>
        </ol>
    </nav>

    <div class="card shadow border-0">
        <div class="card-header bg-info text-white py-3">
            <h4 class="mb-0"><i class="fa fa-file-excel-o"></i> Importar Lista de Alumnos</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <h5><i class="fa fa-info-circle text-info"></i> Instrucciones</h5>
                    <p class="text-muted">Para que la autogestión sea exitosa, asegúrese de que su archivo cumpla con los siguientes requisitos:</p>
                    <ul class="small text-secondary">
                        <li>El archivo debe ser formato <strong>.CSV</strong> o <strong>.XLSX</strong>.</li>
                        <li>Debe contener las columnas: <strong>Matrícula, Nombre y Apellidos</strong>.</li>
                        <li>No debe haber filas vacías entre los registros.</li>
                    </ul>
                    <div class="alert alert-light border">
                        <a href="#" class="btn btn-sm btn-block btn-outline-secondary">
                            <i class="fa fa-download"></i> Descargar Plantilla de Ejemplo
                        </a>
                    </div>
                </div>

                <div class="col-md-7 border-left">
                    <form action="<?php echo URLROOT; ?>/schedules/import" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label><strong>Grupo Destino:</strong></label>
                            <input type="text" class="form-control" value="Programación Web - Grupo 4APRM" readonly>
                            <input type="hidden" name="id_grupo" value="<?php echo $data['id_grupo'] ?? 1; ?>">
                        </div>

                        <div class="form-group mt-4">
                            <label for="archivo_alumnos">Seleccionar archivo de Excel/CSV:</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="archivo_alumnos" id="archivo_alumnos" accept=".csv, .xlsx" required>
                                <label class="custom-file-label" for="archivo_alumnos">Seleccionar archivo...</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-info btn-block mt-4 shadow-sm">
                            <i class="fa fa-upload"></i> Procesar e Inscribir Alumnos
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5 shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-dark"><i class="fa fa-users"></i> Vista Previa de Inscritos</h5>
            <span class="badge badge-primary">3 Alumnos detectados</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Matrícula</th>
                        <th>Nombre Completo</th>
                        <th class="text-center">Estado</th>
                        <th class="text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>20260001</td>
                        <td>MARIO CALEB HERNANDEZ RAMOS</td>
                        <td class="text-center"><span class="badge badge-success">Registrado</span></td>
                        <td class="text-right"><button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button></td>
                    </tr>
                    <tr>
                        <td>20260002</td>
                        <td>VALENTINA ORTIZ DIAZ</td>
                        <td class="text-center"><span class="badge badge-success">Registrado</span></td>
                        <td class="text-right"><button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button></td>
                    </tr>
                    <tr>
                        <td>20260003</td>
                        <td>ALYSON NAYELI RIVERA</td>
                        <td class="text-center"><span class="badge badge-success">Registrado</span></td>
                        <td class="text-right"><button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white text-muted small">
            <i class="fa fa-check-circle"></i> Estos alumnos ahora pueden recibir evaluaciones en las actividades configuradas.
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
