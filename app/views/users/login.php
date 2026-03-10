<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="row mt-5 mb-5">
    <div class="col-md-6 col-lg-5 mx-auto">
      <div class="card auth-card">
        <div class="auth-header border-login">
            <i class="fas fa-lock auth-icon"></i>
            <h2 class="font-weight-bold mb-0">Bienvenido de Vuelta</h2>
            <p class="text-light opacity-50 mb-0 mt-2">Ingresa tus credenciales para continuar</p>
        </div>
        <div class="card-body p-4 p-md-5">
            <form action="<?php echo URLROOT; ?>/users/login" method="post">
              <div class="form-group mb-4">
                  <label class="text-muted font-weight-bold"><i class="fas fa-envelope mr-1"></i> Correo Electrónico:</label>
                  <input type="email" name="email" class="form-control form-control-custom <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>" placeholder="ejemplo@correo.com">
                  <span class="invalid-feedback ml-2"><?php echo $data['email_err']; ?></span>
              </div>    
              <div class="form-group mb-4">
                  <label class="text-muted font-weight-bold"><i class="fas fa-key mr-1"></i> Contraseña:</label>
                  <input type="password" name="password" class="form-control form-control-custom <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>" placeholder="Introduce tu contraseña">
                  <span class="invalid-feedback ml-2"><?php echo $data['password_err']; ?></span>
              </div>
              <div class="form-group mt-5">
                  <button type="submit" class="btn btn-primary btn-block btn-custom mb-3 shadow-sm">
                      <i class="fas fa-sign-in-alt mr-1"></i> Iniciar Sesión
                  </button>
                  <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-light btn-block btn-custom text-secondary">
                      ¿No tienes cuenta? <strong class="text-dark">Regístrate</strong>
                  </a>
              </div>
            </form>
            
            <?php if(isset($_SESSION['success_message'])) : ?>
              <div class="alert alert-success mt-4 mb-0 rounded-pill text-center shadow-sm">
                <i class="fas fa-check-circle mr-1"></i> <?php echo $_SESSION['success_message']; ?>
              </div>
            <?php endif; ?>
        </div>
      </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
