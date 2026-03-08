<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="row mt-5">
    <div class="col-md-6 mx-auto">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-warning text-dark text-center py-4">
                <h3 class="font-weight-light my-2">Cambio de Contraseña Requerido</h3>
            </div>
            <div class="card-body p-5">
                <p class="text-center text-muted mb-4">
                    Por motivos de seguridad, debes cambiar tu contraseña predeterminada antes de continuar.
                </p>
                <form action="<?php echo URLROOT; ?>/users/change_password" method="post">
                    
                    <div class="form-group mb-4">
                        <label for="password" class="font-weight-bold">Nueva Contraseña: <sup>*</sup></label>
                        <input type="password" 
                               name="password" 
                               class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo $data['password']; ?>">
                        <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                    </div>

                    <div class="form-group mb-4">
                        <label for="confirm_password" class="font-weight-bold">Confirmar Nueva Contraseña: <sup>*</sup></label>
                        <input type="password" 
                               name="confirm_password" 
                               class="form-control form-control-lg <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo $data['confirm_password']; ?>">
                        <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
                    </div>

                    <div class="row mt-4 mb-0">
                        <div class="col">
                            <input type="submit" value="Guardar y Continuar" class="btn btn-primary btn-block btn-lg shadow-sm">
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <div class="small">
                    <a href="<?php echo URLROOT; ?>/users/logout" class="text-danger">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
