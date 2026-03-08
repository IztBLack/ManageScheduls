<?php require APPROOT . '/views/inc/header.php'; ?>
<style>
    /* VARIABLES Y ESTILOS BASE COMPARTIDOS (igual que maestros) */
    :root {
        --primary-gradient: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        --accent-color: #ffd700;
        --accent-hover: #ffed4a;
        --card-bg: #ffffff;
        --text-main: #334155;
        --text-muted: #64748b;
        --border-radius: 12px;
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* CARD ESTILO PREMIUM */
    .config-card {
        background: var(--card-bg);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        overflow: hidden;
    }

    .config-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .config-header {
        background: var(--primary-gradient) !important;
        border-bottom: 3px solid var(--accent-color);
        padding: 1.5rem;
    }

    .config-header h5 {
        color: white;
        font-weight: 600;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .detail-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        color: var(--text-main);
    }

    .detail-icon {
        width: 24px;
        color: var(--text-muted);
        text-align: center;
        margin-right: 0.5rem;
    }
</style>

<div class="container mt-4 mb-5">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-graduation-cap text-primary mr-2"></i> Mis Calificaciones
            </h2>
        </div>
    </div>
    
    <div class="row">
        <?php foreach($data['grades'] as $grupo) : ?>
            <div class="col-md-4 mb-4">
                <div class="card config-card h-100">
                    <div class="card-header config-header">
                        <h5 class="text-truncate" title="<?php echo htmlspecialchars($grupo->subject_name); ?>">
                            <i class="fas fa-book mr-2"></i><?php echo htmlspecialchars($grupo->subject_name); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-item">
                            <i class="fas fa-users detail-icon"></i>
                            <span><strong>Grupo:</strong> <?php echo htmlspecialchars($grupo->grupo); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-chalkboard-teacher detail-icon"></i>
                            <span><strong>Docente:</strong> <?php echo htmlspecialchars($grupo->teacher_name); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-calendar-alt detail-icon"></i>
                            <span><strong>Periodo:</strong> <?php echo htmlspecialchars($grupo->periodo); ?></span>
                        </div>
                        <div class="detail-item mb-0">
                            <i class="fas fa-clock detail-icon"></i>
                            <span><strong>Turno:</strong> <?php echo htmlspecialchars($grupo->turno); ?></span>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top-0 p-3">
                        <a href="<?php echo URLROOT; ?>/students/report/<?php echo $grupo->id; ?>" class="btn btn-outline-dark btn-block font-weight-bold rounded-pill">
                            <i class="fas fa-file-alt mr-2"></i> Ver Reporte Detallado
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if(empty($data['grades'])) : ?>
            <div class="col-12">
                <div class="alert alert-info rounded-pill shadow-sm border-0">
                    <i class="fas fa-info-circle mr-2"></i> No estás inscrito en ningún grupo actualmente.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>