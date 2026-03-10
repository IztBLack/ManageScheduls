<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="row mt-5 mb-5">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card config-card">
            <div class="card-header config-header text-white text-center py-4">
                <h3 class="font-weight-light my-2">
                    <i class="fas fa-id-badge text-warning mr-2" style="font-size: 2rem;"></i> Mi Perfil
                </h3>
                <p class="text-light opacity-75 text-wrap mb-0 mt-2 px-3">
                    Verifica y actualiza los datos básicos de tu cuenta.
                </p>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="<?php echo URLROOT; ?>/users/profile" method="post">
                    
                    <div class="form-group mb-4">
                        <label for="name" class="font-weight-bold text-muted small text-uppercase">Nombre Completo</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span></div>
                            <input type="text" 
                                   name="name" 
                                   class="form-control focus-warning <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($data['user']->name); ?>"
                                   placeholder="Ingresa tu nombre">
                            <span class="invalid-feedback ml-2"><?php echo $data['name_err'] ?? ''; ?></span>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="email" class="font-weight-bold text-muted small text-uppercase">Correo Electrónico</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span></div>
                            <input type="email" 
                                   name="email" 
                                   class="form-control focus-warning <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($data['user']->email); ?>"
                                   placeholder="Ej: tu@correo.com">
                            <span class="invalid-feedback ml-2"><?php echo $data['email_err'] ?? ''; ?></span>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small text-uppercase">Rol Actual</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text bg-light"><i class="fas fa-user-tag text-muted"></i></span></div>
                            <input type="text" 
                                   class="form-control bg-light text-capitalize" 
                                   value="<?php echo htmlspecialchars($data['user']->rol); ?>"
                                   readonly disabled>
                        </div>
                        <small class="form-text text-muted mt-2"><i class="fas fa-info-circle mr-1"></i> Tu rol en el sistema no puede ser modificado manualmente.</small>
                    </div>

                    <div class="row mt-5 mb-0">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <a href="<?php echo URLROOT; ?>/pages/index" class="btn btn-light btn-block text-secondary border shadow-sm">
                                <i class="fas fa-undo mr-1"></i> Cancelar
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-warning btn-block shadow-sm font-weight-bold">
                                <i class="fas fa-save mr-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
