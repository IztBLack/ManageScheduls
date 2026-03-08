<?php
require_once '../FPDF/fpdf.php';

class Schedules extends Controller
{
    private $scheduleModel;
    private $studentModel;
    private $userModel;

    public function __construct()
    {
        // Protect all methods in this controller
        if (isset($_SESSION['must_change_password']) && $_SESSION['must_change_password'] === true) {
            redirect('users/change_password');
        }

        $this->scheduleModel = $this->model('Schedule');
        $this->studentModel  = $this->model('Student');
        $this->userModel     = $this->model('User');
        $this->attendanceModel = $this->model('Attendance');
    }

    // =========================================================
    // INDEX
    // =========================================================
    public function index()
    {
        $schedules = $this->scheduleModel->getSchedulesWithNames();
        $data = ['schedules' => $schedules];
        $this->view('schedules/index', $data);
    }

    // =========================================================
    // ADD
    // =========================================================
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $sanitize = fn($v) => htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8');

            $data = [
                'teacher_id'   => $_SESSION['user_id'],
                'subject_id'   => $sanitize($_POST['subject_id']   ?? ''),
                'grupo'        => $sanitize($_POST['grupo']         ?? ''),
                'turno'        => $sanitize($_POST['turno']         ?? ''),
                'aula'         => $sanitize($_POST['salon']         ?? ''),
                'periodo'      => $sanitize($_POST['periodo']       ?? ''),
                'especialidad' => $sanitize($_POST['especialidad']  ?? ''),
                'units'        => $_POST['units'] ?? [],
                'subjects'     => $this->scheduleModel->getSubjects(),
                'error'        => '',
                'warning'      => '',
            ];

            if (empty($data['subject_id']) || empty($data['grupo'])) {
                $data['error'] = 'La materia y el identificador del grupo son obligatorios.';
                $this->view('schedules/add', $data);
                return;
            }

            // Validar que el maestro no duplique el mismo grupo para la misma materia en el mismo periodo
            if ($this->scheduleModel->groupExists($data['teacher_id'], $data['subject_id'], $data['grupo'], $data['periodo'])) {
                $data['error'] = 'Ya existe un grupo registrado con esta materia, nombre y periodo.';
                $this->view('schedules/add', $data);
                return;
            }

            if (!empty($data['units'])) {
                foreach ($data['units'] as $index => $unit) {
                    $totalWeight  = 0;
                    $hasActivities = false;

                    foreach ($unit['activities'] as $act) {
                        if (!empty(trim($act['name'] ?? ''))) {
                            $hasActivities = true;
                            $totalWeight  += intval($act['weight'] ?? 0);
                        }
                    }

                    if (!$hasActivities) {
                        $data['error'] = "La Unidad $index no tiene actividades con nombre.";
                        $this->view('schedules/add', $data);
                        return;
                    }

                    if ($totalWeight > 100) {
                        $data['error'] = "La Unidad $index supera el 100% de ponderación (Actual: {$totalWeight}%).";
                        $this->view('schedules/add', $data);
                        return;
                    }

                    if ($totalWeight != 100) {
                        $data['warning'] = "Advertencia: una o más unidades no suman 100%. Puedes ajustar los pesos desde la edición del grupo.";
                    }
                }
            }

            if ($this->scheduleModel->addFullStructure($data)) {
                flash('schedule_message', '¡Grupo creado con éxito!');
                redirect('schedules/index');
            } else {
                $data['error'] = 'Error al guardar en la base de datos. Revisa los logs.';
                $this->view('schedules/add', $data);
            }

        } else {
            $data = [
                'subjects' => $this->scheduleModel->getSubjects(),
                'error'    => '',
                'warning'  => '',
            ];
            $this->view('schedules/add', $data);
        }
    }

    // =========================================================
    // EDIT
    // =========================================================
    public function edit($id)
    {
        $schedule = $this->scheduleModel->getScheduleById($id);

        if (!$schedule) {
            flash('error', 'Horario no encontrado', 'alert alert-danger');
            redirect('schedules/index');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return $this->handleEditPost($id);
        }

        return $this->showEditView($id, $schedule);
    }

    private function handleEditPost($scheduleId)
    {
        $action = $_POST['action'] ?? '';

        switch ($action) {

            // ── UNIDADES ──────────────────────────────────────
            case 'add_unit':
                $unitData = [
                    'schedule_id' => $scheduleId,
                    'nombre'      => trim($_POST['nombre'] ?? 'Nueva Unidad'),
                    'orden'       => intval($_POST['orden'] ?? $this->scheduleModel->getNextUnitOrder($scheduleId))
                ];
                if ($this->scheduleModel->addUnit($unitData)) {
                    flash('edit_message', 'Unidad agregada correctamente');
                } else {
                    flash('edit_error', 'Error al agregar la unidad', 'alert alert-danger');
                }
                break;

            case 'update_unit':
                if (isset($_POST['unit_id'])) {
                    $nombre = trim($_POST['nombre'] ?? '');
                    if (empty($nombre)) {
                        flash('edit_error', 'El nombre de la unidad no puede estar vacío', 'alert alert-danger');
                        break;
                    }

                    $unitData = [
                        'id'     => intval($_POST['unit_id']),
                        'nombre' => $nombre,
                        'orden'  => intval($_POST['orden'] ?? 1)
                    ];
                    if ($this->scheduleModel->updateUnit($unitData)) {
                        flash('edit_message', 'Unidad actualizada');
                    } else {
                        flash('edit_error', 'Error al actualizar la unidad', 'alert alert-danger');
                    }
                }
                break;

            case 'delete_unit':
                if (isset($_POST['unit_id'])) {
                    if ($this->scheduleModel->deleteUnit(intval($_POST['unit_id']))) {
                        flash('edit_message', 'Unidad eliminada');
                    } else {
                        flash('edit_error', 'Error al eliminar la unidad', 'alert alert-danger');
                    }
                }
                break;

            // ── ACTIVIDADES ───────────────────────────────────
            case 'add_activity':
                if (isset($_POST['unidad_id'])) {
                    $nombre = trim($_POST['nombre'] ?? '');
                    if (empty($nombre)) {
                        flash('edit_error', 'El nombre de la actividad es obligatorio', 'alert alert-danger');
                        break;
                    }

                    $activityData = [
                        'unidad_id'    => intval($_POST['unidad_id']),
                        'nombre'       => $nombre,
                        'ponderacion'  => intval($_POST['ponderacion'] ?? 0),
                        'fecha_entrega'=> !empty($_POST['fecha_entrega']) ? $_POST['fecha_entrega'] : null
                    ];
                    if ($this->scheduleModel->addActivity($activityData)) {
                        flash('edit_message', 'Actividad agregada');
                    } else {
                        flash('edit_error', 'Error al agregar la actividad', 'alert alert-danger');
                    }
                }
                break;

            case 'update_activity':
                if (isset($_POST['activity_id'])) {
                    $nombre = trim($_POST['nombre'] ?? '');
                    if (empty($nombre)) {
                        flash('edit_error', 'El nombre de la actividad no puede estar vacío', 'alert alert-danger');
                        break;
                    }

                    $activityData = [
                        'id'           => intval($_POST['activity_id']),
                        'nombre'       => $nombre,
                        'ponderacion'  => intval($_POST['ponderacion'] ?? 0),
                        'fecha_entrega'=> !empty($_POST['fecha_entrega']) ? $_POST['fecha_entrega'] : null
                    ];
                    if ($this->scheduleModel->updateActivity($activityData)) {
                        flash('edit_message', 'Actividad actualizada');
                    } else {
                        flash('edit_error', 'Error al actualizar la actividad', 'alert alert-danger');
                    }
                }
                break;

            case 'delete_activity':
                if (isset($_POST['activity_id'])) {
                    if ($this->scheduleModel->deleteActivity(intval($_POST['activity_id']))) {
                        flash('edit_message', 'Actividad eliminada');
                    } else {
                        flash('edit_error', 'Error al eliminar la actividad', 'alert alert-danger');
                    }
                }
                break;

            case 'update_activities_batch':
                if (isset($_POST['activities']) && is_array($_POST['activities'])) {
                    $success = true;
                    foreach ($_POST['activities'] as $activityId => $d) {
                        $activityData = [
                            'id'           => $activityId,
                            'nombre'       => trim($d['nombre']),
                            'ponderacion'  => intval($d['ponderacion']),
                            'fecha_entrega'=> !empty($d['fecha_entrega']) ? $d['fecha_entrega'] : null
                        ];
                        if (!$this->scheduleModel->updateActivity($activityData)) $success = false;
                    }
                    flash('edit_message',
                        $success ? 'Actividades actualizadas' : 'Algunas actividades no se actualizaron',
                        $success ? 'alert alert-success' : 'alert alert-warning');
                }
                break;

            // ── ALUMNOS ───────────────────────────────────────
            case 'add_student':
                if (isset($_POST['name'])) {
                    $nombre     = trim($_POST['name'] ?? '');
                    $matricula  = trim($_POST['matricula'] ?? '');
                    $inputEmail = trim($_POST['email'] ?? '');

                    if (empty($nombre)) {
                        flash('edit_error', 'El nombre del alumno es obligatorio', 'alert alert-danger');
                        break;
                    }

                    // Si viene matrícula numérica, construir email; si no, usar el email directo
                    $emailFinal = ($matricula && ctype_digit($matricula))
                        ? $matricula . '@students.local'
                        : $inputEmail;

                    if (empty($emailFinal) || !filter_var($emailFinal, FILTER_VALIDATE_EMAIL)) {
                        flash('edit_error', 'Se requiere una matrícula o un correo electrónico válido (ej: usuario@dominio.com)', 'alert alert-danger');
                        break;
                    }

                    $studentData = [
                        'schedule_id' => $scheduleId,
                        'name'        => $nombre,
                        'email'       => $emailFinal,
                        'password'    => password_hash($matricula ?: $inputEmail, PASSWORD_DEFAULT),
                        'rol'         => 'alumno'
                    ];
                    $result = $this->studentModel->addStudentToGroup($studentData);
                    if ($result === true) {
                        flash('edit_message', 'Estudiante agregado correctamente');
                    } elseif ($result === 'already_exists') {
                        flash('edit_error', 'El estudiante ya está inscrito en este grupo', 'alert alert-warning');
                    } else {
                        flash('edit_error', 'Error al agregar estudiante', 'alert alert-danger');
                    }
                }
                break;

            case 'remove_student':
                if (isset($_POST['inscripcion_id'])) {
                    if ($this->studentModel->removeFromGroup(intval($_POST['inscripcion_id']))) {
                        flash('edit_message', 'Estudiante eliminado del grupo');
                    } else {
                        flash('edit_error', 'Error al eliminar estudiante', 'alert alert-danger');
                    }
                }
                break;

            case 'import_students':
                $imported = 0;
                $errors   = 0;

                // Acepta tanto array POST como JSON string
                $students = [];
                if (isset($_POST['students']) && is_array($_POST['students'])) {
                    $students = $_POST['students'];
                } elseif (isset($_POST['students'])) {
                    $students = json_decode($_POST['students'], true) ?? [];
                }

                foreach ($students as $student) {
                    $studentData = [
                        'schedule_id' => $scheduleId,
                        'name'        => trim($student['name']     ?? ''),
                        'email'       => trim($student['email']    ?? ''),
                        'password'    => password_hash(trim($student['matricula'] ?? $student['email'] ?? ''), PASSWORD_DEFAULT),
                        'rol'         => 'alumno'
                    ];
                    $result = $this->studentModel->addStudentToGroup($studentData);
                    $result === true ? $imported++ : $errors++;
                }
                flash('edit_message', "$imported alumno(s) importado(s)" . ($errors ? ", $errors error(es)" : ''));
                break;

            // ── CONFIGURACIÓN ─────────────────────────────────
            case 'update_schedule':
                $scheduleData = [
                    'grupo'        => trim($_POST['grupo']        ?? ''),
                    'turno'        => trim($_POST['turno']        ?? ''),
                    'aula'         => trim($_POST['aula']         ?? ''),
                    'periodo'      => trim($_POST['periodo']      ?? ''),
                    'especialidad' => trim($_POST['especialidad'] ?? '')
                ];
                if ($this->scheduleModel->editSchedule($scheduleId, $scheduleData)) {
                    flash('edit_message', 'Información del grupo actualizada');
                } else {
                    flash('edit_error', 'Error al actualizar el grupo', 'alert alert-danger');
                }
                break;

            case 'update_student':
                if (isset($_POST['user_id'], $_POST['name'], $_POST['matricula'])) {
                    $result = $this->userModel->updateStudent([
                        'user_id' => intval($_POST['user_id']),
                        'name'    => trim($_POST['name']),
                        'email'   => trim($_POST['matricula']) . '@students.local',
                    ]);
                    if ($result) {
                        flash('edit_message', 'Alumno actualizado correctamente');
                    } else {
                        flash('edit_error', 'Error al actualizar alumno', 'alert alert-danger');
                    }
                }
                break;

            case 'delete_student_full':
                if (isset($_POST['user_id'])) {
                    // CASCADE en FK elimina inscripciones, resultados y bonus automáticamente
                    if ($this->userModel->deleteStudent(intval($_POST['user_id']))) {
                        flash('edit_message', 'Alumno eliminado permanentemente');
                    } else {
                        flash('edit_error', 'Error al eliminar alumno', 'alert alert-danger');
                    }
                }
                break;

            case 'reset_grades':
                if ($this->scheduleModel->resetGrades($scheduleId)) {
                    flash('edit_message', 'Calificaciones reiniciadas');
                } else {
                    flash('edit_error', 'Error al reiniciar calificaciones', 'alert alert-danger');
                }
                break;

            case 'archive_group':
                if ($this->scheduleModel->archiveSchedule($scheduleId)) {
                    flash('edit_message', 'Grupo archivado');
                    redirect('schedules/index');
                    return;
                } else {
                    flash('edit_error', 'Error al archivar grupo', 'alert alert-danger');
                }
                break;

            default:
                flash('edit_error', 'Acción no reconocida', 'alert alert-warning');
        }

        redirect('schedules/edit/' . $scheduleId);
    }

    private function showEditView($scheduleId, $schedule)
    {
        $unidades = $this->scheduleModel->getUnitsWithActivities($scheduleId);
        $students = $this->studentModel->getStudentsInGroup($scheduleId);

        foreach ($unidades as $unidad) {
            $unidad->total_ponderacion = 0;
            if (!empty($unidad->actividades)) {
                foreach ($unidad->actividades as $act) {
                    $unidad->total_ponderacion += $act->ponderacion;
                }
            }
        }

        $data = [
            'schedule'         => $schedule,
            'unidades'         => $unidades,
            'students'         => $students,
            'total_unidades'   => count($unidades),
            'total_students'   => count($students),
            'total_actividades'=> $this->scheduleModel->countActivitiesBySchedule($scheduleId),
            'editMode'         => true
        ];

        $this->view('schedules/edit', $data);
    }

    // =========================================================
    // AJAX
    // =========================================================
    public function ajax($action = '')
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso no permitido']);
            return;
        }

        header('Content-Type: application/json');

        switch ($action) {
            case 'update_activity_field': $this->ajaxUpdateActivityField(); break;
            case 'get_unit_details':      $this->ajaxGetUnitDetails();      break;
            case 'validate_weights':      $this->ajaxValidateWeights();     break;
            case 'search_students':       $this->ajaxSearchStudents();      break;
            default: echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        }
    }

    private function ajaxUpdateActivityField()
    {
        if (!isset($_POST['id'], $_POST['field'], $_POST['value'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $activityId = intval($_POST['id']);
        $field      = $_POST['field'];
        $value      = $_POST['value'];
        $data       = ['id' => $activityId];

        switch ($field) {
            case 'nombre':      $data['nombre']       = trim($value);                              break;
            case 'ponderacion': $data['ponderacion']  = intval($value);                            break;
            case 'fecha':       $data['fecha_entrega']= !empty($value) ? $value : null;            break;
            default:
                echo json_encode(['success' => false, 'error' => 'Campo inválido']);
                return;
        }

        echo json_encode($this->scheduleModel->updateActivity($data)
            ? ['success' => true]
            : ['success' => false, 'error' => 'Error al actualizar']);
    }

    private function ajaxGetUnitDetails()
    {
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }
        $unit = $this->scheduleModel->getUnitById(intval($_GET['id']));
        echo json_encode($unit
            ? ['success' => true, 'data' => $unit]
            : ['success' => false, 'error' => 'Unidad no encontrada']);
    }

    private function ajaxValidateWeights()
    {
        if (!isset($_GET['schedule_id'])) {
            echo json_encode(['success' => false, 'error' => 'ID de horario no proporcionado']);
            return;
        }

        $unidades = $this->scheduleModel->getUnitsWithActivities(intval($_GET['schedule_id']));
        $result   = ['success' => true, 'unidades' => [], 'total_general' => 0];

        foreach ($unidades as $unidad) {
            $total = 0;
            if (!empty($unidad->actividades)) {
                foreach ($unidad->actividades as $act) $total += $act->ponderacion;
            }
            $result['unidades'][] = [
                'id'     => $unidad->id,
                'nombre' => $unidad->nombre,
                'total'  => $total,
                'estado' => $total == 100 ? 'ok' : ($total > 100 ? 'exceso' : 'falta')
            ];
            $result['total_general'] += $total;
        }

        echo json_encode($result);
    }

    private function ajaxSearchStudents()
    {
        if (!isset($_GET['q'])) {
            echo json_encode(['success' => false, 'error' => 'Término de búsqueda no proporcionado']);
            return;
        }
        $students = $this->userModel->searchUsersByRole('alumno', trim($_GET['q']));
        echo json_encode(['success' => true, 'data' => $students]);
    }

    // =========================================================
    // GRADES
    // =========================================================
    public function grades($id)
    {
        $editMode = isset($_GET['edit']) && $_GET['edit'] == 1;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['calif'])) {
                foreach ($_POST['calif'] as $inscripcion_id => $actividades) {
                    foreach ($actividades as $actividadId => $nota) {
                        if ($nota === '' || $nota === null) continue; // saltar vacíos
                        $this->studentModel->saveGrade($inscripcion_id, $actividadId, round((float)$nota));
                    }
                }
                flash('grade_message', 'Calificaciones registradas');
            }

            if (isset($_POST['bonus'])) {
                foreach ($_POST['bonus'] as $inscripcion_id => $unidades) {
                    foreach ($unidades as $unidad_id => $puntos) {
                        if ($puntos === '' || $puntos === null) continue; // saltar vacíos
                        $this->studentModel->saveBonus($inscripcion_id, $unidad_id, round((float)$puntos));
                    }
                }
            }

            redirect('schedules/grades/' . $id);
            return;
        }

        $unidades            = $this->scheduleModel->getUnitsBySchedule($id);
        $actividadesPorUnidad= $this->scheduleModel->getActivitiesByUnit($id);

        $todasActividades = [];
        foreach ($actividadesPorUnidad as $unidad) {
            foreach ($unidad['actividades'] as $actividad) {
                $todasActividades[] = $actividad;
            }
        }

        $data = [
            'schedule'            => $this->scheduleModel->getScheduleById($id),
            'unidades'            => $unidades,
            'activities'          => $todasActividades,
            'actividadesPorUnidad'=> $actividadesPorUnidad,
            'students'            => $this->studentModel->getStudentsInGroup($id),
            'grades'              => $this->studentModel->getGradesBySchedule($id),
            'bonusPorUnidad'      => $this->studentModel->getBonusBySchedule($id),
            'editMode'            => $editMode
        ];

        $this->view('schedules/grades', $data);
    }

    // =========================================================
    // IMPORT (vista independiente)
    // =========================================================
    public function import($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $schedule_id = intval($_POST['id_grupo'] ?? 0);

            if (!isset($_POST['alumnos']) || empty($_POST['alumnos'])) {
                flash('schedule_message', 'No hay datos de alumnos para procesar.', 'alert alert-danger');
                redirect('schedules/index');
                return;
            }

            $added   = 0;
            $skipped = 0;

            foreach ($_POST['alumnos'] as $alumno) {
                $matricula = trim($alumno['matricula']);
                $nombre    = trim($alumno['nombre']);

                if (empty($matricula) || empty($nombre)) { $skipped++; continue; }

                $email = $matricula . '@students.local';
                $user  = $this->userModel->getUserByEmail($email);

                if (!$user) {
                    $userData = [
                        'name'     => $nombre,
                        'email'    => $email,
                        'password' => password_hash($matricula, PASSWORD_DEFAULT),
                        'rol'      => 'alumno'
                    ];
                    if ($this->userModel->register($userData)) {
                        $user = $this->userModel->getUserByEmail($email);
                    } else {
                        $skipped++;
                        continue;
                    }
                }

                if ($user) {
                    $this->studentModel->registerInGroup($schedule_id, $user->id) ? $added++ : $skipped++;
                }
            }

            flash('schedule_message', "Importación completada: $added inscritos, $skipped omitidos.");
            redirect('schedules/index');

        } else {
            $data = ['id_grupo' => $id];
            $this->view('schedules/import', $data);
        }
    }

    // =========================================================
    // VISUALIZAR / PDF
    // =========================================================
    public function visu($id)
    {
        $schedule = $this->scheduleModel->getScheduleById($id);
        $schedule ? $this->view('schedules/visu', $schedule) : redirect('schedules/index');
    }

    public function down($id)
    {
        $schedule = $this->scheduleModel->getScheduleById($id);
        $schedule ? $this->generatePDF($schedule) : redirect('schedules/index');
    }

    // =========================================================
    // PASE DE LISTA
    // =========================================================
    public function attendance($schedule_id) {
        $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
        
        $schedule = $this->scheduleModel->getScheduleById($schedule_id);

        if (!$schedule) {
            redirect('schedules/index');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $fecha_post = $_POST['fecha'];
            
            if(isset($_POST['attendance'])) {
                foreach($_POST['attendance'] as $inscripcion_id => $estado) {
                    $this->attendanceModel->saveAttendance($inscripcion_id, $fecha_post, $estado);
                }
                flash('attendance_message', 'Lista de asistencia guardada correctamente para el ' . $fecha_post);
            }
            
            redirect('schedules/attendance/' . $schedule_id . '?fecha=' . $fecha_post);
        } else {
            $students = $this->attendanceModel->getStudentsForAttendance($schedule_id);
            $attendance_data = $this->attendanceModel->getAttendanceByDate($schedule_id, $fecha);
            
            // Nuevos datos para el historial
            $all_dates = $this->attendanceModel->getAttendanceDates($schedule_id);
            $all_records = $this->attendanceModel->getAllAttendanceRecords($schedule_id);
            
            // Modo edición
            $editMode = isset($_GET['edit']) ? true : false;
            
            $data = [
                'schedule' => $schedule,
                'fecha' => $fecha,
                'students' => $students,
                'attendance_data' => $attendance_data,
                'all_dates' => $all_dates,
                'all_records' => $all_records,
                'editMode' => $editMode
            ];
            
            $this->view('schedules/attendance', $data);
        }
    }

    public function deleteAttendanceDate($schedule_id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fecha = trim($_POST['fecha']);
            if($this->attendanceModel->deleteRecordsByDate($schedule_id, $fecha)) {
                flash('attendance_message', 'Registros de asistencia eliminados para el ' . $fecha);
            } else {
                flash('attendance_error', 'Algo salió mal al eliminar los registros', 'alert alert-danger');
            }
            redirect('schedules/attendance/' . $schedule_id);
        } else {
            redirect('schedules/attendance/' . $schedule_id);
        }
    }

    private function generatePDF($schedule)
    {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Visualizar Horario', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetX(10);

        $details = [
            'Periodo Escolar' => $schedule['periodo_escolar'],
            'Turno'           => $schedule['turno'],
            'Tutor'           => $schedule['tutor'],
            'Grupo'           => $schedule['grupo'],
            'Especialidad'    => $schedule['especialidad'],
            'Nivel'           => $schedule['nivel'],
            'Salon'           => $schedule['salon'],
        ];

        foreach ($details as $field => $value) {
            $pdf->Cell(40, 10, $field, 1, 0, 'L');
            $pdf->Cell(60, 10, $value, 1, 0, 'L');
            $pdf->Ln();
        }

        $pdf->Output('D', 'horario.pdf');
        exit();
    }
}