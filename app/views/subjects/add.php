<?php require APPROOT . '/views/inc/header.php'; ?>
<h2>Agregar Asignatura</h2>
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <form action="<?php echo URLROOT; ?>/subjects/add" method="POST">
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="name" class="form-control form-control-lg" id="name">
                </div>
                <div class="form-group">
                    <label>Materia:</label>
                    <input type="text" name="subject" class="form-control form-control-lg" id="subject">
                </div>
                <div class="form-group">
                    <label>Profesores:</label>
                    <?php foreach ($data['teachers'] as $teacher) : ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="teachers[]" value="<?php echo $teacher->id; ?>">
                            <label class="form-check-label"><?php echo $teacher->name . ' ' . $teacher->lastName1 . ' ' . $teacher->lastName2; ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="submit" class="btn btn-success btn-block" value="Add">
            </form>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>