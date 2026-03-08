<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="container mt-5">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2>Mis Calificaciones</h2>
        </div>
    </div>
    
    <div class="row">
        <?php foreach($data['grades'] as $grupo) : ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><?php echo htmlspecialchars($grupo->subject_name); ?></h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Grupo:</strong> <?php echo htmlspecialchars($grupo->grupo); ?><br>
                            <strong>Docente:</strong> <?php echo htmlspecialchars($grupo->teacher_name); ?><br>
                            <strong>Periodo:</strong> <?php echo htmlspecialchars($grupo->periodo); ?><br>
                            <strong>Turno:</strong> <?php echo htmlspecialchars($grupo->turno); ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="<?php echo URLROOT; ?>/students/report/<?php echo $grupo->id; ?>" class="btn btn-outline-primary btn-block">Ver Reporte</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if(empty($data['grades'])) : ?>
            <div class="col-12">
                <div class="alert alert-info">No estás inscrito en ningún grupo actualmente.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>