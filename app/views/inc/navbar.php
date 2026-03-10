<style>
.navbar-custom {
    background: linear-gradient(135deg, #343a40 0%, #1a1d20 100%);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 1rem 0;
}
.navbar-brand-custom {
    font-weight: 800;
    font-size: 1.5rem;
    letter-spacing: 1px;
    color: #fff !important;
}
.navbar-brand-custom i {
    color: #ffc107;
    margin-right: 8px;
    transition: transform 0.3s ease;
}
.navbar-brand-custom:hover i {
    transform: rotate(15deg) scale(1.1);
}
.nav-link-custom {
    color: rgba(255,255,255,0.85) !important;
    font-weight: 500;
    margin-left: 0.5rem;
    padding: 0.5rem 1rem !important;
    border-radius: 2rem;
    transition: all 0.3s ease;
}
.nav-link-custom:hover {
    color: #fff !important;
    background-color: rgba(255,255,255,0.1);
    transform: translateY(-2px);
}
.nav-btn-register {
    background-color: #28a745;
    color: white !important;
    box-shadow: 0 2px 4px rgba(40,167,69,0.3);
}
.nav-btn-register:hover {
    background-color: #218838;
    box-shadow: 0 4px 8px rgba(40,167,69,0.4);
}
.nav-btn-logout {
    background-color: #dc3545;
    color: white !important;
}
.nav-btn-logout:hover {
    background-color: #c82333;
}
</style>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
  <div class="container">
    <a class="navbar-brand navbar-brand-custom" href="<?php echo URLROOT; ?>">
        <i class="fas fa-graduation-cap"></i> <?php echo SITENAME; ?>
    </a>
    <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      
      <ul class="navbar-nav ml-auto align-items-center mt-3 mt-lg-0">
        <?php if (isset($_SESSION['user_id'])) : ?>
            <!-- Menú de Usuario Logueado (Dropdown) -->
            <li class="nav-item dropdown mb-2 mb-lg-0 mr-lg-2">
                <a class="nav-link nav-link-custom dropdown-toggle text-light opacity-75 d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle mr-2" style="font-size: 1.2rem;"></i>
                    <span>Hola, <strong><?php echo $_SESSION['user_name']; ?></strong></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow border-0 mt-2" aria-labelledby="userDropdown" style="border-radius: 8px;">
                    <a class="dropdown-item py-2" href="<?php echo URLROOT; ?>/users/profile">
                        <i class="fas fa-id-badge text-info mr-2"></i>Mi Perfil
                    </a>
                    <a class="dropdown-item py-2" href="<?php echo URLROOT; ?>/users/change_password">
                        <i class="fas fa-key text-secondary mr-2"></i>Cambiar Contraseña
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item py-2 text-danger" href="<?php echo URLROOT; ?>/users/logout">
                        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                    </a>
                </div>
            </li>
        <?php else : ?>
            <li class="nav-item mb-2 mb-lg-0 w-100 text-center text-lg-left">
                <a class="nav-link nav-link-custom" href="<?php echo URLROOT; ?>/users/login">
                    <i class="fas fa-sign-in-alt mr-1"></i> Iniciar Sesión
                </a>
            </li>
            <li class="nav-item w-100 text-center text-lg-left">
                <a class="nav-link nav-link-custom nav-btn-register" href="<?php echo URLROOT; ?>/users/register">
                    <i class="fas fa-user-plus mr-1"></i> Registrarse
                </a>
            </li>
        <?php endif; ?>
      </ul>
      
    </div>
  </div>
</nav>