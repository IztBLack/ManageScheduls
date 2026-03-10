<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
/* Estilos extra para la Landing Page */
.hero-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 4rem 2rem;
    border-radius: 0.5rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.05);
    margin-bottom: 3rem;
    text-align: center;
}
.hero-title {
    font-weight: 700;
    color: #343a40;
    margin-bottom: 1rem;
}
.hero-subtitle {
    font-size: 1.25rem;
    color: #6c757d;
    margin-bottom: 2rem;
}
.feature-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}
.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}
.feature-icon {
    font-size: 2.5rem;
    color: #007bff;
    margin-bottom: 1rem;
}
</style>

<div class="container mt-4 mb-5">
  <?php if (isset($_SESSION['is_logged_in'])) : ?>
    
    <!-- VISTA PARA USUARIOS LOGUEADOS (Mini Dashboard) -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-dark text-white shadow-sm">
                <div class="card-body py-4">
                    <h2 class="font-weight-bold mb-1"><i class="fas fa-user-circle mr-2 text-warning"></i> <?php echo $data['title']; ?></h2>
                    <p class="mb-0 text-light"><?php echo $data['description']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php if($_SESSION['user_role'] == 'maestro'): ?>
            <!-- Accesos Rápidos para Maestro -->
            <div class="col-md-6 mb-4">
                <div class="card feature-card text-center">
                    <div class="card-body py-5">
                        <i class="fas fa-users mb-3" style="font-size: 3rem; color: #28a745;"></i>
                        <h4 class="card-title font-weight-bold">Mis Grupos</h4>
                        <p class="card-text text-muted">Accede a tus grupos, pasa lista y califica a tus alumnos.</p>
                        <a href="<?php echo URLROOT; ?>/schedules/index" class="btn btn-success btn-lg mt-2 shadow-sm">
                            <i class="fas fa-arrow-right mr-1"></i> Ir a Mis Grupos
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card feature-card text-center">
                    <div class="card-body py-5">
                        <i class="fas fa-folder-plus mb-3" style="font-size: 3rem; color: #007bff;"></i>
                        <h4 class="card-title font-weight-bold">Nuevo Grupo</h4>
                        <p class="card-text text-muted">Abre rápidamente un nuevo espacio de trabajo para este ciclo.</p>
                        <a href="<?php echo URLROOT; ?>/schedules/add" class="btn btn-primary btn-lg mt-2 shadow-sm">
                            <i class="fas fa-plus mr-1"></i> Crear Grupo
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Accesos Rápidos para Alumno -->
            <div class="col-md-6 mb-4 mx-auto">
                <div class="card feature-card text-center">
                    <div class="card-body py-5">
                        <i class="fas fa-book-reader mb-3" style="font-size: 3rem; color: #17a2b8;"></i>
                        <h4 class="card-title font-weight-bold">Mis Clases</h4>
                        <p class="card-text text-muted">Revisa las materias en las que estás inscrito y tus evaluaciones.</p>
                        <a href="<?php echo URLROOT; ?>/students/index" class="btn btn-info btn-lg mt-2 shadow-sm">
                            <i class="fas fa-sign-in-alt mr-1"></i> Ver Mis Clases
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

  <?php else : ?>
    
    <!-- VISTA PARA INVITADOS (Landing Page) -->
    <div class="hero-section">
        <i class="fas fa-school mb-3" style="font-size: 4rem; color: #343a40;"></i>
        <h1 class="hero-title display-4"><?php echo $data['title']; ?></h1>
        <p class="hero-subtitle lead mx-auto" style="max-width: 700px;"><?php echo $data['description']; ?></p>
        <div class="mt-4">
            <a href="<?php echo URLROOT; ?>/users/login" class="btn btn-primary btn-lg px-4 mr-2 shadow-sm">
                <i class="fas fa-sign-in-alt mr-1"></i> Iniciar Sesión
            </a>
            <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-outline-dark btn-lg px-4 shadow-sm">
                <i class="fas fa-user-plus mr-1"></i> Crear Cuenta
            </a>
        </div>
    </div>

    <!-- Features / Características -->
    <div class="row text-center mt-5 mb-4">
        <div class="col-12 mb-4">
            <h2 class="font-weight-bold" style="color:#343a40;">Todo lo que necesitas en un solo lugar</h2>
            <hr style="width: 50px; border-top: 3px solid #007bff;">
        </div>
    </div>

    <div class="row text-center">
        <div class="col-md-4 mb-4">
            <div class="card feature-card">
                <div class="card-body p-4">
                    <i class="fas fa-users-cog feature-icon"></i>
                    <h5 class="font-weight-bold">Gestión de Grupos</h5>
                    <p class="text-muted small">Crea y administra múltiples grupos, asigna materias y maneja la información completa de tus ciclos escolares de forma organizada.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card feature-card">
                <div class="card-body p-4">
                    <i class="fas fa-clipboard-list feature-icon text-success"></i>
                    <h5 class="font-weight-bold">Pase de Lista</h5>
                    <p class="text-muted small">Registra asistencias y faltas al vuelo. Mantén un seguimiento histórico completo por cada alumno y fecha.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card feature-card">
                <div class="card-body p-4">
                    <i class="fas fa-chart-line feature-icon text-warning"></i>
                    <h5 class="font-weight-bold">Esquemas de Evaluación</h5>
                    <p class="text-muted small">Libertad total. Define tus propias unidades, actividades, ponderaciones y puntos de bono para emitir calificaciones automatizadas.</p>
                </div>
            </div>
        </div>
    </div>
    
  <?php endif; ?>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>