  <?php


  class Users extends Controller
  {
    private $userModel;


    public function __construct()
    {
      $this->userModel = $this->model('User');
      $this->teacherModel = $this->model('Teacher');
    }

    public function index()
    {
      redirect('users/login');
    }

    public function register()
    {
      // Check if logged in
      if ($this->isLoggedIn()) {
        redirect('posts');
      }

      // Check if POST
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize POST
        $_POST = array_map(function($value) {
            return is_string($value) ? htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8') : $value;
        }, $_POST);

        $data = [
          'name' => trim($_POST['name']),
          'email' => trim($_POST['email']),
          'rol' => trim($_POST['rol'] ?? 'alumno'),
          'password' => trim($_POST['password']),
          'confirm_password' => trim($_POST['confirm_password']),
          'name_err' => '',
          'email_err' => '',
          'password_err' => '',
          'confirm_password_err' => ''
        ];

        // Validate email
        if (empty($data['email'])) {
          $data['email_err'] = 'Please enter an email';
          // Validate name
          if (empty($data['name'])) {
            $data['name_err'] = 'Please enter a name';
          }
        } else {
          // Check Email
          if ($this->userModel->findUserByEmail($data['email'])) {
            $data['email_err'] = 'Email is already taken.';
          }
        }

        // Validate password
        if (empty($data['password'])) {
          $data['password_err'] = 'Please enter a password.';
        } elseif (strlen($data['password']) < 8) {
          $data['password_err'] = 'Password must have atleast 8 characters.';
        } elseif (!preg_match('/[A-Za-z]/', $data['password']) || !preg_match('/[0-9]/', $data['password'])) {
          $data['password_err'] = 'Password must contain at least one letter and one number.';
        }
        // Validate confirm password
        if (empty($data['confirm_password'])) {
          $data['confirm_password_err'] = 'Please confirm password.';
        } else {
          if ($data['password'] != $data['confirm_password']) {
            $data['confirm_password_err'] = 'Password do not match.';
          }
        }

        // Make sure errors are empty
        if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
          // SUCCESS - Proceed to insert

          // Hash Password
          $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
          
          // Por ser registro voluntario, no requerir cambio posterior
          $data['must_change_password'] = 0;

          // //Execute
          if ($this->userModel->register($data)) {
            // Redirect to login
            flash('register_success', 'You are now registered and can log in');
            redirect('users/login');
          } else {
            die('Something went wrong');
          }
        } else {
          // Load View
          $this->view('users/register', $data);
        }
      } else {
        // IF NOT A POST REQUEST

        // Init data
        $data = [
          'name' => '',
          'email' => '',
          'password' => '',
          'confirm_password' => '',
          'name_err' => '',
          'email_err' => '',
          'password_err' => '',
          'confirm_password_err' => ''
        ];

        // Load View
        $this->view('users/register', $data);
      }
    }

    public function login()
    {
      // Check if logged in
      if ($this->isLoggedIn()) {
        redirect('users/login');
      }

      // Check if POST
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize POST
        $_POST = array_map(function($value) {
            return is_string($value) ? htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8') : $value;
        }, $_POST);

        $data = [
          'email' => trim($_POST['email']),
          'password' => trim($_POST['password']),
          'email_err' => '',
          'password_err' => '',
        ];

        // Check for email
        if (empty($data['email'])) {
          $data['email_err'] = 'Please enter email.';
        }

        // Check for name
        if (empty($data['name'])) {
          $data['name_err'] = 'Please enter name.';
        }

        // Check for user
        if ($this->userModel->findUserByEmail($data['email'])) {
          // User Found
        } else {
          // No User
          $data['email_err'] = 'This email is not registered.';
        }

        // Make sure errors are empty
        if (empty($data['email_err']) && empty($data['password_err'])) {

          // Check and set logged in user
          $loggedInUser = $this->userModel->login($data['email'], $data['password']);

          if ($loggedInUser) {
            // User Authenticated!
            $_SESSION['is_logged_in'] = true;
            $this->createUserSession($loggedInUser);
          } else {
            $data['password_err'] = 'Password incorrect.';
            // Load View
            $this->view('users/login', $data);
          }
        } else {
          // Load View
          $this->view('users/login', $data);
        }
      } else {
        // If NOT a POST

        // Init data
        $data = [
          'email' => '',
          'password' => '',
          'email_err' => '',
          'password_err' => '',
        ];

        // Load View
        $this->view('users/login', $data);
      }
    }

         // Dentro de la clase Users
    public function createUserSession($user){
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->rol; // Almacenamos el rol (maestro/alumno)
        $_SESSION['is_logged_in'] = true;
        $_SESSION['must_change_password'] = (bool)$user->must_change_password;

        if ($_SESSION['must_change_password'] && $user->rol != 'maestro') {
            redirect('users/change_password');
            return;
        }

        // Redirección basada en el rol
        if($user->rol == 'maestro'){
            if (!$this->teacherModel->getTeacherByUserId($user->id)) {
                redirect('teachers/complete_profile');
                return;
            }
            redirect('pages/index');
        } else {
            redirect('pages/index');
        }
    }
    

    // Logout & Destroy Session
    public function logout()
    {
      unset($_SESSION['user_id']);
      unset($_SESSION['user_email']);
      unset($_SESSION['user_name']);
      session_destroy();
      redirect('users/login');
    }

    // Check Logged In
    public function isLoggedIn()
    {
      if (isset($_SESSION['user_id'])) {
        return true;
      } else {
        return false;
      }
    }

    public function profile()
    {
        if (!$this->isLoggedIn()) {
            redirect('users/login');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $user = $this->userModel->getUserById($_SESSION['user_id']);
            
            $data = [
                'user' => $user,
                'name_err' => '',
                'email_err' => ''
            ];

            $newName = trim($_POST['name'] ?? '');
            $newEmail = trim($_POST['email'] ?? '');

            if (empty($newName)) {
                $data['name_err'] = 'Por favor ingresa un nombre.';
            }

            if (empty($newEmail)) {
                $data['email_err'] = 'Por favor ingresa un correo electrónico.';
            } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'El formato del correo es inválido.';
            } else {
                $existingUser = $this->userModel->getUserByEmail($newEmail);
                if ($existingUser && $existingUser->id != $_SESSION['user_id']) {
                    $data['email_err'] = 'Este correo ya se encuentra en uso.';
                }
            }

            if (empty($data['name_err']) && empty($data['email_err'])) {
                if ($this->userModel->updateProfile($_SESSION['user_id'], $newName, $newEmail)) {
                    $_SESSION['user_name'] = $newName;
                    $_SESSION['user_email'] = $newEmail;
                    
                    flash('register_success', 'Tus datos de perfil han sido actualizados exitosamente.');
                    redirect('pages/index');
                } else {
                    die('Algo salió mal al actualizar el perfil.');
                }
            } else {
                $data['user']->name = $newName;
                $data['user']->email = $newEmail;
                $this->view('profile/index', $data);
            }
        } else {
            $user = $this->userModel->getUserById($_SESSION['user_id']);
            $this->view('profile/index', [
                'user' => $user,
                'name_err' => '',
                'email_err' => ''
            ]);
        }
    }

    public function change_password() {
        if (!$this->isLoggedIn()) {
            redirect('users/login');
        }

        $isForced = !empty($_SESSION['must_change_password']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'isForced' => $isForced,
                'current_password' => trim($_POST['current_password'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'confirm_password' => trim($_POST['confirm_password'] ?? ''),
                'current_password_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Si es voluntario, exigimos la contraseña actual
            $currentUser = $this->userModel->getUserById($_SESSION['user_id']);
            if (!$isForced) {
                if (empty($data['current_password'])) {
                    $data['current_password_err'] = 'Ingresa tu contraseña actual.';
                } elseif (!password_verify($data['current_password'], $currentUser->password)) {
                    $data['current_password_err'] = 'Contraseña actual incorrecta.';
                }
            }

            // Validar nueva contraseña
            if (empty($data['password'])) {
                $data['password_err'] = 'Por favor ingresa una contraseña nueva.';
            } elseif (strlen($data['password']) < 8) {
                $data['password_err'] = 'La contraseña debe tener al menos 8 caracteres.';
            } elseif (!preg_match('/[A-Za-z]/', $data['password']) || !preg_match('/[0-9]/', $data['password'])) {
                $data['password_err'] = 'La contraseña debe contener al menos una letra y un número.';
            }

            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Por favor confirma la contraseña.';
            } elseif ($data['password'] != $data['confirm_password']) {
                $data['confirm_password_err'] = 'Las contraseñas no coinciden.';
            }

            // Validar que no recicle la misma (sea forzado o no)
            if (empty($data['password_err']) && password_verify($data['password'], $currentUser->password)) {
                $data['password_err'] = 'Debes usar una contraseña diferente a la actual.';
            }

            // Si todo OK procedemos
            if (empty($data['current_password_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                if ($this->userModel->updatePassword($_SESSION['user_id'], $data['password'])) {
                    
                    if ($isForced) {
                        $_SESSION['must_change_password'] = false;
                        flash('register_success', 'Gracias por actualizar tu contraseña. Bienvenido.');
                    } else {
                        flash('register_success', 'Tu contraseña ha sido actualizada exitosamente.');
                    }

                    redirect('pages/index');
                } else {
                    die('Algo salió mal al actualizar la BD.');
                }
            } else {
                $this->view('users/change_password', $data);
            }
        } else {
            // GET Request
            $data = [
                'isForced' => $isForced,
                'current_password' => '',
                'password' => '',
                'confirm_password' => '',
                'current_password_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];
            $this->view('users/change_password', $data);
        }
    }

    
  }
  ?>