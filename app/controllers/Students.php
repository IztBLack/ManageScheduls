<?php
class Students extends Controller {

    private $studentModel;
    private $scheduleModel;

    public function __construct(){
        // Protect all methods in this controller
        if (isset($_SESSION['must_change_password']) && $_SESSION['must_change_password'] === true) {
            redirect('users/change_password');
        }

        $this->studentModel = $this->model('Student');
        $this->scheduleModel = $this->model('Schedule');
    }

    // Vista del Alumno: Mis Calificaciones
    public function index(){
        if($_SESSION['user_role'] != 'alumno') redirect('pages/index');
        
        $grades = $this->studentModel->getMyGrades($_SESSION['user_id']);
        $data = ['grades' => $grades];
        $this->view('students/grades', $data);
    }

    // Vista del Alumno: Reporte de Calificaciones
    public function report($schedule_id){
        if($_SESSION['user_role'] != 'alumno') redirect('pages/index');
        
        $inscripcion = $this->studentModel->getInscripcion($_SESSION['user_id'], $schedule_id);
        
        if(!$inscripcion) {
            flash('error', 'No estás inscrito en este grupo');
            redirect('students/index');
            return;
        }

        $schedule = $this->scheduleModel->getScheduleById($schedule_id);
        $unidades = $this->scheduleModel->getUnitsWithActivities($schedule_id);
        
        // Cargar calificaciones y bonus
        $grades = $this->studentModel->getGradesBySchedule($schedule_id);
        $bonus = $this->studentModel->getBonusBySchedule($schedule_id);

        $data = [
            'schedule' => $schedule,
            'unidades' => $unidades,
            'inscripcion_id' => $inscripcion->id,
            'grades' => $grades,
            'bonus' => $bonus
        ];

        $this->view('students/report', $data);
    }

    // Vista del Maestro: Captura de Notas
    public function grades($group_id, $unidad_id){
        if($_SESSION['user_role'] != 'maestro') redirect('pages/index');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Lógica para guardar las notas enviadas desde la matriz
            foreach($_POST['calificaciones'] as $inscripcion_id => $actividades){
                foreach($actividades as $actividad_id => $nota){
                    $this->studentModel->saveGrade($inscripcion_id, $actividad_id, $nota);
                }
            }
            flash('grade_message', 'Calificaciones actualizadas');
            redirect('schedules/index');
        } else {
            // Cargar datos para la matriz de la vista grades.php
            $students = $this->scheduleModel->getStudentsBySchedule($group_id);
            $activities = $this->studentModel->getActivitiesByUnit($unidad_id);
            
            $data = [
                'students' => $students,
                'activities' => $activities
            ];
            $this->view('groups/grades', $data);
        }
    }
}