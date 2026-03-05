<?php
require_once '../FPDF/fpdf.php';

class Schedules extends Controller
{
    private $scheduleModel;
    private $studentModel;
    private $userModel;

    public function __construct()
    {
        $this->scheduleModel = $this->model('Schedule');
        $this->studentModel = $this->model('Student');
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        $schedules = $this->scheduleModel->getSchedulesWithNames();
        $data = ['schedules' => $schedules];
        $this->view('schedules/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Log para debugging
            error_log('Schedules::add POST raw: ' . print_r($_POST, true));

            // Sanitizar datos
            $clean = [];
            $keysToClean = ['subject_id', 'grupo', 'turno', 'salon', 'periodo', 'especialidad'];
            foreach ($keysToClean as $k) {
                if (isset($_POST[$k])) {
                    $clean[$k] = filter_var($_POST[$k], FILTER_SANITIZE_STRING);
                }
            }

            $data = [
                'teacher_id'   => $_SESSION['user_id'],
                'subject_id'   => trim($clean['subject_id'] ?? ''),
                'grupo'        => trim($clean['grupo'] ?? ''),
                'turno'        => trim($clean['turno'] ?? ''),
                'aula'         => trim($clean['salon'] ?? ''),
                'periodo'      => trim($clean['periodo'] ?? ''),
                'especialidad' => trim($clean['especialidad'] ?? ''),
                'units'        => $_POST['units'] ?? []
            ];

            // Validar unidades
            if (empty($data['units'])) {
                flash('schedule_message', 'Debe agregar al menos una unidad con actividades', 'alert alert-danger');
                $data['subjects'] = $this->scheduleModel->getSubjects();
                $this->view('schedules/add', $data);
                return;
            }

            // Verificar pesos por unidad
            $hasWarning = false;
            foreach ($data['units'] as $index => $unit) {
                $totalUnitWeight = 0;
                foreach ($unit['activities'] as $activity) {
                    $totalUnitWeight += floatval($activity['weight']);
                }

                if ($totalUnitWeight == 0) {
                    flash('schedule_message', "Error en Unidad $index: Debe haber al menos una actividad con peso mayor a 0.", 'alert alert-danger');
                    $data['subjects'] = $this->scheduleModel->getSubjects();
                    $this->view('schedules/add', $data);
                    return;
                }

                if ($totalUnitWeight > 100) {
                    flash('schedule_message', "Error en Unidad $index: La suma de pesos no puede superar 100% (Actual: $totalUnitWeight%)", 'alert alert-danger');
                    $data['subjects'] = $this->scheduleModel->getSubjects();
                    $this->view('schedules/add', $data);
                    return;
                }

                if ($totalUnitWeight != 100) {
                    $hasWarning = true;
                }
            }

            if ($hasWarning) {
                flash('schedule_message', 'Advertencia: una o más unidades no suman 100%. Se guardará el registro, revisa los porcentajes.', 'alert alert-warning');
            }

            if ($this->scheduleModel->addFullStructure($data)) {
                flash('schedule_message', '¡Grupo y Plan de Evaluación registrados con éxito!');
                redirect('schedules/index');
            } else {
                flash('schedule_message', 'Error al guardar en la base de datos', 'alert alert-danger');
                $data['subjects'] = $this->scheduleModel->getSubjects();
                $this->view('schedules/add', $data);
            }
        } else {
            $subjects = $this->scheduleModel->getSubjects();
            $data = ['subjects' => $subjects];
            $this->view('schedules/add', $data);
        }
    }

    /**
     * EDIT - VERSIÓN MEJORADA PARA GESTIÓN COMPLETA DE LA CLASE
     */
    public function edit($id)
    {
        // Obtener datos del schedule
        $schedule = $this->scheduleModel->getScheduleById($id);
        
        if (!$schedule) {
            flash('error', 'Horario no encontrado', 'alert alert-danger');
            redirect('schedules/index');
            return;
        }

        // Procesar POST requests
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return $this->handleEditPost($id);
        }

        // GET request - mostrar vista de edición
        return $this->showEditView($id, $schedule);
    }

    /**
     * Manejar todas las operaciones POST en edit
     */
    private function handleEditPost($scheduleId)
    {
        // Detectar acción a realizar
        $action = $_POST['action'] ?? '';

        switch ($action) {
            // ===== OPERACIONES CON UNIDADES =====
            case 'add_unit':
                $unitData = [
                    'schedule_id' => $scheduleId,
                    'nombre' => trim($_POST['nombre'] ?? 'Nueva Unidad'),
                    'orden' => intval($_POST['orden'] ?? $this->scheduleModel->getNextUnitOrder($scheduleId))
                ];
                
                if ($this->scheduleModel->addUnit($unitData)) {
                    flash('edit_message', 'Unidad agregada correctamente');
                } else {
                    flash('edit_error', 'Error al agregar la unidad', 'alert alert-danger');
                }
                break;

            case 'update_unit':
                if (isset($_POST['unit_id'])) {
                    $unitData = [
                        'id' => intval($_POST['unit_id']),
                        'nombre' => trim($_POST['nombre']),
                        'orden' => intval($_POST['orden'] ?? 1)
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
                    $unitId = intval($_POST['unit_id']);
                    
                    if ($this->scheduleModel->deleteUnit($unitId)) {
                        flash('edit_message', 'Unidad eliminada');
                    } else {
                        flash('edit_error', 'Error al eliminar la unidad', 'alert alert-danger');
                    }
                }
                break;

            // ===== OPERACIONES CON ACTIVIDADES =====
            case 'add_activity':
                if (isset($_POST['unidad_id'])) {
                    $activityData = [
                        'unidad_id' => intval($_POST['unidad_id']),
                        'nombre' => trim($_POST['nombre'] ?? 'Nueva Actividad'),
                        'ponderacion' => intval($_POST['ponderacion'] ?? 0),
                        'fecha_entrega' => !empty($_POST['fecha_entrega']) ? $_POST['fecha_entrega'] : null
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
                    $activityData = [
                        'id' => intval($_POST['activity_id']),
                        'nombre' => trim($_POST['nombre']),
                        'ponderacion' => intval($_POST['ponderacion'] ?? 0),
                        'fecha_entrega' => !empty($_POST['fecha_entrega']) ? $_POST['fecha_entrega'] : null
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
                    $activityId = intval($_POST['activity_id']);
                    
                    if ($this->scheduleModel->deleteActivity($activityId)) {
                        flash('edit_message', 'Actividad eliminada');
                    } else {
                        flash('edit_error', 'Error al eliminar la actividad', 'alert alert-danger');
                    }
                }
                break;

            case 'update_activities_batch':
                if (isset($_POST['activities']) && is_array($_POST['activities'])) {
                    $success = true;
                    foreach ($_POST['activities'] as $activityId => $data) {
                        $activityData = [
                            'id' => $activityId,
                            'nombre' => trim($data['nombre']),
                            'ponderacion' => intval($data['ponderacion']),
                            'fecha_entrega' => !empty($data['fecha_entrega']) ? $data['fecha_entrega'] : null
                        ];
                        
                        if (!$this->scheduleModel->updateActivity($activityData)) {
                            $success = false;
                        }
                    }
                    
                    flash('edit_message', $success ? 'Actividades actualizadas' : 'Algunas actividades no se actualizaron', 
                          $success ? 'alert alert-success' : 'alert alert-warning');
                }
                break;

            // ===== OPERACIONES CON ESTUDIANTES =====
            case 'add_student':
                if (isset($_POST['name'], $_POST['email'])) {
                    $studentData = [
                        'schedule_id' => $scheduleId,
                        'name' => trim($_POST['name']),
                        'email' => trim($_POST['email']),
                        'password' => password_hash(trim($_POST['matricula'] ?? $_POST['email']), PASSWORD_DEFAULT),
                        'rol' => 'alumno'
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
                    $inscripcionId = intval($_POST['inscripcion_id']);
                    
                    if ($this->studentModel->removeFromGroup($inscripcionId)) {
                        flash('edit_message', 'Estudiante eliminado del grupo');
                    } else {
                        flash('edit_error', 'Error al eliminar estudiante', 'alert alert-danger');
                    }
                }
                break;

            case 'import_students':
                if (isset($_POST['students']) && is_array($_POST['students'])) {
                    $imported = 0;
                    $errors = 0;
                    
                    foreach ($_POST['students'] as $student) {
                        $studentData = [
                            'schedule_id' => $scheduleId,
                            'name' => trim($student['name']),
                            'email' => trim($student['email']),
                            'password' => password_hash(trim($student['matricula'] ?? $student['email']), PASSWORD_DEFAULT),
                            'rol' => 'alumno'
                        ];
                        
                        $result = $this->studentModel->addStudentToGroup($studentData);
                        
                        if ($result === true) {
                            $imported++;
                        } else {
                            $errors++;
                        }
                    }
                    
                    flash('edit_message', "$imported estudiantes importados, $errors errores");
                }
                break;

            // ===== OPERACIONES DE CONFIGURACIÓN =====
            case 'update_schedule':
                $scheduleData = [
                    'grupo' => trim($_POST['grupo'] ?? ''),
                    'turno' => trim($_POST['turno'] ?? ''),
                    'aula' => trim($_POST['aula'] ?? ''),
                    'periodo' => trim($_POST['periodo'] ?? ''),
                    'especialidad' => trim($_POST['especialidad'] ?? '')
                ];
                
                if ($this->scheduleModel->editSchedule($scheduleId, $scheduleData)) {
                    flash('edit_message', 'Información del grupo actualizada');
                } else {
                    flash('edit_error', 'Error al actualizar el grupo', 'alert alert-danger');
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

    /**
     * Mostrar vista de edición con todos los datos necesarios
     */
    private function showEditView($scheduleId, $schedule)
    {
        // Obtener unidades con actividades
        $unidades = $this->scheduleModel->getUnitsWithActivities($scheduleId);
        
        // Obtener estudiantes inscritos
        $students = $this->studentModel->getStudentsInGroup($scheduleId);
        
        // Calcular totales por unidad
        foreach ($unidades as $unidad) {
            $unidad->total_ponderacion = 0;
            if (!empty($unidad->actividades)) {
                foreach ($unidad->actividades as $act) {
                    $unidad->total_ponderacion += $act->ponderacion;
                }
            }
        }

        $data = [
            'schedule' => $schedule,
            'unidades' => $unidades,
            'students' => $students,
            'total_unidades' => count($unidades),
            'total_students' => count($students),
            'total_actividades' => $this->scheduleModel->countActivitiesBySchedule($scheduleId),
            'editMode' => true
        ];

        $this->view('schedules/edit', $data);
    }

    /**
     * AJAX endpoints para operaciones en tiempo real
     */
    public function ajax($action = '')
    {
        // Verificar que sea petición AJAX
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso no permitido']);
            return;
        }

        header('Content-Type: application/json');

        switch ($action) {
            case 'update_activity_field':
                $this->ajaxUpdateActivityField();
                break;

            case 'get_unit_details':
                $this->ajaxGetUnitDetails();
                break;

            case 'validate_weights':
                $this->ajaxValidateWeights();
                break;

            case 'search_students':
                $this->ajaxSearchStudents();
                break;

            default:
                echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        }
    }

    private function ajaxUpdateActivityField()
    {
        if (!isset($_POST['id'], $_POST['field'], $_POST['value'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $activityId = intval($_POST['id']);
        $field = $_POST['field'];
        $value = $_POST['value'];

        $data = ['id' => $activityId];

        switch ($field) {
            case 'nombre':
                $data['nombre'] = trim($value);
                break;
            case 'ponderacion':
                $data['ponderacion'] = intval($value);
                break;
            case 'fecha':
                $data['fecha_entrega'] = !empty($value) ? $value : null;
                break;
            default:
                echo json_encode(['success' => false, 'error' => 'Campo inválido']);
                return;
        }

        if ($this->scheduleModel->updateActivity($data)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
        }
    }

    private function ajaxGetUnitDetails()
    {
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }

        $unitId = intval($_GET['id']);
        $unit = $this->scheduleModel->getUnitById($unitId);

        if ($unit) {
            echo json_encode(['success' => true, 'data' => $unit]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Unidad no encontrada']);
        }
    }

    private function ajaxValidateWeights()
    {
        if (!isset($_GET['schedule_id'])) {
            echo json_encode(['success' => false, 'error' => 'ID de horario no proporcionado']);
            return;
        }

        $scheduleId = intval($_GET['schedule_id']);
        $unidades = $this->scheduleModel->getUnitsWithActivities($scheduleId);

        $result = [
            'success' => true,
            'unidades' => [],
            'total_general' => 0
        ];

        foreach ($unidades as $unidad) {
            $total = 0;
            if (!empty($unidad->actividades)) {
                foreach ($unidad->actividades as $act) {
                    $total += $act->ponderacion;
                }
            }

            $result['unidades'][] = [
                'id' => $unidad->id,
                'nombre' => $unidad->nombre,
                'total' => $total,
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

        $query = trim($_GET['q']);
        $students = $this->userModel->searchUsersByRole('alumno', $query);

        echo json_encode(['success' => true, 'data' => $students]);
    }

    // ===== MÉTODOS EXISTENTES (sin cambios) =====
    
    public function grades($id)
    {
        $editMode = isset($_GET['edit']) && $_GET['edit'] == 1;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['calif'])) {
                foreach ($_POST['calif'] as $inscripcion_id => $actividades) {
                    foreach ($actividades as $actividadId => $nota) {
                        $nota = round($nota);
                        $this->studentModel->saveGrade($inscripcion_id, $actividadId, $nota);
                    }
                }
                flash('grade_message', 'Calificaciones registradas');
            }
            
            if (isset($_POST['bonus'])) {
                foreach ($_POST['bonus'] as $inscripcion_id => $unidades) {
                    foreach ($unidades as $unidad_id => $puntos) {
                        if (!empty($puntos) || $puntos === '0') {
                            $puntos = round($puntos);
                            $this->studentModel->saveBonus($inscripcion_id, $unidad_id, $puntos);
                        }
                    }
                }
            }
            
            redirect('schedules/grades/' . $id);
            return;
        }

        $unidades = $this->scheduleModel->getUnitsBySchedule($id);
        $actividadesPorUnidad = $this->scheduleModel->getActivitiesByUnit($id);
        
        $todasActividades = [];
        foreach ($actividadesPorUnidad as $unidad) {
            foreach ($unidad['actividades'] as $actividad) {
                $todasActividades[] = $actividad;
            }
        }

        $students = $this->studentModel->getStudentsInGroup($id);
        $grades = $this->studentModel->getGradesBySchedule($id);
        $bonusPorUnidad = $this->studentModel->getBonusBySchedule($id);

        $data = [
            'schedule' => $this->scheduleModel->getScheduleById($id),
            'unidades' => $unidades,
            'activities' => $todasActividades,
            'actividadesPorUnidad' => $actividadesPorUnidad,
            'students' => $students,
            'grades' => $grades,
            'bonusPorUnidad' => $bonusPorUnidad,
            'editMode' => $editMode
        ];

        $this->view('schedules/grades', $data);
    }

    public function import($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $schedule_id = intval($_POST['id_grupo'] ?? 0);
            
            if (!isset($_POST['alumnos']) || empty($_POST['alumnos'])) {
                flash('schedule_message', 'No hay datos de alumnos para procesar.', 'alert alert-danger');
                redirect('schedules/index');
                return;
            }

            $added = 0;
            $skipped = 0;

            foreach ($_POST['alumnos'] as $alumno) {
                $matricula = trim($alumno['matricula']);
                $nombre = trim($alumno['nombre']);

                if (empty($matricula) || empty($nombre)) {
                    $skipped++;
                    continue;
                }

                $email = $matricula . '@students.local';
                
                $user = $this->userModel->getUserByEmail($email);

                if (!$user) {
                    $tempPass = password_hash($matricula, PASSWORD_DEFAULT);
                    $userData = [
                        'name' => $nombre,
                        'email' => $email,
                        'password' => $tempPass,
                        'rol' => 'alumno'
                    ];
                    
                    if ($this->userModel->register($userData)) {
                        $user = $this->userModel->getUserByEmail($email);
                    } else {
                        $skipped++;
                        continue;
                    }
                }

                if ($user) {
                    if ($this->studentModel->registerInGroup($schedule_id, $user->id)) {
                        $added++;
                    } else {
                        $skipped++;
                    }
                }
            }

            flash('schedule_message', "Importación manual completada: $added inscritos, $skipped omitidos.");
            redirect('schedules/index');
        } else {
            $data = ['id_grupo' => $id];
            $this->view('schedules/import', $data);
        }
    }

    public function visu($id)
    {
        $schedule = $this->scheduleModel->getScheduleById($id);
        if ($schedule) {
            $this->view('schedules/visu', $schedule);
        } else {
            redirect('schedules/index');
        }
    }

    public function down($id)
    {
        $schedule = $this->scheduleModel->getScheduleById($id);
        if ($schedule) {
            $this->generatePDF($schedule);
        } else {
            flash('error', 'Schedule not found', 'alert alert-danger');
            redirect('schedules/index');
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
            'Turno' => $schedule['turno'],
            'Tutor' => $schedule['tutor'],
            'Grupo' => $schedule['grupo'],
            'Especialidad' => $schedule['especialidad'],
            'Nivel' => $schedule['nivel'],
            'Salon' => $schedule['salon'],
        ];

        foreach ($details as $field => $value) {
            $pdf->Cell(40, 10, $field, 1, 0, 'L');
            $pdf->Cell(60, 10, $value, 1, 0, 'L');
            $pdf->Ln();
        }

        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(30, 10, 'Hora', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Lunes', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Martes', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Miércoles', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Jueves', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Viernes', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 12);
        foreach ($schedule['horas'] as $hora) {
            $pdf->Cell(30, 10, $hora['hora_inicio'] . ' - ' . $hora['hora_fin'], 1, 0, 'C');
            $pdf->Cell(30, 10, $hora['lunes'], 1, 0, 'C');
            $pdf->Cell(30, 10, $hora['martes'], 1, 0, 'C');
            $pdf->Cell(30, 10, $hora['miercoles'], 1, 0, 'C');
            $pdf->Cell(30, 10, $hora['jueves'], 1, 0, 'C');
            $pdf->Cell(30, 10, $hora['viernes'], 1, 1, 'C');
        }

        $pdf->Output('D', 'horario.pdf');
        exit();
    }

    private function processScheduleData($formData)
    {
        $scheduleData = [
            'periodo_escolar' => $formData['periodo_escolar'],
            'turno' => $formData['turno'],
            'tutor' => $formData['tutor'],
            'grupo' => $formData['grupo'],
            'especialidad' => $formData['especialidad'],
            'nivel' => $formData['nivel'],
            'salon' => $formData['salon'],
            'horas' => [],
        ];

        foreach ($formData['hora_inicio'] as $key => $hora_inicio) {
            $scheduleData['horas'][] = [
                'hora_inicio' => $hora_inicio,
                'hora_fin' => $formData['hora_fin'][$key],
                'lunes' => $formData['lunes'][$key],
                'martes' => $formData['martes'][$key],
                'miercoles' => $formData['miercoles'][$key],
                'jueves' => $formData['jueves'][$key],
                'viernes' => $formData['viernes'][$key],
            ];
        }

        return $scheduleData;
    }

    private function getScheduleHours()
    {
        return [
            "07:00-08:00",
            "08:00-09:00",
            "09:00-10:00",
            "10:00-11:00",
            "11:00-12:00",
            "12:00-13:00",
            "13:00-14:00"
        ];
    }
}