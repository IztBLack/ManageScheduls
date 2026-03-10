<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="row mt-5 mb-5">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card auth-card">
            
            <div class="auth-header">
                <i class="fas fa-key auth-icon text-warning mb-2" style="font-size: 2.5rem;"></i>
                <h2 class="font-weight-bold mb-0">
                    <?php echo $data['isForced'] ? 'Cambio Requerido' : 'Cambiar Contraseña'; ?>
                </h2>
                <?php if ($data['isForced']): ?>
                    <p class="text-light opacity-50 text-wrap mb-0 mt-2 px-3">
                        Por motivos de seguridad, debes cambiar tu contraseña predeterminada antes de continuar.
                    </p>
                <?php else: ?>
                    <p class="text-light opacity-50 text-wrap mb-0 mt-2 px-3">
                        Asegúrate de teclear tu contraseña actual antes de asignar una nueva.
                    </p>
                <?php endif; ?>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="<?php echo URLROOT; ?>/users/change_password" method="post">
                    
                    <?php if (!$data['isForced']): ?>
                        <div class="form-group mb-4">
                            <label for="current_password" class="text-muted font-weight-bold"><i class="fas fa-unlock-alt mr-1"></i> Contraseña Actual:</label>
                            <input type="password" 
                                   name="current_password" 
                                   class="form-control form-control-custom focus-warning <?php echo (!empty($data['current_password_err'])) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo $data['current_password']; ?>"
                                   placeholder="Ingresa tu contraseña actual">
                            <span class="invalid-feedback ml-2"><?php echo $data['current_password_err']; ?></span>
                        </div>
                        <hr class="mb-4">
                    <?php endif; ?>
                    
                    <div class="form-group mb-4">
                        <label for="password" class="text-muted font-weight-bold"><i class="fas fa-lock mr-1"></i> Nueva Contraseña:</label>
                        <input type="password" 
                               name="password" 
                               class="form-control form-control-custom focus-warning <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo $data['password']; ?>"
                               placeholder="Mínimo 8 caracteres alfa-numéricos">
                        <span class="invalid-feedback ml-2"><?php echo $data['password_err']; ?></span>
                    </div>

                    <div class="form-group mb-4">
                        <label for="confirm_password" class="text-muted font-weight-bold"><i class="fas fa-check-double mr-1"></i> Confirmar Nueva Contraseña:</label>
                        <input type="password" 
                               name="confirm_password" 
                               class="form-control form-control-custom focus-warning <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo $data['confirm_password']; ?>"
                               placeholder="Repite la nueva contraseña">
                        <span class="invalid-feedback ml-2"><?php echo $data['confirm_password_err']; ?></span>
                    </div>

                    <div class="form-group mt-5 mb-0">
                        <button type="submit" class="btn btn-warning btn-block btn-custom py-3 shadow-sm font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Guardar y Continuar
                        </button>
                        
                        <?php if ($data['isForced']): ?>
                            <a href="<?php echo URLROOT; ?>/users/logout" class="btn btn-light btn-block btn-custom text-danger font-weight-bold mt-3">
                                <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
                            </a>
                        <?php else: ?>
                            <a href="<?php echo URLROOT; ?>/pages/index" class="btn btn-light btn-block btn-custom text-secondary font-weight-bold mt-3">
                                <i class="fas fa-undo mr-1"></i> Cancelar y Volver
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
