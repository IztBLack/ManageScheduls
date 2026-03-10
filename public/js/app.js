/* ==============================================
   ASISTENCIAS (attendance.php)
   ============================================== */
function confirmarEliminacion(fecha, fechaF) {
    if(typeof Swal === 'undefined') return;
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Vas a eliminar permanentemente el pase de lista del " + fechaF + ".",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-trash"></i> Sí, eliminar columna',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-fecha-input').value = fecha;
            document.getElementById('delete-form').submit();
        }
    });
}

/* ==============================================
   FILTRO Y ORDENAMIENTO (Global - Grades, Edit, Attendance)
   ============================================== */
let sortOrder = 'default';

// Guardar orden original al cargar en Edit
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
    const searchInput = document.getElementById('alumnoSearch');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
    
    // Buscar contenedor (tabla en Grades/Attendance, o div list en Edit)
    const tableGrades = document.querySelector('.grades-table tbody');
    const tableAttendance = document.querySelector('.table-responsive-custom tbody');
    const divContainer = document.getElementById('studentListContainer');
    
    // El contenedor real actual en esta vista
    const container = tableGrades || tableAttendance || divContainer;
    
    if (!container) return;

    const items = Array.from(container.querySelectorAll('tr.student-item, div.student-item'));
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
        visiblesYListos.sort((a, b) => parseInt(a.dataset.index) - parseInt(b.dataset.index));
    }

    visiblesYListos.forEach(item => container.appendChild(item));

    // Extras de Edit.php (Controles)
    const contador = document.getElementById('alumnoContadorFiltro');
    const noResults = document.getElementById('noResultsMsg');
    const totalVisibles = items.filter(i => i.style.display !== 'none').length;
    
    if (noResults) {
        noResults.style.display = (totalVisibles === 0 && query) ? 'block' : 'none';
        if (totalVisibles === 0 && query) {
            document.getElementById('noResultsTerm').textContent = query;
        }
    }
    
    if (contador) {
        contador.textContent = query 
            ? `${totalVisibles} de ${items.length}` 
            : (items.length > 0 ? `${items.length} alumno(s)` : '');
    }
}

/* ==============================================
   CÁLCULO DE CALIFICACIONES (grades.php)
   ============================================== */
document.addEventListener("input", function(e) {
    if (e.target.classList.contains("nota") || e.target.classList.contains("bonus-unidad")) {
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
    let filas = document.querySelectorAll("tbody tr.student-item");
    if(filas.length === 0 || !document.querySelector(".nota")) return;
    
    let sumaGrupo = 0;
    let contador = 0;

    filas.forEach(row => {
        let notas = row.querySelectorAll(".nota");
        let notasPorUnidad = {};

        notas.forEach(input => {
            let unidadId = input.dataset.unidadId;
            let valor = parseFloat(input.value) || 0;
            let peso = parseFloat(input.dataset.weight);

            if (!notasPorUnidad[unidadId]) notasPorUnidad[unidadId] = { sumaPonderada: 0, sumaPesos: 0 };
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
        let promedioCalifUnidad = calificacionesUnidad.length > 0 ? sumaCalifUnidad / calificacionesUnidad.length : 0;
        
        let sumaBonuses = 0;
        bonuses.forEach(input => { sumaBonuses += parseFloat(input.value) || 0; });

        let final = promedioCalifUnidad + sumaBonuses;
        let finalCell = row.querySelector(".final");
        
        if(finalCell) {
            finalCell.innerText = Math.round(final);
            finalCell.classList.remove("bg-success", "bg-warning", "bg-danger");
            if (final >= 90) finalCell.classList.add("bg-success");
            else if (final >= 70) finalCell.classList.add("bg-warning");
            else finalCell.classList.add("bg-danger");
        }

        sumaGrupo += final;
        contador++;
    });

    let promediogElement = document.getElementById("promedioGrupo");
    if(promediogElement && contador > 0) {
        promediogElement.innerText = Math.round(sumaGrupo / contador);
    }
}
window.addEventListener("load", calcularTodo);


/* ==============================================
   NUEVA MATERIA ASÍNCRONA (add.php)
   ============================================== */
$(document).ready(function() {
    $('#addEspecialidadForm').on('submit', function(e) {
        e.preventDefault();
        const inputName = $('#especialidadNameInput').val().trim();
        if(inputName.length === 0) return;
        
        // Agregar y seleccionar la nueva especialidad sin recargar la página
        $('#especialidadSelect').append(new Option(inputName, inputName, true, true));
        $('#addEspecialidadModal').modal('hide');
        $('#addEspecialidadForm')[0].reset();
    });

    $('#addInstitucionForm').on('submit', function(e) {
        e.preventDefault();
        const inputName = $('#institucionNameInput').val().trim();
        if(inputName.length === 0) return;
        
        $('#institucionSelect').append(new Option(inputName, inputName, true, true));
        $('#addInstitucionModal').modal('hide');
        $('#addInstitucionForm')[0].reset();
    });

    $('#addSubjectForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            url: URLROOT + '/schedules/ajax/add_subject',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#subjectSelect').append(new Option(res.subject_name, res.id, true, true));
                    $('#addSubjectModal').modal('hide');
                    $('#addSubjectForm')[0].reset();
                } else {
                    alert(res.error || 'Error al guardar la materia');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("Error de conexión:\n" + textStatus + "\n" + jqXHR.responseText);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Materia');
            }
        });
    });

    $('#addSubjectModal').on('hidden.bs.modal', function () {
        $(this).removeClass('show');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    });
});

/* ==============================================
   ESTRUCTURA Y CONFIGURACIÓN (edit.php)
   ============================================== */

// TABS con persistencia
$(document).ready(function () {
    $('#configTabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
        localStorage.setItem('lastTab', $(this).attr('href'));
    });
    const last = localStorage.getItem('lastTab');
    if (last) $(`#configTabs a[href="${last}"]`).tab('show');
});

// HELPER: POST + recarga
function enviarYRecargar(formData, mensajeExito, onSuccess) {
    if(typeof SCHED_ID === 'undefined') return;
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

// NOTIFICACIÓN
function mostrarNotificacion(mensaje, tipo = 'success', tiempo = 3000) {
    const n = document.createElement('div');
    n.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
    n.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:250px;';
    n.innerHTML = `${mensaje}<button type="button" class="close" onclick="this.parentElement.remove()"><span>&times;</span></button>`;
    document.body.appendChild(n);
    setTimeout(() => n.parentNode && n.remove(), tiempo);
}

// MODAL UNIDAD
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

// MODAL ACTIVIDAD
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
    if (isNaN(ponderacion) || ponderacion < 0 || ponderacion > 100) { mostrarNotificacion('La ponderación debe ser entre 0 y 100', 'warning'); return; }
    const fd = new FormData();
    fd.append('action',      'add_activity');
    fd.append('unidad_id',   unidadId);
    fd.append('nombre',      nombre);
    fd.append('ponderacion', ponderacion);
    if (fecha) fd.append('fecha_entrega', fecha);
    enviarYRecargar(fd, 'Actividad agregada', () => $('#modalActividad').modal('hide'));
}

// ACTUALIZAR ACTIVIDAD INLINE
function actualizarActividad(id, campo, valor) {
    if(typeof URLROOT === 'undefined') return;
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

// DUPLICAR ACTIVIDAD
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

// ALUMNOS MANUAL Y CSV IMPORT
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

if(document.getElementById('importar_archivo')) {
    document.getElementById('importar_archivo').addEventListener('change', function () {
        document.getElementById('importar_label').textContent = this.files[0]?.name || 'Seleccionar archivo...';
    });
}

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

// CONFIRMACION GENERICA
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
            if(typeof SCHED_ID !== 'undefined') {
                fetch(`${URLROOT}/schedules/edit/${SCHED_ID}`, { method: 'POST', body: fd, redirect: 'follow' })
                    .then(() => { mostrarNotificacion('Grupo archivado', 'success'); setTimeout(() => window.location.href = `${URLROOT}/schedules/index`, 1500); });
            }
            return;
    }
}

function guardarConfiguracion() {
    const fd = new FormData();
    fd.append('action',  'update_schedule');
    fd.append('grupo',   $('#cfg_grupo').val());
    fd.append('periodo', $('#cfg_periodo').val());
    fd.append('aula',    $('#cfg_aula').val());
    fd.append('especialidad', $('#cfg_especialidad').val());
    enviarYRecargar(fd, 'Configuración guardada');
}

function calcularPesos() {
    if(typeof SCHED_ID === 'undefined') return;
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

function abrirModalEditarAlumno(userId, nombre, matricula) {
    $('#editAlumnoUserId').val(userId);
    $('#editAlumnoNombre').val(nombre);
    $('#editAlumnoMatricula').val(matricula);
    $('#editAlumnoEmailPreview').text((matricula || '...') + '@students.local');
    $('#modalEditarAlumno').modal('show');
}
if(document.getElementById('editAlumnoMatricula')) {
    document.getElementById('editAlumnoMatricula').addEventListener('input', function () {
        document.getElementById('editAlumnoEmailPreview').textContent =
            (this.value.trim() || '...') + '@students.local';
    });
}
function guardarEdicionAlumno() {
    const userId    = $('#editAlumnoUserId').val();
    const nombre    = $('#editAlumnoNombre').val().trim();
    const matricula = $('#editAlumnoMatricula').val().trim();
    if (!nombre || !matricula) { mostrarNotificacion('Nombre y matrícula son obligatorios', 'warning'); return; }
    if (!/^\d+$/.test(matricula)) { mostrarNotificacion('La matrícula debe ser numérica', 'warning'); return; }

    const fd = new FormData();
    fd.append('action',    'update_student');
    fd.append('user_id',   userId);
    fd.append('name',      nombre);
    fd.append('matricula', matricula);
    enviarYRecargar(fd, 'Alumno actualizado', () => $('#modalEditarAlumno').modal('hide'));
}
function exportarLista() {
    if(typeof SCHED_ID !== 'undefined') window.location.href = `${URLROOT}/students/export/${SCHED_ID}`;
}
