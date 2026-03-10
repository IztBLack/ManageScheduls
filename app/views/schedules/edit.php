<?php require APPROOT . '/views/inc/header.php'; ?>

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
                                    <h6 class="card-title mt-4">Acciones de Lista</h6>
                                    <button class="btn btn-sm btn-success btn-block mb-2" data-toggle="modal" data-target="#modalImportar">
                                        <i class="fas fa-file-import mr-1"></i> Importar Lista CSV
                                    </button>
                                    <button class="btn btn-sm btn-primary btn-block mb-2" onclick="abrirModalAlumno()">
                                        <i class="fas fa-user-plus mr-1"></i> Agregar Manualmente
                                    </button>
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
                                        <label class="d-flex justify-content-between align-items-center" for="cfg_especialidad" style="margin-bottom: 0.5rem;">
                                            <span>Especialidad / Carrera</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-toggle="modal" data-target="#addEspecialidadModal" title="Nueva Especialidad">
                                                <i class="fas fa-plus"></i> Nueva
                                            </button>
                                        </label>
                                        <select id="cfg_especialidad" class="form-control">
                                            <option value="">Seleccione...</option>
                                            <?php if (!empty($data['especialidades_existentes'])) : ?>
                                                <?php foreach ($data['especialidades_existentes'] as $esp) : ?>
                                                    <option value="<?php echo htmlspecialchars($esp); ?>"
                                                        <?php echo ($data['schedule']->especialidad == $esp) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($esp); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
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

<!-- Modal Editar Alumno -->
<div class="modal fade" id="modalEditarAlumno" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-bottom-0 pb-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-edit mr-2"></i>Editar Alumno</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body pt-4">
                <input type="hidden" id="editAlumnoUserId">
                <div class="form-group">
                    <label class="font-weight-bold text-muted small text-uppercase">Nombre Completo</label>
                    <input type="text" class="form-control form-control-custom focus-info" id="editAlumnoNombre" placeholder="Nombre real">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold text-muted small text-uppercase">Matrícula</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text bg-light"><i class="fas fa-id-card text-muted"></i></span></div>
                        <input type="number" class="form-control form-control-custom focus-success" id="editAlumnoMatricula" placeholder="Ej: 2134509">
                    </div>
                    <small class="form-text mt-2 text-primary" style="font-size: 0.8rem;">
                        <i class="fas fa-at mr-1"></i> <span id="editAlumnoEmailPreview">...@students.local</span>
                    </small>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0 pt-3">
                <button type="button" class="btn btn-secondary btn-custom px-4" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-custom px-4" onclick="guardarEdicionAlumno()">
                    <i class="fas fa-save mr-1"></i> Guardar Cambios
                </button>
            </div>
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
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Usar</button>
                </div>
            </form>
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

<script>
const SCHED_ID = <?php echo $data['schedule']->id; ?>;
</script>


<?php require APPROOT . '/views/inc/footer.php'; ?>