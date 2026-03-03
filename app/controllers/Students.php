<?php
class Students extends Controller {

    private $studentModel;
    private $scheduleModel;

    public function __construct(){
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