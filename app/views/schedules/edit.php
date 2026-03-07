<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
    .config-card { border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
    .config-header { background: linear-gradient(135deg, #343a40 0%, #23272b 100%); border-bottom: 3px solid #ffc107; }
    .section-title { border-left: 4px solid #ffc107; padding-left: 15px; margin: 20px 0; }
    .unit-card { border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 20px; transition: all 0.3s ease; }
    .unit-card:hover { box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1); }
    .unit-header { background-color: #f8f9fa; padding: 12px 15px; border-bottom: 1px solid #dee2e6; border-radius: 8px 8px 0 0; cursor: pointer; }
    .unit-body { padding: 15px; }
    .activity-row { background-color: #fff; transition: background-color 0.2s; }
    .activity-row:hover { background-color: #f8f9fa; }
    .activity-input { width: 70px; text-align: center; border: 1px solid #ced4da; border-radius: 4px; padding: 0.25rem; }
    .activity-input:focus { border-color: #ffc107; outline: none; box-shadow: 0 0 0 0.2rem rgba(255,193,7,0.25); }
    .student-list { max-height: 400px; overflow-y: auto; }
    .student-item { padding: 8px 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .student-item:hover { background-color: #f8f9fa; }
    .badge-total { background-color: #28a745; color: white; font-size: 0.9rem; padding: 0.3rem 0.6rem; }
    .action-btn { margin: 0 3px; padding: 0.25rem 0.5rem; font-size: 0.875rem; }
    .nav-tabs .nav-link { color: #495057; font-weight: 500; }
    .nav-tabs .nav-link.active { color: #ffc107; font-weight: 600; border-bottom: 3px solid #ffc107; }
    .ponderacion-bar { height: 5px; background-color: #e9ecef; border-radius: 3px; margin-top: 5px; }
    .ponderacion-progress { height: 5px; border-radius: 3px; transition: width 0.3s; }
    .empty-state { padding: 40px; text-align: center; color: #6c757d; }
    .empty-state i { font-size: 3rem; margin-bottom: 15px; color: #dee2e6; display: block; }
    #importar_tabla input { font-size: 0.85rem; }
    .sort-btn.active { background-color: #343a40; color: #ffc107; border-color: #343a40; }
    .sort-btn.active:hover { background-color: #23272b; }
    #alumnoSearch:focus { border-color: #ffc107; box-shadow: 0 0 0 0.2rem rgba(255,193,7,0.25); }
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

                <!-- ═══════════════════════════════════════════ -->
                <!-- PESTAÑA 1: UNIDADES                        -->
                <!-- ═══════════════════════════════════════════ -->
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
                                                                    value="<?php echo $actividad->fecha_entrega ?? ''; ?>"
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

                <!-- ═══════════════════════════════════════════ -->
                <!-- PESTAÑA 2: ALUMNOS                         -->
                <!-- ═══════════════════════════════════════════ -->
                <div class="tab-pane fade" id="estudiantes" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="section-title mb-0">Alumnos Inscritos</h5>
                        <div>
                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalImportar">
                                <i class="fas fa-file-import mr-1"></i> Importar Lista
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="abrirModalAlumno()">
                                <i class="fas fa-user-plus mr-1"></i> Agregar Manual
                            </button>
                        </div>
                    </div>

                    <!-- Barra búsqueda + orden -->
                    <div class="d-flex align-items-center mb-3 gap-2" style="gap:10px;">
                        <div class="input-group input-group-sm flex-grow-1" style="max-width:320px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                            <input type="text" id="alumnoSearch"
                                class="form-control border-left-0"
                                placeholder="Buscar por nombre..."
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
                        <small class="text-muted" id="alumnoContadorFiltro"></small>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="student-list border rounded" id="studentListContainer">
                                <?php if (!empty($data['students'])) : ?>
                                    <?php foreach ($data['students'] as $student) :
                                        $parteEmail  = strstr($student->email ?? '', '@', true);
                                        $esMatricula = ctype_digit($parteEmail);
                                        $etiqueta    = $esMatricula ? $parteEmail : ($student->email ?? '');
                                    ?>
                                        <div class="student-item" data-name="<?php echo strtolower(htmlspecialchars($student->name)); ?>">
                                            <div>
                                                <strong class="student-name"><?php echo $student->name; ?></strong><br>
                                                <small class="text-muted">
                                                    <?php if ($esMatricula): ?>
                                                        <i class="fas fa-id-card mr-1"></i><?php echo $etiqueta; ?>
                                                    <?php else: ?>
                                                        <i class="fas fa-envelope mr-1"></i><?php echo $etiqueta; ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <button class="btn btn-sm btn-outline-primary mr-1"
                                                    title="Editar alumno"
                                                    onclick="abrirModalEditarAlumno(
                                                        <?php echo $student->user_id; ?>,
                                                        '<?php echo addslashes($student->name); ?>',
                                                        '<?php echo $esMatricula ? addslashes($parteEmail) : ''; ?>'
                                                    )">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar alumno completamente"
                                                    onclick="abrirModalConfirmar('eliminar_alumno_completo', <?php echo $student->user_id; ?>, '¿Eliminar a <strong><?php echo addslashes($student->name); ?></strong>? Se borrará su usuario y <strong>todas</strong> sus calificaciones permanentemente.')">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <div id="noResultsMsg" class="empty-state" style="display:none;">
                                        <i class="fas fa-search" style="font-size:2rem;color:#dee2e6;display:block;margin-bottom:10px;"></i>
                                        <p class="text-muted mb-0">Sin resultados para "<span id="noResultsTerm"></span>"</p>
                                    </div>
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
                                    <hr>
                                    <button class="btn btn-sm btn-outline-secondary btn-block" onclick="exportarLista()">
                                        <i class="fas fa-download mr-1"></i> Exportar Lista
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════ -->
                <!-- PESTAÑA 3: CONFIGURACIÓN                   -->
                <!-- ═══════════════════════════════════════════ -->
                <div class="tab-pane fade" id="configuracion" role="tabpanel">
                    <h5 class="section-title">Configuración General del Grupo</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light"><h6 class="mb-0">Información Básica</h6></div>
                                <div class="card-body">
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
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light"><h6 class="mb-0">Acciones Rápidas</h6></div>
                                <div class="card-body">
                                    <button class="btn btn-warning btn-block mb-2"
                                        onclick="abrirModalConfirmar('reiniciar_calificaciones', null, '¿Reiniciar TODAS las calificaciones? Esta acción no se puede deshacer.')">
                                        <i class="fas fa-redo-alt mr-1"></i> Reiniciar Calificaciones
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
                <button class="btn btn-secondary" onclick="history.back()"><i class="fas fa-arrow-left mr-1"></i> Volver</button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- MODALES                                                     -->
<!-- ═══════════════════════════════════════════════════════════ -->

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
                    <label>Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="actividadNombre" placeholder="Ej: Examen Parcial 1">
                </div>
                <div class="form-group">
                    <label>Ponderación (%)</label>
                    <input type="number" class="form-control" id="actividadPonderacion" value="0" min="0" max="100">
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

<!-- Modal: Agregar Alumno Manual -->
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
                    <label>Matrícula</label>
                    <input type="text" class="form-control" id="alumnoMatricula" placeholder="Ej: 21020001">
                    <small class="text-muted">Si no tiene matrícula, llena el correo abajo</small>
                </div>
                <div class="form-group">
                    <label>Correo electrónico <small class="text-muted">(solo si no tiene matrícula)</small></label>
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
                <p class="text-muted small">Se creará una copia. Puedes editar el nombre y la ponderación.</p>
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

<!-- Modal: Importar Lista CSV (embebido) -->
<div class="modal fade" id="modalImportar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-file-import mr-2"></i>Importar Lista de Alumnos</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-5">
                        <h6><i class="fas fa-info-circle text-info mr-1"></i> Instrucciones</h6>
                        <ul class="small text-muted pl-3">
                            <li>Formato <strong>.CSV</strong> únicamente</li>
                            <li>Columnas: <strong>Matrícula, Nombre</strong></li>
                            <li>Sin filas vacías entre registros</li>
                        </ul>
                        <div class="alert alert-light border p-2">
                            <small class="text-muted d-block mb-1">Ejemplo:</small>
                            <code class="small">21020001,Juan Perez<br>21020002,Maria Garcia</code>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <label>Seleccionar archivo CSV:</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="importar_archivo" accept=".csv">
                                <label class="custom-file-label" for="importar_archivo" id="importar_label">Seleccionar archivo...</label>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-info btn-block mt-2" onclick="cargarPreviewImport()">
                            <i class="fas fa-eye mr-1"></i> Cargar Vista Previa
                        </button>
                    </div>
                </div>

                <div id="importar_preview" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Vista Previa — puedes editar antes de inscribir</h6>
                        <span class="badge badge-primary" id="importar_contador">0 alumnos</span>
                    </div>
                    <div style="max-height:250px; overflow-y:auto; border:1px solid #dee2e6; border-radius:4px;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light" style="position:sticky;top:0;">
                                <tr>
                                    <th style="width:30%">Matrícula</th>
                                    <th>Nombre</th>
                                    <th style="width:40px"></th>
                                </tr>
                            </thead>
                            <tbody id="importar_tabla"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info" id="btn_importar_confirm" disabled onclick="ejecutarImportacion()">
                    <i class="fas fa-check mr-1"></i> Inscribir Alumnos
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Editar Alumno -->
<div class="modal fade" id="modalEditarAlumno" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-edit mr-2"></i>Editar Alumno</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editAlumnoUserId">
                <div class="form-group">
                    <label>Nombre completo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editAlumnoNombre" placeholder="Ej: Juan García López">
                </div>
                <div class="form-group">
                    <label>Matrícula <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editAlumnoMatricula" placeholder="Ej: 21020001">
                    <small class="text-muted mt-1 d-block">
                        Email resultante: <span id="editAlumnoEmailPreview" class="text-info font-weight-bold"></span>
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarEdicionAlumno()">
                    <i class="fas fa-save mr-1"></i>Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Confirmación genérica -->
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

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- SCRIPTS                                                     -->
<!-- ═══════════════════════════════════════════════════════════ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

<script>
const URLROOT = '<?php echo URLROOT; ?>';
const SCHED_ID = <?php echo $data['schedule']->id; ?>;

// ── TABS con persistencia ─────────────────────────────────────
$(document).ready(function () {
    $('#configTabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
        localStorage.setItem('lastTab', $(this).attr('href'));
    });
    const last = localStorage.getItem('lastTab');
    if (last) $(`#configTabs a[href="${last}"]`).tab('show');
});

// ── HELPER: POST + recarga ────────────────────────────────────
function enviarYRecargar(formData, mensajeExito, onSuccess) {
    fetch(`${URLROOT}/schedules/edit/${SCHED_ID}`, {
        method: 'POST', body: formData, redirect: 'follow'
    })
    .then(() => {
        if (onSuccess) onSuccess();
        mostrarNotificacion(mensajeExito, 'success');
        setTimeout(() => location.reload(), 1000);
    })
    .catch(() => mostrarNotificacion('Error de conexión', 'danger'));
}

// ── NOTIFICACIÓN ──────────────────────────────────────────────
function mostrarNotificacion(mensaje, tipo = 'success', tiempo = 3000) {
    const n = document.createElement('div');
    n.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
    n.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:250px;';
    n.innerHTML = `${mensaje}<button type="button" class="close" onclick="this.parentElement.remove()"><span>&times;</span></button>`;
    document.body.appendChild(n);
    setTimeout(() => n.parentNode && n.remove(), tiempo);
}

// ── MODAL UNIDAD ──────────────────────────────────────────────
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
    const orden  = $('#unidadOrden').val();
    const editId = $('#unidadEditandoId').val();
    if (!nombre) { mostrarNotificacion('El nombre es obligatorio', 'warning'); return; }
    const fd = new FormData();
    fd.append('action', editId ? 'update_unit' : 'add_unit');
    if (editId) fd.append('unit_id', editId);
    fd.append('nombre', nombre);
    fd.append('orden', orden);
    enviarYRecargar(fd, 'Unidad guardada', () => $('#modalUnidad').modal('hide'));
}

// ── MODAL ACTIVIDAD ───────────────────────────────────────────
function abrirModalActividad(unidadId) {
    $('#actividadUnidadId').val(unidadId);
    $('#actividadNombre').val('');
    $('#actividadPonderacion').val('0');
    $('#actividadFecha').val('');
    $('#modalActividad').modal('show');
}

function guardarActividad() {
    const nombre      = $('#actividadNombre').val().trim();
    const ponderacion = parseInt($('#actividadPonderacion').val());
    const fecha       = $('#actividadFecha').val();
    const unidadId    = $('#actividadUnidadId').val();
    if (!nombre) { mostrarNotificacion('El nombre es obligatorio', 'warning'); return; }
    if (isNaN(ponderacion) || ponderacion < 0 || ponderacion > 100) {
        mostrarNotificacion('La ponderación debe ser entre 0 y 100', 'warning'); return;
    }
    const fd = new FormData();
    fd.append('action',      'add_activity');
    fd.append('unidad_id',   unidadId);
    fd.append('nombre',      nombre);
    fd.append('ponderacion', ponderacion);
    if (fecha) fd.append('fecha_entrega', fecha);
    enviarYRecargar(fd, 'Actividad agregada', () => $('#modalActividad').modal('hide'));
}

// ── ACTUALIZAR ACTIVIDAD INLINE ───────────────────────────────
function actualizarActividad(id, campo, valor) {
    fetch(`${URLROOT}/schedules/ajax/update_activity_field`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ id, field: campo, value: valor })
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

// ── MODAL DUPLICAR ────────────────────────────────────────────
function abrirModalDuplicar(id, unidadId, nombre, ponderacion) {
    $('#duplicarUnidadId').val(unidadId);
    $('#duplicarNombre').val('Copia de ' + nombre);
    $('#duplicarPonderacion').val(ponderacion);
    $('#modalDuplicar').modal('show');
}

function confirmarDuplicar() {
    const nombre      = $('#duplicarNombre').val().trim();
    const ponderacion = parseInt($('#duplicarPonderacion').val());
    const unidadId    = $('#duplicarUnidadId').val();
    if (!nombre) { mostrarNotificacion('El nombre es obligatorio', 'warning'); return; }
    const fd = new FormData();
    fd.append('action',      'add_activity');
    fd.append('unidad_id',   unidadId);
    fd.append('nombre',      nombre);
    fd.append('ponderacion', ponderacion);
    enviarYRecargar(fd, 'Actividad duplicada', () => $('#modalDuplicar').modal('hide'));
}

// ── MODAL ALUMNO MANUAL ───────────────────────────────────────
function abrirModalAlumno() {
    $('#alumnoNombre').val('');
    $('#alumnoMatricula').val('');
    $('#alumnoEmail').val('');
    $('#modalAlumno').modal('show');
}

function guardarAlumno() {
    const nombre    = $('#alumnoNombre').val().trim();
    const matricula = $('#alumnoMatricula').val().trim();
    const email     = $('#alumnoEmail').val().trim();

    if (!nombre) { mostrarNotificacion('El nombre es obligatorio', 'warning'); return; }
    if (!matricula && !email) { mostrarNotificacion('Ingresa matrícula o correo electrónico', 'warning'); return; }
    if (matricula && !/^\d+$/.test(matricula)) { mostrarNotificacion('La matrícula debe ser numérica', 'warning'); return; }

    const fd = new FormData();
    fd.append('action',    'add_student');
    fd.append('name',      nombre);
    fd.append('matricula', matricula);
    fd.append('email',     email);

    enviarYRecargar(fd, 'Alumno agregado', () => $('#modalAlumno').modal('hide'));
}

// ── MODAL IMPORTAR CSV ────────────────────────────────────────
document.getElementById('importar_archivo').addEventListener('change', function () {
    document.getElementById('importar_label').textContent = this.files[0]?.name || 'Seleccionar archivo...';
});

function cargarPreviewImport() {
    const file = document.getElementById('importar_archivo').files[0];
    if (!file) { mostrarNotificacion('Selecciona un archivo primero', 'warning'); return; }

    const reader = new FileReader();
    reader.onload = function (e) {
        const tbody = document.getElementById('importar_tabla');
        tbody.innerHTML = '';
        let count = 0;

        e.target.result.split(/\r?\n/).forEach(linea => {
            const cols = linea.split(',');
            if (cols.length < 2 || !cols[0].trim()) return;
            const matricula = cols[0].trim();
            const nombre    = cols[1].trim();
            count++;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" class="form-control form-control-sm imp-mat" value="${matricula}"></td>
                <td><input type="text" class="form-control form-control-sm imp-nom" value="${nombre}"></td>
                <td>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0"
                        onclick="this.closest('tr').remove(); actualizarContadorImport();">&times;</button>
                </td>`;
            tbody.appendChild(tr);
        });

        actualizarContadorImport();
        document.getElementById('importar_preview').style.display = count > 0 ? 'block' : 'none';
        if (count === 0) mostrarNotificacion('No se encontraron registros válidos en el archivo', 'warning');
    };
    reader.readAsText(file);
}

function actualizarContadorImport() {
    const n = document.querySelectorAll('#importar_tabla tr').length;
    document.getElementById('importar_contador').textContent = n + ' alumno' + (n !== 1 ? 's' : '');
    document.getElementById('btn_importar_confirm').disabled = n === 0;
}

function ejecutarImportacion() {
    const filas    = document.querySelectorAll('#importar_tabla tr');
    const students = [];

    filas.forEach(tr => {
        const mat = tr.querySelector('.imp-mat')?.value.trim();
        const nom = tr.querySelector('.imp-nom')?.value.trim();
        if (mat && nom) students.push({ name: nom, email: mat + '@students.local', matricula: mat });
    });

    if (!students.length) { mostrarNotificacion('No hay alumnos para importar', 'warning'); return; }

    const fd = new FormData();
    fd.append('action', 'import_students');
    students.forEach((s, i) => {
        fd.append(`students[${i}][name]`,      s.name);
        fd.append(`students[${i}][email]`,     s.email);
        fd.append(`students[${i}][matricula]`, s.matricula);
    });

    $('#modalImportar').modal('hide');
    enviarYRecargar(fd, `${students.length} alumno(s) importado(s)`);
}

// ── MODAL CONFIRMACIÓN GENÉRICA ───────────────────────────────
function abrirModalConfirmar(accion, id, mensaje) {
    $('#modalConfirmarAccion').val(accion);
    $('#modalConfirmarId').val(id || '');
    $('#modalConfirmarMensaje').html(mensaje);
    $('#modalConfirmar').modal('show');
}

function ejecutarAccionConfirmada() {
    const accion = $('#modalConfirmarAccion').val();
    const id     = $('#modalConfirmarId').val();
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
            fd.append('inscripcion_id', id);
            enviarYRecargar(fd, 'Alumno eliminado del grupo');
            break;
        case 'eliminar_alumno_completo':
            fd.append('action', 'delete_student_full');
            fd.append('user_id', id);
            enviarYRecargar(fd, 'Alumno eliminado permanentemente');
            break;
        case 'reiniciar_calificaciones':
            fd.append('action', 'reset_grades');
            enviarYRecargar(fd, 'Calificaciones reiniciadas');
            break;
        case 'archivar_grupo':
            fd.append('action', 'archive_group');
            fetch(`${URLROOT}/schedules/edit/${SCHED_ID}`, { method: 'POST', body: fd, redirect: 'follow' })
                .then(() => { mostrarNotificacion('Grupo archivado', 'success'); setTimeout(() => window.location.href = `${URLROOT}/schedules/index`, 1500); });
            return;
    }
}

// ── CONFIGURACIÓN GENERAL ─────────────────────────────────────
function guardarConfiguracion() {
    const fd = new FormData();
    fd.append('action',  'update_schedule');
    fd.append('grupo',   $('#cfg_grupo').val());
    fd.append('periodo', $('#cfg_periodo').val());
    fd.append('aula',    $('#cfg_aula').val());
    enviarYRecargar(fd, 'Configuración guardada');
}

// ── VERIFICAR PESOS ───────────────────────────────────────────
function calcularPesos() {
    fetch(`${URLROOT}/schedules/ajax/validate_weights?schedule_id=${SCHED_ID}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        let msg = 'Verificación de pesos:\n\n';
        let ok  = true;
        data.unidades.forEach(u => {
            const e = u.estado === 'ok' ? '✅' : (u.estado === 'exceso' ? '⚠️ Exceso' : '⚠️ Falta');
            msg += `${u.nombre}: ${u.total}% ${e}\n`;
            if (u.estado !== 'ok') ok = false;
        });
        msg += `\nTotal general: ${data.total_general}%`;
        mostrarNotificacion(ok ? 'Todas las unidades están correctas' : 'Algunas unidades no suman 100%', ok ? 'success' : 'warning');
        alert(msg);
    });
}

// ── FILTRO Y ORDEN DE ALUMNOS ─────────────────────────────────
let sortOrder = 'default';

// Guardar orden original al cargar
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('studentListContainer');
    if (container) {
        Array.from(container.querySelectorAll('.student-item')).forEach((item, i) => {
            item.dataset.index = i;
        });
    }
    filtrarYOrdenar();
});

function cambiarOrden(btn, orden) {
    sortOrder = orden;
    document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filtrarYOrdenar();
}

function filtrarYOrdenar() {
    const q         = (document.getElementById('alumnoSearch')?.value || '').toLowerCase().trim();
    const container = document.getElementById('studentListContainer');
    if (!container) return;
    const items     = Array.from(container.querySelectorAll('.student-item'));
    const noResults = document.getElementById('noResultsMsg');
    const contador  = document.getElementById('alumnoContadorFiltro');

    // Filtrar visibilidad
    items.forEach(item => {
        const nombre = item.dataset.name || '';
        item.style.display = (!q || nombre.includes(q)) ? '' : 'none';
    });

    // Ordenar nodos en el DOM
    const todos = [...items]; // copia para no mutar
    if (sortOrder === 'asc') {
        todos.sort((a, b) => (a.dataset.name).localeCompare(b.dataset.name, 'es'));
    } else if (sortOrder === 'desc') {
        todos.sort((a, b) => (b.dataset.name).localeCompare(a.dataset.name, 'es'));
    } else {
        // default: restaurar orden original por índice
        todos.sort((a, b) => parseInt(a.dataset.index) - parseInt(b.dataset.index));
    }
    todos.forEach(item => container.appendChild(item));

    // Contar visibles
    const visibles = items.filter(i => i.style.display !== 'none').length;
    const total    = items.length;

    // Mensaje sin resultados
    if (noResults) {
        noResults.style.display = visibles === 0 && q ? 'block' : 'none';
        if (visibles === 0 && q) {
            document.getElementById('noResultsTerm').textContent = q;
        }
    }

    // Contador
    if (contador) {
        contador.textContent = q
            ? `${visibles} de ${total}`
            : (total > 0 ? `${total} alumno${total !== 1 ? 's' : ''}` : '');
    }
}

// ── MODAL EDITAR ALUMNO ───────────────────────────────────────
function abrirModalEditarAlumno(userId, nombre, matricula) {
    $('#editAlumnoUserId').val(userId);
    $('#editAlumnoNombre').val(nombre);
    $('#editAlumnoMatricula').val(matricula);
    $('#editAlumnoEmailPreview').text((matricula || '...') + '@students.local');
    $('#modalEditarAlumno').modal('show');
}

document.getElementById('editAlumnoMatricula').addEventListener('input', function () {
    document.getElementById('editAlumnoEmailPreview').textContent =
        (this.value.trim() || '...') + '@students.local';
});

function guardarEdicionAlumno() {
    const userId    = $('#editAlumnoUserId').val();
    const nombre    = $('#editAlumnoNombre').val().trim();
    const matricula = $('#editAlumnoMatricula').val().trim();

    if (!nombre || !matricula) {
        mostrarNotificacion('Nombre y matrícula son obligatorios', 'warning');
        return;
    }
    if (!/^\d+$/.test(matricula)) {
        mostrarNotificacion('La matrícula debe ser numérica', 'warning');
        return;
    }

    const fd = new FormData();
    fd.append('action',    'update_student');
    fd.append('user_id',   userId);
    fd.append('name',      nombre);
    fd.append('matricula', matricula);

    enviarYRecargar(fd, 'Alumno actualizado', () => $('#modalEditarAlumno').modal('hide'));
}

// ── EXPORTAR LISTA ────────────────────────────────────────────
function exportarLista() {
    window.location.href = `${URLROOT}/students/export/${SCHED_ID}`;
}
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>