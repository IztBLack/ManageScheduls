<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
    .config-card {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .config-header {
        background: linear-gradient(135deg, #343a40 0%, #23272b 100%);
        border-bottom: 3px solid #ffc107;
    }

    .section-title {
        border-left: 4px solid #ffc107;
        padding-left: 15px;
        margin: 20px 0;
    }

    .unit-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .unit-card:hover {
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    .unit-header {
        background-color: #f8f9fa;
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        border-radius: 8px 8px 0 0;
        cursor: pointer;
    }

    .unit-body {
        padding: 15px;
    }

    .activity-row {
        background-color: #fff;
        transition: background-color 0.2s;
    }

    .activity-row:hover {
        background-color: #f8f9fa;
    }

    .activity-input {
        width: 70px;
        text-align: center;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 0.25rem;
    }

    .activity-input:focus {
        border-color: #ffc107;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }

    .student-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .student-item {
        padding: 8px 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .student-item:hover {
        background-color: #f8f9fa;
    }

    .badge-total {
        background-color: #28a745;
        color: white;
        font-size: 0.9rem;
        padding: 0.3rem 0.6rem;
    }

    .action-btn {
        margin: 0 3px;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        color: #ffc107;
        font-weight: 600;
        border-bottom: 3px solid #ffc107;
    }

    .ponderacion-bar {
        height: 5px;
        background-color: #e9ecef;
        border-radius: 3px;
        margin-top: 5px;
    }

    .ponderacion-progress {
        height: 5px;
        background-color: #28a745;
        border-radius: 3px;
        transition: width 0.3s;
    }

    .empty-state {
        padding: 40px;
        text-align: center;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #dee2e6;
        display: block;
    }

    /* Modal de confirmación de peligro */
    .modal-danger .modal-header {
        background-color: #dc3545;
        color: white;
    }

    .modal-danger .modal-header .close {
        color: white;
    }

    .modal-warning-header .modal-header {
        background-color: #ffc107;
    }
</style>

<div class="container-fluid mt-4">
    <div class="card config-card">

        <div class="card-header config-header text-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h3 class="mb-1"><i class="fas fa-cog mr-2"></i>Configuración de la Clase</h3>
                <div class="d-flex align-items-center">
                    <span class="badge badge-light mr-2"><i class="fas fa-book mr-1"></i><?php echo $data['schedule']->subject_name; ?></span>
                    <span class="badge badge-warning mr-2"><i class="fas fa-users mr-1"></i>Grupo <?php echo $data['schedule']->grupo; ?></span>
                    <span class="badge badge-info"><i class="fas fa-calendar mr-1"></i><?php echo $data['schedule']->periodo; ?></span>
                </div>
            </div>
            <div>
                <button class="btn btn-outline-light btn-sm" onclick="window.print()">
                    <i class="fas fa-print mr-1"></i> Imprimir
                </button>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#unidades" role="tab">
                        <i class="fas fa-layer-group mr-1"></i> Unidades y Actividades
                        <span class="badge badge-warning ml-1"><?php echo count($data['unidades']); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#estudiantes" role="tab">
                        <i class="fas fa-user-graduate mr-1"></i> Lista de Alumnos
                        <span class="badge badge-warning ml-1"><?php echo count($data['students']); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#configuracion" role="tab">
                        <i class="fas fa-sliders-h mr-1"></i> Configuración General
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="configTabsContent">

                <!-- PESTAÑA 1: UNIDADES -->
                <div class="tab-pane fade show active" id="unidades" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="section-title mb-0">Estructura de Evaluación</h5>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="abrirModalUnidad()">
                                <i class="fas fa-plus-circle mr-1"></i> Nueva Unidad
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="calcularPesos()">
                                <i class="fas fa-calculator mr-1"></i> Verificar Pesos
                            </button>
                        </div>
                    </div>
<!-- Agrega esto TEMPORALMENTE arriba de la lista de alumnos en la pestaña -->
<?php 
if (!empty($data['students'])) {
    echo '<pre>';
    var_dump($data['students'][0]);
    echo '</pre>';
}
?>
                    <?php if (!empty($data['unidades'])) : ?>
                        <?php foreach ($data['unidades'] as $unidad) :
                            $totalUnidad = 0;
                            if (!empty($unidad->actividades)) {
                                foreach ($unidad->actividades as $act) $totalUnidad += $act->ponderacion;
                            }
                            $progressClass = $totalUnidad == 100 ? 'bg-success' : ($totalUnidad > 100 ? 'bg-danger' : 'bg-warning');
                        ?>
                        
                            <div class="unit-card">
                                <div class="unit-header d-flex justify-content-between align-items-center"
                                    data-toggle="collapse" data-target="#unidad-<?php echo $unidad->id; ?>">
                                    <div>
                                        <h6 class="mb-0">
                                            <i class="fas fa-chevron-down mr-2" style="font-size:0.8rem;"></i>
                                            <?php echo $unidad->nombre; ?>
                                        </h6>
                                        <small class="text-muted">
                                            Orden: <?php echo $unidad->orden; ?> |
                                            Total: <span class="<?php echo $totalUnidad == 100 ? 'text-success' : ($totalUnidad > 100 ? 'text-danger' : 'text-warning'); ?>">
                                                <?php echo $totalUnidad; ?>%
                                            </span>
                                        </small>
                                    </div>
                                    <div onclick="event.stopPropagation()">
                                        <button class="btn btn-sm btn-outline-primary action-btn"
                                            onclick="abrirModalEditarUnidad(<?php echo $unidad->id; ?>, '<?php echo addslashes($unidad->nombre); ?>', <?php echo $unidad->orden; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger action-btn"
                                            onclick="abrirModalConfirmar('eliminar_unidad', <?php echo $unidad->id; ?>, '¿Eliminar la unidad <strong><?php echo addslashes($unidad->nombre); ?></strong> y todas sus actividades?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success action-btn"
                                            onclick="abrirModalActividad(<?php echo $unidad->id; ?>)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="collapse show" id="unidad-<?php echo $unidad->id; ?>">
                                    <div class="unit-body">
                                        <div class="ponderacion-bar mb-3">
                                            <div class="ponderacion-progress <?php echo $progressClass; ?>"
                                                style="width:<?php echo min($totalUnidad, 100); ?>%"></div>
                                        </div>
                                        <table class="table table-sm table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Actividad</th>
                                                    <th width="100">Ponderación</th>
                                                    <th width="120">Fecha Límite</th>
                                                    <th width="120">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($unidad->actividades)) : ?>
                                                    <?php foreach ($unidad->actividades as $actividad) : ?>
                                                        <tr class="activity-row">
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    value="<?php echo $actividad->nombre; ?>"
                                                                    onchange="actualizarActividad(<?php echo $actividad->id; ?>, 'nombre', this.value)">
                                                            </td>
                                                            <td>
                                                                <input type="number" class="activity-input"
                                                                    value="<?php echo $actividad->ponderacion; ?>"
                                                                    min="0" max="100" step="1"
                                                                    onchange="actualizarActividad(<?php echo $actividad->id; ?>, 'ponderacion', this.value)">
                                                            </td>
                                                            <td>
                                                                <input type="date" class="form-control form-control-sm"
                                                                    value="<?php echo $actividad->fecha_entrega; ?>"
                                                                    onchange="actualizarActividad(<?php echo $actividad->id; ?>, 'fecha', this.value)">
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-link text-danger p-0 mr-2"
                                                                    onclick="abrirModalConfirmar('eliminar_actividad', <?php echo $actividad->id; ?>, '¿Eliminar la actividad <strong><?php echo addslashes($actividad->nombre); ?></strong>?')">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-link text-success p-0"
                                                                    onclick="abrirModalDuplicar(<?php echo $actividad->id; ?>, <?php echo $unidad->id; ?>, '<?php echo addslashes($actividad->nombre); ?>', <?php echo $actividad->ponderacion; ?>)">
                                                                    <i class="fas fa-copy"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-3">
                                                            <i class="fas fa-info-circle mr-1"></i> No hay actividades en esta unidad
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h5>No hay unidades configuradas</h5>
                            <p class="text-muted">Comienza agregando una nueva unidad al plan de evaluación</p>
                            <button class="btn btn-primary" onclick="abrirModalUnidad()">
                                <i class="fas fa-plus-circle mr-1"></i> Crear Primera Unidad
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- PESTAÑA 2: ALUMNOS -->
                <div class="tab-pane fade" id="estudiantes" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="section-title mb-0">Alumnos Inscritos</h5>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="importarAlumnos()">
                                <i class="fas fa-file-import mr-1"></i> Importar Lista
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="abrirModalAlumno()">
                                <i class="fas fa-user-plus mr-1"></i> Agregar Manual
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="student-list border rounded">
                                <?php if (!empty($data['students'])) : ?>
                                    <?php foreach ($data['students'] as $student) : ?>
                                        <div class="student-item">
                                            <div>
                                                <strong><?php echo $student->name; ?></strong><br>
                                     </div>
                                            <div>
                                                <span class="badge badge-secondary mr-2">ID: <?php echo $student->inscripcion_id; ?></span>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="abrirModalConfirmar('eliminar_alumno', <?php echo $student->id; ?>, '¿Eliminar a <strong><?php echo addslashes($student->name); ?></strong> del grupo? Se borrarán todas sus calificaciones.')">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <h5>No hay alumnos inscritos</h5>
                                        <p class="text-muted">Importa una lista o agrega alumnos manualmente</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Estadísticas</h6>
                                    <hr>
                                    <p class="mb-1"><strong>Total alumnos:</strong> <span class="badge badge-total float-right"><?php echo count($data['students']); ?></span></p>
                                    <p class="mb-1"><strong>Capacidad máxima:</strong> <span class="badge badge-info float-right">40</span></p>
                                    <p class="mb-1"><strong>Cupo disponible:</strong> <span class="badge badge-success float-right"><?php echo 40 - count($data['students']); ?></span></p>
                                    <hr>
                                    <button class="btn btn-sm btn-outline-secondary btn-block" onclick="exportarLista()">
                                        <i class="fas fa-download mr-1"></i> Exportar Lista
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PESTAÑA 3: CONFIGURACIÓN -->
                <div class="tab-pane fade" id="configuracion" role="tabpanel">
                    <h5 class="section-title">Configuración General del Grupo</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Información Básica</h6>
                                </div>
                                <div class="card-body">
                                    <form id="configForm">
                                        <div class="form-group">
                                            <label>Materia</label>
                                            <input type="text" class="form-control" value="<?php echo $data['schedule']->subject_name; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Grupo</label>
                                            <input type="text" class="form-control" id="cfg_grupo" value="<?php echo $data['schedule']->grupo; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Periodo</label>
                                            <input type="text" class="form-control" id="cfg_periodo" value="<?php echo $data['schedule']->periodo; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Salón/Aula</label>
                                            <input type="text" class="form-control" id="cfg_aula" value="<?php echo $data['schedule']->aula; ?>">
                                        </div>
                                        <button type="button" class="btn btn-primary btn-block" onclick="guardarConfiguracion()">
                                            <i class="fas fa-save mr-1"></i> Guardar Información
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Configuración de Evaluación</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Esquema de calificación</label>
                                        <select class="form-control">
                                            <option>Promedio de unidades</option>
                                            <option>Suma ponderada</option>
                                            <option>Acumulativo</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Manejo de bonus</label>
                                        <select class="form-control">
                                            <option>Sumar al final</option>
                                            <option>Promediar con unidades</option>
                                            <option>Máximo 20 puntos</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Escala de calificación</label>
                                        <select class="form-control">
                                            <option>0-100 (Numérica)</option>
                                            <option>0-10 (Decimal)</option>
                                            <option>Letras (A-F)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Acciones Rápidas</h6>
                                </div>
                                <div class="card-body">
                                    <button class="btn btn-warning btn-block mb-2"
                                        onclick="abrirModalConfirmar('reiniciar_calificaciones', null, '¿Reiniciar TODAS las calificaciones? Esta acción no se puede deshacer.')">
                                        <i class="fas fa-redo-alt mr-1"></i> Reiniciar Calificaciones
                                    </button>
                                    <button class="btn btn-info btn-block mb-2" onclick="backupConfiguracion()">
                                        <i class="fas fa-database mr-1"></i> Respaldo de Configuración
                                    </button>
                                    <button class="btn btn-danger btn-block"
                                        onclick="abrirModalConfirmar('archivar_grupo', null, '¿Archivar este grupo? Los alumnos serán dados de baja.')">
                                        <i class="fas fa-archive mr-1"></i> Archivar Grupo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /tab-content -->
        </div><!-- /card-body -->

        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Última modificación: <?php echo date('d/m/Y H:i'); ?></small>
                <div>
                    <button class="btn btn-secondary" onclick="history.back()"><i class="fas fa-arrow-left mr-1"></i> Volver</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODALES ==================== -->

<!-- Modal: Agregar/Editar Unidad -->
<div class="modal fade" id="modalUnidad" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalUnidadTitulo">Nueva Unidad</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="unidadEditandoId" value="">
                <div class="form-group">
                    <label>Nombre de la unidad <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="unidadNombre" placeholder="Ej: Unidad 1 - Introducción">
                </div>
                <div class="form-group">
                    <label>Orden</label>
                    <input type="number" class="form-control" id="unidadOrden" value="1" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarUnidad()"><i class="fas fa-save mr-1"></i>Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Agregar Actividad -->
<div class="modal fade" id="modalActividad" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Nueva Actividad</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="actividadUnidadId" value="">
                <div class="form-group">
                    <label>Nombre de la actividad <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="actividadNombre" placeholder="Ej: Examen Parcial 1">
                </div>
                <div class="form-group">
                    <label>Ponderación (%)</label>
                    <input type="number" class="form-control" id="actividadPonderacion" value="0" min="0" max="100">
                    <small class="text-muted">Ingresa un valor entre 0 y 100</small>
                </div>
                <div class="form-group">
                    <label>Fecha límite</label>
                    <input type="date" class="form-control" id="actividadFecha">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarActividad()"><i class="fas fa-save mr-1"></i>Agregar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Agregar Alumno -->
<div class="modal fade" id="modalAlumno" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Agregar Alumno</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nombre completo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="alumnoNombre" placeholder="Ej: Juan García López">
                </div>
                <div class="form-group">
                    <label>Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="alumnoEmail" placeholder="Ej: juan@ejemplo.com">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarAlumno()"><i class="fas fa-user-plus mr-1"></i>Agregar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Duplicar Actividad -->
<div class="modal fade" id="modalDuplicar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Duplicar Actividad</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="duplicarUnidadId" value="">
                <p class="text-muted">Se creará una copia de la actividad. Puedes modificar el nombre y la ponderación.</p>
                <div class="form-group">
                    <label>Nombre de la copia <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="duplicarNombre">
                </div>
                <div class="form-group">
                    <label>Ponderación (%)</label>
                    <input type="number" class="form-control" id="duplicarPonderacion" min="0" max="100">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancelar</button>
                <button type="button" class="btn btn-info" onclick="confirmarDuplicar()"><i class="fas fa-copy mr-1"></i>Duplicar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Confirmación genérica de peligro -->
<div class="modal fade" id="modalConfirmar" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-1"></i>Confirmar acción</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p id="modalConfirmarMensaje"></p>
                <input type="hidden" id="modalConfirmarAccion" value="">
                <input type="hidden" id="modalConfirmarId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancelar</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="ejecutarAccionConfirmada()"><i class="fas fa-check mr-1"></i>Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

<script>
    const URLROOT = '<?php echo URLROOT; ?>';
    const SCHED_ID = <?php echo $data['schedule']->id; ?>;

    // ── TABS ──────────────────────────────────────────────
    $(document).ready(function() {
        $('#configTabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
            localStorage.setItem('lastTab', $(this).attr('href'));
        });
        const last = localStorage.getItem('lastTab');
        if (last) $(`#configTabs a[href="${last}"]`).tab('show');
    });

    // ── MODAL UNIDAD ──────────────────────────────────────
    function abrirModalUnidad() {
        $('#unidadEditandoId').val('');
        $('#unidadNombre').val('');
        $('#unidadOrden').val('1');
        $('#modalUnidadTitulo').text('Nueva Unidad');
        $('#modalUnidad').modal('show');
    }

    function abrirModalEditarUnidad(id, nombre, orden) {
        $('#unidadEditandoId').val(id);
        $('#unidadNombre').val(nombre);
        $('#unidadOrden').val(orden);
        $('#modalUnidadTitulo').text('Editar Unidad');
        $('#modalUnidad').modal('show');
    }

    function guardarUnidad() {
        const nombre = $('#unidadNombre').val().trim();
        const orden = $('#unidadOrden').val();
        const editId = $('#unidadEditandoId').val();

        if (!nombre) {
            mostrarNotificacion('El nombre es obligatorio', 'warning');
            return;
        }

        const fd = new FormData();
        fd.append('action', editId ? 'update_unit' : 'add_unit');
        if (editId) fd.append('unit_id', editId);
        fd.append('nombre', nombre);
        fd.append('orden', orden);

        enviarYRecargar(fd, 'Unidad guardada', () => $('#modalUnidad').modal('hide'));
    }

    // ── MODAL ACTIVIDAD ───────────────────────────────────
    function abrirModalActividad(unidadId) {
        $('#actividadUnidadId').val(unidadId);
        $('#actividadNombre').val('');
        $('#actividadPonderacion').val('0');
        $('#actividadFecha').val('');
        $('#modalActividad').modal('show');
    }

    function guardarActividad() {
        const nombre = $('#actividadNombre').val().trim();
        const ponderacion = parseInt($('#actividadPonderacion').val());
        const fecha = $('#actividadFecha').val();
        const unidadId = $('#actividadUnidadId').val();

        if (!nombre) {
            mostrarNotificacion('El nombre es obligatorio', 'warning');
            return;
        }
        if (isNaN(ponderacion) || ponderacion < 0 || ponderacion > 100) {
            mostrarNotificacion('La ponderación debe ser entre 0 y 100', 'warning');
            return;
        }

        const fd = new FormData();
        fd.append('action', 'add_activity');
        fd.append('unidad_id', unidadId);
        fd.append('nombre', nombre);
        fd.append('ponderacion', ponderacion);
        if (fecha) fd.append('fecha', fecha);

        enviarYRecargar(fd, 'Actividad agregada', () => $('#modalActividad').modal('hide'));
    }

    // ── ACTUALIZAR ACTIVIDAD INLINE ───────────────────────
    function actualizarActividad(id, campo, valor) {
        fetch(`${URLROOT}/schedules/ajax/update_activity_field`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id,
                    field: campo,
                    value: valor
                })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    mostrarNotificacion('Cambio guardado', 'success', 1500);
                    if (campo === 'ponderacion') recalcularBarraVisual();
                }
            });
    }

    function recalcularBarraVisual() {
        document.querySelectorAll('.unit-card').forEach(card => {
            let total = 0;
            card.querySelectorAll('.activity-input').forEach(i => total += parseInt(i.value) || 0);
            const barra = card.querySelector('.ponderacion-progress');
            if (barra) {
                barra.style.width = Math.min(total, 100) + '%';
                barra.classList.remove('bg-success', 'bg-warning', 'bg-danger');
                barra.classList.add(total === 100 ? 'bg-success' : total > 100 ? 'bg-danger' : 'bg-warning');
            }
        });
    }

    // ── MODAL DUPLICAR ────────────────────────────────────
    function abrirModalDuplicar(id, unidadId, nombre, ponderacion) {
        $('#duplicarUnidadId').val(unidadId);
        $('#duplicarNombre').val('Copia de ' + nombre);
        $('#duplicarPonderacion').val(ponderacion);
        $('#modalDuplicar').modal('show');
    }

    function confirmarDuplicar() {
        const nombre = $('#duplicarNombre').val().trim();
        const ponderacion = parseInt($('#duplicarPonderacion').val());
        const unidadId = $('#duplicarUnidadId').val();

        if (!nombre) {
            mostrarNotificacion('El nombre es obligatorio', 'warning');
            return;
        }

        const fd = new FormData();
        fd.append('action', 'add_activity');
        fd.append('unidad_id', unidadId);
        fd.append('nombre', nombre);
        fd.append('ponderacion', ponderacion);

        enviarYRecargar(fd, 'Actividad duplicada', () => $('#modalDuplicar').modal('hide'));
    }

    // ── MODAL ALUMNO ──────────────────────────────────────
    function abrirModalAlumno() {
        $('#alumnoNombre').val('');
        $('#alumnoEmail').val('');
        $('#modalAlumno').modal('show');
    }

    function guardarAlumno() {
        const nombre = $('#alumnoNombre').val().trim();
        const email = $('#alumnoEmail').val().trim();

        if (!nombre || !email) {
            mostrarNotificacion('Nombre y email son obligatorios', 'warning');
            return;
        }

        const fd = new FormData();
        fd.append('action', 'add_student');
        fd.append('name', nombre);
        fd.append('email', email);

        enviarYRecargar(fd, 'Alumno agregado', () => $('#modalAlumno').modal('hide'));
    }

    // ── MODAL CONFIRMACIÓN GENÉRICA ───────────────────────
    function abrirModalConfirmar(accion, id, mensaje) {
        $('#modalConfirmarAccion').val(accion);
        $('#modalConfirmarId').val(id || '');
        $('#modalConfirmarMensaje').html(mensaje);
        $('#modalConfirmar').modal('show');
    }

    function ejecutarAccionConfirmada() {
        const accion = $('#modalConfirmarAccion').val();
        const id = $('#modalConfirmarId').val();
        $('#modalConfirmar').modal('hide');

        const fd = new FormData();

        switch (accion) {
            case 'eliminar_unidad':
                fd.append('action', 'delete_unit');
                fd.append('unit_id', id);
                enviarYRecargar(fd, 'Unidad eliminada');
                break;
            case 'eliminar_actividad':
                fd.append('action', 'delete_activity');
                fd.append('activity_id', id);
                enviarYRecargar(fd, 'Actividad eliminada');
                break;
            case 'eliminar_alumno':
                fd.append('action', 'remove_student');
                fd.append('user_id', id);
                enviarYRecargar(fd, 'Alumno eliminado');
                break;
            case 'reiniciar_calificaciones':
                fd.append('action', 'reset_grades');
                enviarYRecargar(fd, 'Calificaciones reiniciadas');
                break;
            case 'archivar_grupo':
                fd.append('action', 'archive_group');
                fetch(`${URLROOT}/schedules/edit/${SCHED_ID}`, {
                        method: 'POST',
                        body: fd,
                        redirect: 'follow'
                    })
                    .then(() => {
                        mostrarNotificacion('Grupo archivado', 'success');
                        setTimeout(() => window.location.href = `${URLROOT}/schedules/index`, 1500);
                    });
                return;
        }
    }

    // ── IMPORTAR ALUMNOS ──────────────────────────────────
    function importarAlumnos() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.csv,.txt';
        input.onchange = function(e) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const students = ev.target.result.split('\n')
                    .map(l => l.split(','))
                    .filter(p => p.length >= 2)
                    .map(p => ({
                        name: p[0].trim(),
                        email: p[1].trim()
                    }));
                if (!students.length) {
                    mostrarNotificacion('No se encontraron alumnos en el archivo', 'warning');
                    return;
                }
                const fd = new FormData();
                fd.append('action', 'import_students');
                fd.append('students', JSON.stringify(students));
                enviarYRecargar(fd, `${students.length} alumnos importados`);
            };
            reader.readAsText(e.target.files[0]);
        };
        input.click();
    }

    function exportarLista() {
        window.location.href = `${URLROOT}/students/export/${SCHED_ID}`;
    }

    // ── CONFIGURACIÓN GENERAL ─────────────────────────────
    function guardarConfiguracion() {
        const fd = new FormData();
        fd.append('action', 'update_schedule');
        fd.append('grupo', $('#cfg_grupo').val());
        fd.append('periodo', $('#cfg_periodo').val());
        fd.append('aula', $('#cfg_aula').val());
        enviarYRecargar(fd, 'Configuración guardada');
    }

    function backupConfiguracion() {
        mostrarNotificacion('Función de respaldo no implementada aún', 'info');
    }

    // ── VERIFICAR PESOS ───────────────────────────────────
    function calcularPesos() {
        fetch(`${URLROOT}/schedules/ajax/validate_weights?schedule_id=${SCHED_ID}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                let msg = 'Verificación de pesos:\n\n';
                let ok = true;
                data.unidades.forEach(u => {
                    const e = u.estado === 'ok' ? '✅' : (u.estado === 'exceso' ? '⚠️ Exceso' : '⚠️ Falta');
                    msg += `${u.nombre}: ${u.total}% ${e}\n`;
                    if (u.estado !== 'ok') ok = false;
                });
                msg += `\nTotal general: ${data.total_general}%`;
                if (ok) msg += '\n\n✅ Todas las unidades suman 100%';
                mostrarNotificacion(ok ? 'Todas las unidades están correctas' : 'Algunas unidades no suman 100%', ok ? 'success' : 'warning');
                alert(msg);
            });
    }

    // ── HELPER: enviar POST y recargar ─────────────────────
    function enviarYRecargar(formData, mensajeExito, onSuccess) {
        fetch(`${URLROOT}/schedules/edit/${SCHED_ID}`, {
                method: 'POST',
                body: formData,
                redirect: 'follow'
            })
            .then(response => {
                if (onSuccess) onSuccess();
                mostrarNotificacion(mensajeExito, 'success');
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                console.error(err);
                mostrarNotificacion('Error de conexión', 'danger');
            });
    }

    // ── NOTIFICACIÓN ──────────────────────────────────────
    function mostrarNotificacion(mensaje, tipo = 'success', tiempo = 3000) {
        const n = document.createElement('div');
        n.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
        n.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:250px;';
        n.innerHTML = `${mensaje}<button type="button" class="close" onclick="this.parentElement.remove()"><span>&times;</span></button>`;
        document.body.appendChild(n);
        setTimeout(() => n.parentNode && n.remove(), tiempo);
    }
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>