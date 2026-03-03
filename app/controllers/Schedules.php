<?php

require_once '../FPDF/fpdf.php'; // Import the FPDF class

class Schedules extends Controller
{
    private $scheduleModel;
    private $studentModel;

    public function __construct()
    {
        $this->scheduleModel = $this->model('Schedule');
        $this->studentModel = $this->model('Student');
        $this->userModel = $this->model('User');
    }

    public function index(){
        $schedules = $this->scheduleModel->getSchedulesWithNames(); 

        $data = [
            'schedules' => $schedules
        ];

        $this->view('schedules/index', $data);
    }

    

 public function add() {
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // --- DEBUGGING: volcar POST antes de sanitizar
        error_log('Schedules::add POST raw: '.print_r($_POST, true));

        // Sanitizar sólo campos escalares; no queremos que se pierda el arreglo units
        $clean = [];
        $keysToClean = ['subject_id','grupo','turno','salon','periodo','especialidad'];
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
            'units'        => $_POST['units'] ?? [] // conserva el array
        ];

        // debug data estructurado
        error_log('Schedules::add $data: '.print_r($data, true));

        // VALIDACIÓN: el arreglo units no puede estar vacío
        if (empty($data['units'])) {
            flash('schedule_message', 'Debe agregar al menos una unidad con actividades', 'alert alert-danger');
            // recargar materias
            $data['subjects'] = $this->scheduleModel->getSubjects();
            $data['debug'] = true;
            $this->view('schedules/add', $data);
            return;
        }

        // Verificar pesos por unidad: bloquear solo si 0 o >100, permitir guardado con advertencia si <>100
        $hasWarning = false;
        foreach($data['units'] as $index => $unit){
            $totalUnitWeight = 0;
            foreach($unit['activities'] as $activity){
                $totalUnitWeight += floatval($activity['weight']);
            }

            if($totalUnitWeight == 0){
                flash('schedule_message', "Error en Unidad $index: Debe haber al menos una actividad con peso mayor a 0.", 'alert alert-danger');
                $data['subjects'] = $this->scheduleModel->getSubjects();
                $this->view('schedules/add', $data);
                return;
            }

            if($totalUnitWeight > 100){
                flash('schedule_message', "Error en Unidad $index: La suma de pesos no puede superar 100% (Actual: $totalUnitWeight%)", 'alert alert-danger');
                $data['subjects'] = $this->scheduleModel->getSubjects();
                $this->view('schedules/add', $data);
                return;
            }

            if($totalUnitWeight != 100){
                $hasWarning = true;
            }
        }

        if($hasWarning){
            flash('schedule_message', 'Advertencia: una o más unidades no suman 100%. Se guardará el registro, revisa los porcentajes.', 'alert alert-warning');
        }

        // Ejecutar inserción en el modelo
        if($this->scheduleModel->addFullStructure($data)){
            flash('schedule_message', '¡Grupo y Plan de Evaluación registrados con éxito!');
            redirect('schedules/index');
        } else {
            // fallo en modelo, mostrar formulario con debug y mensaje
            flash('schedule_message', 'Error al guardar en la base de datos', 'alert alert-danger');
            $data['subjects'] = $this->scheduleModel->getSubjects();
            $data['debug'] = true;
            $this->view('schedules/add', $data);
            return;
        }

    } else {
        // Cargar materias para el select del formulario
        $subjects = $this->scheduleModel->getSubjects();
        $data = ['subjects' => $subjects, 'debug' => false];
        $this->view('schedules/add', $data);
    }
}

    private function getScheduleHours()
    {
        return ["07:00-08:00",
            "08:00-09:00",
            "09:00-10:00",
            "10:00-11:00",
            "11:00-12:00",
            "12:00-13:00",
            "13:00-14:00"];
    }

    public function edit($id)
    {
        // Load schedule data for the specified ID
        $schedule = $this->scheduleModel->getScheduleById($id);
        if ($schedule) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Process the form data and update the schedule
                $scheduleData = $this->processScheduleData($_POST);
                $success = $this->scheduleModel->editSchedule($id, $scheduleData);
                if ($success) {
                    // Redirect to the success view
                    flash('schedule_message', 'Schedule Updated');
                    redirect('schedules/index');
                } else {
                    flash('error', 'Failed to update schedule', 'alert alert-danger');
                    redirect('schedules/edit/' . $id);
                }
            } else {
                // Display the edit view with schedule data
                // prepare unidades with actividades so the view can iterate them
                $unidades = $this->scheduleModel->getUnitsWithActivities($id);

                $data = [
                    'schedule' => $schedule,
                    'unidades' => $unidades
                ];

                $this->view('schedules/edit', $data);
            }
        } else {
            flash('error', 'Schedule not found', 'alert alert-danger');
            redirect('schedules/index');
        }
    }

    public function delete($id)
    {
        $success = $this->scheduleModel->deleteSchedule($id);

        if ($success) {
            flash('schedule_message', 'Schedule Removed');
        } else {
            flash('error', 'Failed to remove schedule', 'alert alert-danger');
        }

        redirect('schedules/index');
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

    public function import()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $schedule_id = intval($_POST['id_grupo'] ?? 0);

            if (!isset($_FILES['archivo_alumnos']) || $_FILES['archivo_alumnos']['error'] !== UPLOAD_ERR_OK) {
                flash('schedule_message', 'Error al recibir el archivo.', 'alert alert-danger');
                redirect('schedules/import');
            }

            $tmp = $_FILES['archivo_alumnos']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['archivo_alumnos']['name'], PATHINFO_EXTENSION));

            $added = 0;
            $skipped = 0;

            // Procesar CSV simple
            if ($ext === 'csv') {
                if (($handle = fopen($tmp, 'r')) !== false) {
                    // opcional: leer encabezado y descartarlo si contiene palabras clave
                    $first = fgetcsv($handle, 0, ',');
                    $hasHeader = false;
                    if ($first) {
                        $h0 = strtolower(trim($first[0]));
                        if (strpos($h0, 'mat') !== false || strpos($h0, 'matr') !== false) {
                            $hasHeader = true;
                        } else {
                            // no header, procesar primera fila
                            rewind($handle);
                        }
                    }

                    while (($row = fgetcsv($handle, 0, ',')) !== false) {
                        if (count($row) < 2) continue;
                        $matricula = trim($row[0]);
                        $nombre = trim($row[1]);
                        if (empty($matricula) || empty($nombre)) { $skipped++; continue; }

                        $email = $matricula . '@students.local';
                        $user = $this->userModel->getUserByEmail($email);
                        if (!$user) {
                            // crear alumno con contraseña temporal
                            $tempPass = bin2hex(random_bytes(4));
                            $created = $this->userModel->createStudent($nombre, $email, $tempPass);
                            if (!$created) { $skipped++; continue; }
                            $user = $this->userModel->getUserByEmail($email);
                        }

                        if ($user) {
                            $this->studentModel->registerInGroup($schedule_id, $user->id);
                            $added++;
                        } else {
                            $skipped++;
                        }
                    }
                    fclose($handle);
                }
            } else {
                flash('schedule_message', 'Formato de archivo no soportado. Use CSV.', 'alert alert-danger');
                redirect('schedules/import');
            }

            flash('schedule_message', "Importación completada: $added inscritos, $skipped omitidos.");
            redirect('schedules/index');
        } else {
            // Mostrar vista de importación
            $data = [
                'id_grupo' => intval($_GET['id'] ?? 0)
            ];
            $this->view('schedules/import', $data);
        }
    }

    
    public function grades($id)
    {
        // this action is reached from schedule listing, usually via a GET link
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // processing of grades submission could go here; for now keep simple
            // depending on your design you might loop through POST data and save
            if (isset($_POST['calif'])) {
                foreach ($_POST['calif'] as $inscripcion => $actividades) {
                    foreach ($actividades as $actividadId => $nota) {
                        $this->studentModel->saveGrade($inscripcion, $actividadId, $nota);
                    }
                }
                flash('grade_message', 'Calificaciones registradas');
            }
            redirect('schedules/grades/' . $id);
            return;
        }

        // Prepare data for the view
        $data = [
            'schedule'   => $this->scheduleModel->getScheduleById($id),
            'activities' => $this->scheduleModel->getActivitiesBySchedule($id),
            'students'   => $this->scheduleModel->getStudentsBySchedule($id)
        ];

        $this->view('schedules/grades', $data);
    }

    private function generatePDF($schedule)
    {
        // Create a PDF instance
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Visualizar Horario', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 12);

        // Create a table for schedule details
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
        // Process form data and structure it for ScheduleModel
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
}
