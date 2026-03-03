<?php

class Subjects extends Controller
{
    private $subjectModel;
    private $teacherModel;

    public function __construct()
    {
        $this->subjectModel = $this->model("Subject");
        $this->teacherModel = $this->model("Teacher");
    }

    public function index()
    {
        $subjects = $this->subjectModel->getAllSubjects();
        $data = [
            'subjects' => $subjects
        ];

        $this->view("subjects/index", $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $subject = $_POST['subject'];
            $teachers = isset($_POST['teachers']) ? $_POST['teachers'] : [];
            if ($this->subjectModel->addSubject($name, $subject, $teachers)) {
                flash('register_success', 'Asignatura agregada');
                header('Location: ' . URLROOT . '/subjects');
                exit();
            } else {
                flash('error', 'Error al agregar la asignatura');
            }
        } else {
            // Obtener la lista de profesores para mostrar en el formulario
            $teachers = $this->teacherModel->getAllTeachers();
            $this->view('subjects/add', ['teachers' => $teachers]);
        }
    }


    public function edit($id)
    {
        $subject = $this->subjectModel->getSubjectById($id);
        $allTeachers = [];
        $selectedTeachers = [];
    
        if (!$subject) {
            header('Location: ' . URLROOT . '/subjects');
            exit();
        }
                // Obtener la lista de todos los profesores
            $allTeachers = $this->teacherModel->getAllTeachers();
    
            // Obtener la lista de profesores asignados a la asignatura actual
            $assignedTeachers = $this->subjectModel->getAssignedTeachers($id);
    
            // Extraer los IDs de los profesores asignados
            $selectedTeachers = array_map(function ($teacher) {
                return $teacher->teacher_id;
            }, $assignedTeachers);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar y limpiar datos POST según sea necesario
            $name = $_POST['name'] ?? '';
            $subjectName = $_POST['subject'] ?? '';
            $teachers = isset($_POST['teachers']) ? $_POST['teachers'] : [];
    

            // Intentar editar la asignatura
            if ($this->subjectModel->editSubject($id, $name, $subjectName, $teachers)) {
                // Flash y redirección desde el controlador
                flash('edit_success', 'Materia actualizada');
                header('Location: ' . URLROOT . '/subjects/details/' . $id); // Redirigir a los detalles de la asignatura
                exit();
            } else {
                // En caso de fallo, preparar los datos para volver al formulario de edición
                $data = [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'subject' => $subject->subject_name,
                    'teachers' => $allTeachers,
                    'selectedTeachers' => $selectedTeachers,
                ];
                $this->view('subjects/edit', $data);
            }
        } else {
            // Si no es una solicitud POST, simplemente mostrar el formulario de edición
            $data = [
                'id' => $subject->id,
                'name' => $subject->name,
                'subject' => $subject->subject_name,
                'teachers' => $allTeachers,
                'selectedTeachers' => $selectedTeachers,
            ];
            $this->view('subjects/edit', $data);
        }
    }
    



    public function delete($id)
    {
        if ($this->subjectModel->deleteSubject($id)) {
            header('Location: ' . URLROOT . '/subjects');
            exit();
        } else {
            flash('error', 'Error al eliminar la asignatura');
            header('Location: ' . URLROOT . '/subjects');
            exit();
        }
    }

    public function filter()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $filter =  $_POST['filter'];
            $subjects = $this->subjectModel->filterSubjects($filter);
            $data = [
                'subjects' => $subjects
            ];
            $this->view('subjects/filter', $data);
        } else {
            // Show the filter form
        }
    }
}
