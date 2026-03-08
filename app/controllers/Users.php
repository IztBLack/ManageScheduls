  <?php


  class Users extends Controller
  {
    private $userModel;


    public function __construct()
    {
      $this->userModel = $this->model('User');
    }

    public function index()
    {
      redirect('welcome');
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
          $password_err = 'Please enter a password.';
        } elseif (strlen($data['password']) < 6) {
          $data['password_err'] = 'Password must have atleast 6 characters.';
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
        redirect('.URLROOT.');
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

        if ($_SESSION['must_change_password']) {
            redirect('users/change_password');
            return;
        }

        // Redirección basada en el rol
        if($user->rol == 'maestro'){
            redirect('schedules/index');
        } else {
            redirect('students/index');
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
      // Check Logged In
      if ($this->isLoggedIn()) {
        // Obtain the data of the session user (name, email and password)
        $user = $this->userModel->getUser();
        // Load the profile view and pass the user's data
        $this->view('profile/index', ['user' => $user]);
      }
    }

    public function change_password() {
        if (!$this->isLoggedIn()) {
            redirect('users/login');
        }

        // Si no necesita cambiar, lo mandamos a su inicio normal
        if (empty($_SESSION['must_change_password'])) {
            if($_SESSION['user_role'] == 'maestro'){
                redirect('schedules/index');
            } else {
                redirect('students/index');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            if (empty($data['password'])) {
                $data['password_err'] = 'Por favor ingresa una contraseña nueva.';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'La contraseña debe tener al menos 6 caracteres.';
            }

            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Por favor confirma la contraseña.';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Las contraseñas no coinciden.';
                }
            }

            // Validar que no sea la misma que la por defecto (matricula/email) - Opcional, pero recomendado
            $currentUser = $this->userModel->getUserById($_SESSION['user_id']);
            if (password_verify($data['password'], $currentUser->password)) {
                $data['password_err'] = 'Debes usar una contraseña diferente a la actual.';
            }

            if (empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Actualizar password
                if ($this->userModel->updatePassword($_SESSION['user_id'], $data['password'])) {
                    // Limpiar flag
                    $_SESSION['must_change_password'] = false;
                    flash('register_success', 'Contraseña actualizada correctamente. Bienvenido.');
                    
                    if($_SESSION['user_role'] == 'maestro'){
                        redirect('schedules/index');
                    } else {
                        redirect('students/index');
                    }
                } else {
                    die('Algo salió mal al actualizar la contraseña');
                }
            } else {
                $this->view('users/change_password', $data);
            }
        } else {
            $data = [
                'password' => '',
                'confirm_password' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];
            $this->view('users/change_password', $data);
        }
    }

    
  }
  ?>