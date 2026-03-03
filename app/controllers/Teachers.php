<?php

class Teachers extends Controller
{

    private $teachersModel;

    public function __construct()
    {
        $this->teachersModel = $this->model("Teacher");
    }

    public function index()
    {
        $teachers = $this->teachersModel->getAllTeachers();
        $data = [
            'teachers' => $teachers
        ];

        $this->view("teachers/index", $data);
    }
    
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $lastName1 = $_POST['lastName1'];
            $lastName2 = $_POST['lastName2'];
            $curp = $_POST['curp'];
            $rfc = $_POST['rfc'];
            $clave = $_POST['clave'];
            if ($this->teachersModel->addTeacher( $name, $lastName1, $lastName2, $curp, $rfc, $clave)) {
                flash('register_success', 'Docente agregado');
                header('Location: ' . URLROOT . '/teachers');
                exit();
            }
        }
        $this->view('teachers/add');
    }

    public function edit($id)
    {
        $teacher = $this->teachersModel->getTeacherById($id);

        if ($teacher) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $name = $_POST['name'];
                $lastName1 = $_POST['lastName1'];
                $lastName2 = $_POST['lastName2'];
                $curp = $_POST['curp'];
                $rfc = $_POST['rfc'];
                $clave = $_POST['clave'];

                if ($this->teachersModel->updateTeacher($id,$name, $lastName1, $lastName2, $curp, $rfc, $clave)) {
                    flash('edit_success', 'Docente actualizado');
                    header('Location: ' . URLROOT . '/teachers');
                    exit();
                }
            } else {
                $data = [
                    'id' => $id,
                    'teacher' => $teacher,
                    'name' => $teacher->name,
                    'lastName1' => $teacher->lastName1,
                    'lastName2' => $teacher->lastName2,
                    'curp' => $teacher->curp,
                    'rfc' => $teacher->rfc,
                    'clave' => $teacher->clave
                ];
                $this->view('teachers/edit', $data);
            }
        }
    }

    public function delete($id)
    {
        if ($this->teachersModel->deleteTeacher($id)) {
            header('Location: ' . URLROOT . '/teachers');
            exit();
        } else {
        }
    }

    public function filter()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $filter = $_POST['filter'];
            $filteredTeachers = $this->teachersModel->filterTeachers($filter);

            $data = [
                'filteredTeachers' => $filteredTeachers
            ];
            $this->view('teachers/filter', $data);
        } else {
        }
    }
}
?>