<?php

class Teachers extends Controller
{
    private $teachersModel;

    public function __construct()
    {
        // Require login
        if (!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }
        $this->teachersModel = $this->model("Teacher");
    }

    public function complete_profile()
    {
        // Solo para maestros
        if ($_SESSION['user_role'] != 'maestro') {
            redirect('pages/index');
        }

        // Si ya tiene perfil, redirigirlo
        if ($this->teachersModel->getTeacherByUserId($_SESSION['user_id'])) {
            redirect('schedules/index');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // sanitize
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name'      => htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'lastName1' => htmlspecialchars(trim($_POST['lastName1'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'lastName2' => htmlspecialchars(trim($_POST['lastName2'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'curp'      => htmlspecialchars(trim($_POST['curp'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'rfc'       => htmlspecialchars(trim($_POST['rfc'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'clave'     => htmlspecialchars(trim($_POST['clave'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'error'     => ''
            ];

            if (empty($data['name']) || empty($data['lastName1'])) {
                $data['error'] = 'El nombre y el primer apellido son obligatorios.';
            }

            // Comprobar Duplicados
            if (empty($data['error'])) {
                $duplicados = $this->teachersModel->checkDuplicates($data['curp'], $data['rfc'], $data['clave']);
                if (!empty($duplicados)) {
                    $camposDuplicados = implode(', ', $duplicados);
                    $data['error'] = 'Los siguientes datos ya están registrados por otro docente: ' . $camposDuplicados;
                }
            }

            if (empty($data['error'])) {
                if ($this->teachersModel->addTeacher(
                    $data['name'], 
                    $data['lastName1'], 
                    $data['lastName2'], 
                    empty($data['curp']) ? null : strtoupper($data['curp']),
                    empty($data['rfc']) ? null : strtoupper($data['rfc']),
                    empty($data['clave']) ? null : strtoupper($data['clave']),
                    $_SESSION['user_id']
                )) {
                    flash('register_success', 'Perfil completado exitosamente.');
                    redirect('schedules/index');
                } else {
                    $data['error'] = 'Error al guardar el perfil.';
                }
            }
            
            $this->view('teachers/complete_profile', $data);

        } else {
            $data = [
                'name'      => $_SESSION['user_name'] ?? '',
                'lastName1' => '',
                'lastName2' => '',
                'curp'      => '',
                'rfc'       => '',
                'clave'     => '',
                'error'     => ''
            ];
            $this->view('teachers/complete_profile', $data);
        }
    }
}
?>