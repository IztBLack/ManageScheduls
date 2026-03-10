<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="row mt-5 mb-5">
  <div class="col-md-8 col-lg-6 mx-auto">
    <div class="card auth-card">
      
      <div class="auth-header bg-profile">
          <i class="fas fa-id-card auth-icon"></i>
          <h2 class="font-weight-bold mb-0">Completar Perfil</h2>
          <p class="text-light opacity-75 mb-0 mt-2">Danos tus datos básicos para acceder a tus grupos</p>
      </div>

      <div class="card-body p-4 p-md-5">
          <?php if(!empty($data['error'])): ?>
            <div class="alert alert-danger" style="border-radius: 1rem;">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $data['error']; ?>
            </div>
          <?php endif; ?>

          <form action="<?php echo URLROOT; ?>/teachers/complete_profile" method="post">
            
            <div class="form-group mb-4">
                <label class="text-muted font-weight-bold"><i class="fas fa-user mr-1"></i> Nombre(s) Completo:</label>
                <input type="text" name="name" class="form-control form-control-custom focus-info" value="<?php echo htmlspecialchars($data['name']); ?>" placeholder="ej: Juan Carlos" required>
            </div> 

            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="text-muted font-weight-bold"><i class="fas fa-font mr-1"></i> Apellido Paterno:</label>
                    <input type="text" name="lastName1" class="form-control form-control-custom focus-info" value="<?php echo htmlspecialchars($data['lastName1']); ?>" required>
                </div>
                <div class="col-md-6 form-group mb-4">
                    <label class="text-muted font-weight-bold"><i class="fas fa-font mr-1"></i> Apellido Materno:</label>
                    <input type="text" name="lastName2" class="form-control form-control-custom focus-info" value="<?php echo htmlspecialchars($data['lastName2']); ?>" placeholder="(Opcional)">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="text-muted font-weight-bold"><i class="fas fa-id-badge mr-1"></i> CURP:</label>
                    <input type="text" name="curp" class="form-control form-control-custom focus-info text-uppercase" value="<?php echo htmlspecialchars($data['curp'] ?? ''); ?>" maxlength="18">
                </div>
                <div class="col-md-6 form-group mb-4">
                    <label class="text-muted font-weight-bold"><i class="fas fa-file-invoice mr-1"></i> RFC:</label>
                    <input type="text" name="rfc" class="form-control form-control-custom focus-info text-uppercase" value="<?php echo htmlspecialchars($data['rfc'] ?? ''); ?>" maxlength="13">
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="text-muted font-weight-bold"><i class="fas fa-key mr-1"></i> Clave de Empleado (Institucional):</label>
                <input type="text" name="clave" class="form-control form-control-custom focus-info" value="<?php echo htmlspecialchars($data['clave'] ?? ''); ?>">
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-info btn-block btn-custom py-3 shadow-sm">
                    <i class="fas fa-save mr-1"></i> Guardar Perfil y Acceder al Panel
                </button>
            </div>

          </form>
      </div>
      
    </div>
  </div>
</div>
  
<?php require APPROOT . '/views/inc/footer.php'; ?>
